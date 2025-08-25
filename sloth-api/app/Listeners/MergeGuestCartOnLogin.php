<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\Cart\Cart;
use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;

readonly class MergeGuestCartOnLogin implements ShouldQueue
{
    public function __construct(private Request $request)
    {
    }

    public function handle(Login $event): void
    {
        $guestToken = $this->request->cookie('guest_token');
        if (!$guestToken) {
            return;
        }

        $userId = $event->user->id ?? null;
        if (!$userId) {
            return;
        }

        DB::transaction(function () use ($guestToken, $userId) {
            $guestCart = Cart::query()
                ->with('items')
                ->where('guest_token', $guestToken)
                ->first();

            if (!$guestCart || $guestCart->items->isEmpty()) {
                Cookie::queue(Cookie::forget('guest_token'));
                return;
            }

            $userCart = Cart::query()->firstOrCreate(['user_id' => $userId]);
            $userCart->load('items');

            $userItemsByProduct = $userCart->items->keyBy('product_id');

            foreach ($guestCart->items as $gItem) {
                $productId = $gItem->product_id;
                $qty = (int) ($gItem->quantity ?? 1);

                if ($productId === null) {
                    continue;
                }

                $uItem = $userItemsByProduct->get($productId);
                if ($uItem) {
                    $uItem->quantity = (int) $uItem->quantity + $qty;
                    $uItem->save();
                } else {
                    $userCart->items()->create([
                        'product_id' => $productId,
                        'quantity'   => $qty,
                    ]);
                }
            }

            $guestCart->items()->delete();
            $guestCart->delete();

            Cookie::queue(Cookie::forget('guest_token'));
        });
    }
}
