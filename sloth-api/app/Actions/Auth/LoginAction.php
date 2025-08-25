<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\LoginDTO;
use App\Models\Cart\Cart;
use App\Models\User;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LoginAction
{
    /**
     * @throws \Exception
     */
    public function login(LoginDTO $data): array
    {
        $user = User::query()->where('email', $data->email)->first();

        if (!$user || !Hash::check($data->password, $user->password)) {
            throw new \RuntimeException('Email or password is incorrect');
        }

        $token = $user->createToken('auth_token')->plainTextToken;
        $guestToken = $data->guestToken ?? request()->cookie('guest_token');

        if ($guestToken) {
            DB::beginTransaction();
            try {
                $guestCart = Cart::query()->where('guest_token', $guestToken)->first();

                if ($guestCart) {
                    $userCart = Cart::query()->firstOrCreate(['user_id' => $user->id]);

                    foreach ($guestCart->items as $item) {
                        $userCart->items()->updateOrCreate(
                            ['product_id' => $item->product_id],
                            ['quantity' => DB::raw("quantity + {$item->quantity}")]
                        );
                    }

                    $guestCart->delete();
                }
                DB::commit();
            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }
            Cookie::queue(Cookie::forget('guest_token'));
        }

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

}
