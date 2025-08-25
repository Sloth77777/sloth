<?php

namespace App\DTOs;

use ReflectionClass;

abstract class BaseDTO
{
    /**
     * @throws \ReflectionException
     */
    public static function fromArray(array $data): static
    {
        return (new ReflectionClass(static::class))->newInstanceArgs(array_values($data));
    }
}
