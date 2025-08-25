<?php

namespace Tests\Parts\API\Controllers;

use App\Models\Order\Order;
use App\Parts\API\Requests\Order\StoreOrderRequest;
use App\Services\Cart\CartResolver;
use App\Services\Orders\OrderCreator;
use Illuminate\Support\Facades\App;
use function Pest\Laravel\postJson;

test('it creates an order successfully', function () {
    $requestData = ['some_key' => 'some_value'];
    $order = Order::factory()->make();

    $mockCartResolver = $this->mock(CartResolver::class);
    $mockOrderCreator = $this->mock(OrderCreator::class);

    $mockOrderCreator->shouldReceive('createFromCart')
        ->withArgs(function ($request, $payload) use ($requestData) {
            expect($request)->toBeInstanceOf(StoreOrderRequest::class)
                ->and($payload)->toBe($requestData);
            return true;
        })
        ->andReturn($order);

    App::instance(CartResolver::class, $mockCartResolver);
    App::instance(OrderCreator::class, $mockOrderCreator);

    postJson('/orders', $requestData)
        ->assertCreated()
        ->assertJsonFragment($order->toArray());
});

test('it returns an error when a RuntimeException is thrown', function () {
    $requestData = ['some_key' => 'some_value'];

    $mockCartResolver = $this->mock(CartResolver::class);
    $mockOrderCreator = $this->mock(OrderCreator::class);

    $mockOrderCreator->shouldReceive('createFromCart')
        ->withArgs(function ($request, $payload) use ($requestData) {
            expect($request)->toBeInstanceOf(StoreOrderRequest::class)
                ->and($payload)->toBe($requestData);
            return true;
        })
        ->andThrow(new \RuntimeException('Error creating order'));

    App::instance(CartResolver::class, $mockCartResolver);
    App::instance(OrderCreator::class, $mockOrderCreator);

    postJson('/orders', $requestData)
        ->assertStatus(422)
        ->assertJsonFragment(['message' => 'Error creating order']);
});
