<?php

declare(strict_types=1);

namespace App\Parts\API\Controllers\Auth;

use App\Actions\Auth\LoginAction;
use App\Actions\Auth\LogoutAction;
use App\Actions\Auth\RegisterAction;
use App\Http\Controllers\Controller;
use App\Parts\API\Requests\Auth\LoginRequest;
use App\Parts\API\Requests\Auth\RegisterRequest;
use App\Traits\ErrorsTrait;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    use ErrorsTrait;

    public function __construct(protected LoginAction $loginAction, protected RegisterAction $registerAction, protected LogoutAction $logoutAction)
    {

    }

    /**
     * @throws \Exception
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->loginAction->login($request->toDto());

        return $this->getSuccessResponse([
            'token' => $result['token'],
            'user' => $result['user'],
        ]);

    }

    /**
     * @throws \Exception
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        $result = $this->registerAction->register($request->toDto());

        return $this->getSuccessResponse([
            'message' => 'Registration successful',
            'token' => $result['token'],
            'user' => $result['user'],
        ]);
    }

    public function logout(): JsonResponse
    {
        $this->logoutAction->logout();

        return $this->getSuccessResponse([
            'message' => 'Logged out successfully'
        ]);
    }
}
