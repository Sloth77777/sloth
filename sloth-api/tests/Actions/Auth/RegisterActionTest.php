<?php

namespace Tests\Actions\Auth;

use App\Actions\Auth\RegisterAction;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

test('it registers a user successfully and returns user and token', function () {
    // Prepare mock data
    $data = [
        'name' => 'John Doe',
        'email' => 'johndoe@example.com',
        'password' => 'password123',
    ];

    // Ensure there are no users initially
    expect(User::query()->count())->toBe(0);

    // Instantiate action and execute the register method
    $action = app(RegisterAction::class);
    $response = $action->register($data);

    // Assertions
    $user = $response['user'];
    $token = $response['token'];

    // Check user creation
    expect(User::query()->count())->toBe(1)
        ->and($user->name)->toBe($data['name'])
        ->and($user->email)->toBe($data['email'])
        ->and(Hash::check($data['password'], $user->password))->toBe(true)
        ->and($token)->not()->toBeEmpty();

    // Check token
});

test('it fails when required data is missing', function () {
    // Prepare mock data with missing fields
    $data = [
        'name' => 'John Doe',
        // 'email' is missing
        'password' => 'password123',
    ];

    // Expect exception due to invalid data
    $action = app(RegisterAction::class);

    expect(fn() => $action->register($data))->toThrow(\Illuminate\Database\QueryException::class)
        ->and(User::query()->count())->toBe(0);

    // Ensure no user was created
});

test('it fails when email is not unique', function () {
    // Create a user with duplicate email
    User::query()->create([
        'name' => 'Jane Doe',
        'email' => 'johndoe@example.com',
        'password' => Hash::make('password123'),
    ]);

    // Prepare mock data
    $data = [
        'name' => 'John Doe',
        'email' => 'johndoe@example.com', // Duplicate email
        'password' => 'password123',
    ];

    // Expect exception due to duplicate email
    $action = app(RegisterAction::class);

    expect(fn() => $action->register($data))->toThrow(\Illuminate\Database\QueryException::class)
        ->and(User::query()->count())->toBe(1);

    // Ensure no additional user was created
});
