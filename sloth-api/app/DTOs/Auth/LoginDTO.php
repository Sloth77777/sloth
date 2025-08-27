<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

use App\DTOs\BaseDTO;

final class LoginDTO extends BaseDTO
{
    public function __construct(
        public string  $email,
        public string  $password,
        public ?string $guestToken = null,
    )
    {
    }
}
