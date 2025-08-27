<?php

namespace Tests\Parts\API\Controllers;

use App\Models\Cart\CartItem;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('updates quantity of an existing cart item successfully', function () {
    $cartItem = CartItem::factory()->create(['quantity' => 1]);

    $payload = ['quantity' => 3];

    $response = $this->putJson(route('cart-item.update', $cartItem->id), $payload);

    $response->assertOk()
        ->assertJsonPath('success', true)
        ->assertJsonPath('item.id', $cartItem->id)
        ->assertJsonPath('item.quantity', $payload['quantity']);
});

test('throws validation error for invalid request payload', function () {
    $cartItem = CartItem::factory()->create(['quantity' => 1]);

    $payload = ['quantity' => 'invalid'];

    $response = $this->putJson(route('cart-item.update', $cartItem->id), $payload);

    $response->assertStatus(422)
        ->assertJsonStructure(['message', 'errors' => ['quantity']]);
});

test('returns error when updating a non-existent cart item', function () {
    $payload = ['quantity' => 2];

    $response = $this->putJson(route('cart-item.update', 999), $payload);

    $response->assertNotFound();
});

test('handles exception gracefully when update process fails', function () {
    $cartItem = CartItem::factory()->make(['id' => 1, 'quantity' => 1]);
    $cartItem->saveQuietly();

    $mockCartItem = \Mockery::mock($cartItem)->shouldReceive('update')->andThrow(new \Exception('Update failed'))->getMock();
    $this->app->instance(CartItem::class, $mockCartItem);

    $payload = ['quantity' => 2];

    $response = $this->putJson(route('cart-item.update', $cartItem->id), $payload);

    $response->assertStatus(500)
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Error while updating item to cart');
});
