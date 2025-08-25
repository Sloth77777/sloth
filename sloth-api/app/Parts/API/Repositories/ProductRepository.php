<?php

declare(strict_types=1);

namespace App\Parts\API\Repositories;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Model;

class ProductRepository
{
    public function store(array $data): Model|Product
    {
        return Product::query()->create($data);
    }

    public function update(array $array, Product $product): void
    {
        $product->update($array);
    }
}
