<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use Illuminate\Auth\AuthenticationException;

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
