<?php

namespace Tests\Http\Controllers;

use App\Models\Cart\Cart;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

test('it returns cart for authenticated user', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    $response = $this->get('/cart');

    $response->assertOk();
    $cart = Cart::query()->where('user_id', $user->id)->first();
    expect($cart)->not()->toBeNull();
});

test('it creates guest cart if no guest token is provided', function () {
    $response = $this->withCookie('guest_token', null)->get('/cart');

    $response->assertOk();
    $cart = Cart::query()->whereNotNull('guest_token')->first();
    expect($cart)->not()->toBeNull()
        ->and(Cookie::getQueuedCookies())->toHaveKey('guest_token');
});

test('it returns guest cart if guest token exists', function () {
    $guestToken = (string)Str::uuid();
    Cart::factory()->create(['guest_token' => $guestToken]);

    $response = $this->withCookie('guest_token', $guestToken)->get('/cart');

    $response->assertOk();
    $cart = Cart::query()->where('guest_token', $guestToken)->first();
    expect($cart)->not()->toBeNull();
});

test('it prioritizes authenticated user cart over guest token', function () {
    $guestToken = (string)Str::uuid();
    $user = User::factory()->create();
    $this->actingAs($user)->withCookie('guest_token', $guestToken);

    $response = $this->get('/cart');

    $response->assertOk();
    $cart = Cart::query()->where('user_id', $user->id)->first();
    expect($cart)->not()->toBeNull();
});
