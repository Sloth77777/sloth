<?php

declare(strict_types=1);

namespace App\Parts\API\Controllers\Cart;

use App\Http\Controllers\Controller;
use App\Models\Cart\CartItem;
use App\Parts\API\Requests\Cart\CartStoreRequest;
use App\Parts\API\Requests\Cart\CartUpdateRequest;
use App\Parts\API\Resources\CartItemResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Throwable;

class CartItemController extends Controller
{
    public function __construct(protected CartController $cartController)
    {
    }

    public function store(CartStoreRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            return DB::transaction(function () use ($data, $request) {
                $cart = $this->cartController->getCart($request);

                $item = $cart->items()->updateOrCreate(
                    ['product_id' => $data['product_id']],
                    ['quantity' => 0]
                );

                $item->increment('quantity', $data['quantity']);

                return response()->json([
                    'success' => true,
                    'items' => CartItemResource::collection($cart->items()->with('product')->get()),
                ]);
            });
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Error while adding item to cart',
            ], 500);
        }
    }

    public function update(CartUpdateRequest $request, CartItem $item): JsonResponse
    {
        $data = $request->validated();

        try {
            $item->update(['quantity' => $data['quantity']]);

            return response()->json([
                'success' => true,
                'item' => new CartItemResource($item->load('product')),
            ]);
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Error while updating item to cart',
            ], 500);
        }
    }

    public function destroy(CartItem $item): JsonResponse
    {
        try {
            $item->delete();

            return response()->json(['success' => true]);
        } catch (Throwable $e) {
            report($e);

            return response()->json([
                'success' => false,
                'message' => 'Error while deleting item from cart',
            ], 500);
        }
    }
}
