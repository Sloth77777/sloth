<?php

declare(strict_types=1);

namespace App\Services\Cart;

use App\Models\Cart\Cart;
use Illuminate\Http\Request;

class CartResolver
{
    public function resolve(Request $request): Cart
    {
        if ($request->user()) {
            $userCart = Cart::query()->firstOrCreate(['user_id' => $request->user()->id]);

            $guestToken = $this->getGuestTokenFromRequest($request);
            if (is_string($guestToken) && $guestToken !== '') {
                $guestCart = Cart::query()
                    ->where('guest_token', $guestToken)
                    ->with('items')
                    ->first();

                if ($guestCart && $guestCart->id !== $userCart->id) {
                    foreach ($guestCart->items as $gi) {
                        $existing = $userCart->items()
                            ->where('product_id', $gi->product_id)
                            ->first();

                        if ($existing) {
                            $existing->quantity = (int)$existing->quantity + (int)($gi->quantity ?? 1);
                            $existing->save();
                        } else {
                            $userCart->items()->create([
                                'product_id' => $gi->product_id,
                                'quantity' => (int)($gi->quantity ?? 1),
                            ]);
                        }
                    }

                    $guestCart->items()->delete();
                    $guestCart->delete();
                }
            }

            return $userCart;
        }

        $guestToken = $this->getGuestTokenFromRequest($request);
        if (!$guestToken) {

            throw new \RuntimeException('Guest token is required');
        }

        return Cart::query()->firstOrCreate(['guest_token' => $guestToken]);
    }

    public function currentGuestToken(Request $request): ?string
    {
        return $request->user() ? null : $this->getGuestTokenFromRequest($request);
    }

    private function getGuestTokenFromRequest(Request $request): ?string
    {
        $header = $request->header('X-Guest-Token');
        if (is_string($header) && $header !== '') {
            return $header;
        }

        $query = $request->query('guest_token');
        if (is_string($query) && $query !== '') {
            return $query;
        }

        return null;
    }
}
