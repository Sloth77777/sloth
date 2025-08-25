<?php

declare(strict_types=1);

namespace App\Parts\API\Filters;

use Illuminate\Database\Eloquent\Builder;

interface FilterInterface
{
    public function apply(Builder $builder);
}
