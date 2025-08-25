<?php

declare(strict_types=1);

namespace App\Parts\API\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Models\Cart\Cart;
use App\Parts\API\Resources\CartItemResource;
use App\Services\Cart\CartResolver;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(private readonly CartResolver $cartResolver)
    {}

    public function index(Request $request): JsonResponse
    {
        $cart = $this->cartResolver->resolve($request)->load('items.product');

        return response()->json([
            'items' => CartItemResource::collection($cart->items)->resolve(),
        ]);
    }

    public function clear(Request $request): JsonResponse
    {
        $this->cartResolver->resolve($request)->items()->delete();
        return response()->json(['success' => true]);
    }

    public function getCart(Request $request): Cart
    {
        return $this->cartResolver->resolve($request);
    }

}
