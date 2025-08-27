<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\RegisterDTO;
use App\Models\User;
use App\Traits\ErrorsTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class RegisterAction
{
    use ErrorsTrait;

    /**
     * @throws \Exception
     */
    public function register(RegisterDTO $data): array
    {
        try {
            DB::beginTransaction();

            $user = User::query()->create([
                'name' => $data->name,
                'email' => $data->email,
                'password' => Hash::make($data->password),
            ]);

            $token = $user->createToken('auth_token')->plainTextToken;

            DB::commit();

            return [
                'user' => $user,
                'token' => $token,
            ];
        } catch (\Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
