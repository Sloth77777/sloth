<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

use App\DTOs\BaseDTO;

final  class RegisterDTO extends BaseDTO
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password,
    )
    {
    }
}
