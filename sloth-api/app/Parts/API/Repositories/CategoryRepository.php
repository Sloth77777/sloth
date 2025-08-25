<?php

declare(strict_types=1);

namespace App\Parts\API\Repositories;

use App\Models\Category\Category;
use Illuminate\Database\Eloquent\Model;

class CategoryRepository
{

    public function store(array $data): Model|Category
    {
        return Category::query()->create($data);
    }

    public function update(array $array, Category $category): bool
    {
        return $category->update($array);
    }


}
