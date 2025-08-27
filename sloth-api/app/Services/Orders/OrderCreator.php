<?php

declare(strict_types=1);

namespace App\Services\Orders;

use App\Events\OrderCreated;
use App\Models\Order\Order;
use App\Models\Order\OrderItem;
use App\Models\Product\Product;
use App\Services\Cart\CartResolver;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

readonly class OrderCreator
{
    public function __construct(
        private CartResolver $cartResolver,
    )
    {
    }

    public function createFromCart(array $payload, ?int $userId = null): Order
    {
        $request = request();

        $cart = $this->cartResolver->resolve($request)->load('items.product');

        if ($cart->items->isEmpty() && empty($payload['items'])) {
            throw new \RuntimeException('Корзина пуста');
        }

        $resolvedUserId = $userId
            ?? Auth::id()
            ?? Auth::guard('sanctum')->id();

        $guestToken = $this->cartResolver->currentGuestToken($request);

        return DB::transaction(static function () use ($cart, $payload, $resolvedUserId, $guestToken) {
            $order = new Order();
            $order->user_id = $resolvedUserId;
            $order->guest_token = $guestToken;
            $order->status = Order::STATUS_PENDING;
            $order->currency = 'UA';

            $order->customer_name = $payload['customer_name'] ?? null;
            $order->customer_email = $payload['customer_email'] ?? null;
            $order->customer_phone = $payload['customer_phone'] ?? null;
            $order->shipping_address = $payload['shipping_address'] ?? null;
            $order->meta = $payload['meta'] ?? null;

            $order->subtotal = 0;
            $order->discount = 0;
            $order->total = 0;
            $order->save();

            $subtotal = 0;
            if ($cart->items->isNotEmpty()) {
                $items = $cart->items->map(static function ($item) {
                    return (object)[
                        'product' => $item->product,
                        'quantity' => (int)($item->quantity ?? 1),
                    ];
                });
            } else {
                $payloadItems = array_filter(
                    is_array($payload['items'] ?? null) ? $payload['items'] : [],
                    static fn($i) => isset($i['product_id'])
                );
                $productIds = array_values(array_unique(array_map(static fn($i) => (int)$i['product_id'], $payloadItems)));
                $products = Product::query()->whereIn('id', $productIds)->get()->keyBy('id');

                $items = collect($payloadItems)->map(static function ($i) use ($products) {
                    $product = $products->get((int)$i['product_id']);
                    $qty = (int)($i['quantity'] ?? 1);
                    return (object)[
                        'product' => $product,
                        'quantity' => max(1, $qty),
                    ];
                });
            }

            foreach ($items as $item) {
                $product = $item->product;
                $qty = (int)($item->quantity ?? 1);
                $price = (int)($product->price_latest ?? $product->price ?? 0);

                if (!$product) {
                    continue;
                }

                $lineTotal = $qty * $price;
                $subtotal += $lineTotal;

                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $product?->id,
                    'product_title' => $product?->title,
                    'product_slug' => $product?->slug,
                    'quantity' => $qty,
                    'unit_price' => $price,
                    'total' => $lineTotal,
                    'snapshot' => $product ? [
                        'id' => $product->id,
                        'title' => $product->title,
                        'slug' => $product->slug,
                        'price' => $product->price ?? null,
                        'price_latest' => $product->price_latest ?? null,
                        'discount' => $product->discount ?? null,
                    ] : null,
                ]);
            }

            $order->subtotal = $subtotal;
            $order->discount = 0;
            $order->total = max(0, $order->subtotal - $order->discount);
            $order->save();

            $cart->items()->delete();

            DB::afterCommit(static function () use ($order) {
                event(new OrderCreated($order->id));
            });

            return $order->load('items');
        });
    }

}
