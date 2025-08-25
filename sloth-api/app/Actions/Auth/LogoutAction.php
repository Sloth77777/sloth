<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\Cart\Cart;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class LogoutAction
{
    /**
     * @throws \Exception
     */
    public function logout(): void
    {
        $user = auth()->user();

        if (!$user) {
            throw new AuthenticationException('Unauthenticated');
        }

        $currentToken = $user->currentAccessToken();

        if ($currentToken) {
            $currentToken->delete();
        } else {
            $user->tokens()->delete();
        }
    }

}
