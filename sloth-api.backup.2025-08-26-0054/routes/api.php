<?php

use App\Parts\API\Controllers\Auth\AuthController;
use App\Parts\API\Controllers\Cart\CartController;
use App\Parts\API\Controllers\Cart\CartItemController;
use App\Parts\API\Controllers\Order\OrderController;
use App\Parts\API\Controllers\Product\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
    });
});

Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index'])->name('products.index');
    Route::get('/{product}', [ProductController::class, 'show'])->name('products.show');
    Route::post('/', [ProductController::class, 'store'])->name('products.store');
    Route::put('/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
});

Route::prefix('cart')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::delete('/', [CartController::class, 'clear']);
    Route::post('/items', [CartItemController::class, 'store']);
    Route::patch('/items/{item}', [CartItemController::class, 'update']);
    Route::delete('/items/{item}', [CartItemController::class, 'destroy']);
});

Route::middleware('auth:sanctum')->prefix('orders')->group(function () {
    Route::get('/', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/', [OrderController::class, 'store'])->name('orders.store');
});

