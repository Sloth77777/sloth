<?php

namespace Tests\Actions\Auth;

use App\Actions\Auth\LoginAction;
use App\Models\Cart\Cart;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\NewAccessToken;

test('it logs in successfully with valid credentials and no guest token', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);
    $token = 'sample-token';

    $user->shouldReceive('createToken')
        ->with('auth_token')
        ->once()
        ->andReturn(new NewAccessToken($user, $token));

    $action = new LoginAction();

    $result = $action->login([
        'email' => $user->email,
        'password' => 'password123',
    ]);

    expect($result)
        ->toHaveKey('user', $user)
        ->toHaveKey('token', $token);
});

test('it fails to login with invalid credentials', function () {
    User::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make('password123'),
    ]);

    $action = new LoginAction();

    $this->expectException(\RuntimeException::class);
    $this->expectExceptionMessage('Email or password is incorrect');

    $action->login([
        'email' => 'user@example.com',
        'password' => 'wrongpassword',
    ]);
});

test('it merges cart items when a guest token is provided', function () {
    $user = User::factory()->create([
        'password' => Hash::make('password123'),
    ]);
    $token = 'user-auth-token';

    $user->shouldReceive('createToken')
        ->with('auth_token')
        ->once()
        ->andReturn(new NewAccessToken($user, $token));

    $guestCart = Cart::factory()->create([
        'guest_token' => 'guest-token',
    ]);

    $guestCart->items()->createMany([
        ['product_id' => 1, 'quantity' => 2],
        ['product_id' => 2, 'quantity' => 3],
    ]);

    $userCart = Cart::factory()->create([
        'user_id' => $user->id,
    ]);

    $userCart->items()->create(['product_id' => 1, 'quantity' => 1]);

    Cookie::queue('guest_token', 'guest-token');

    $action = new LoginAction();

    DB::spy();

    $result = $action->login([
        'email' => $user->email,
        'password' => 'password123',
        'guest_token' => 'guest-token',
    ]);

    DB::shouldHaveReceived('beginTransaction')->once();
    DB::shouldHaveReceived('commit')->once();

    expect($result)
        ->toHaveKey('user', $user)
        ->toHaveKey('token', $token)
        ->and(Cookie::hasQueued('guest_token'))->toBeTrue()
        ->and($userCart->items()->count())->toBe(2);

});
