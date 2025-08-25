<?php

declare(strict_types=1);

namespace App\Parts\API\Services\Category;

use App\Models\Category\Category;
use App\Parts\API\Repositories\CategoryRepository;
use App\Traits\ErrorsTrait;

class CategoryService
{
    use ErrorsTrait;

    public function __construct(protected CategoryRepository $categoryRepository)
    {
    }

    public function store(mixed $validated): static
    {
        try {
            $this->categoryRepository->store([
                'title' => $validated['title']
            ]);
        } catch (\Exception $e) {
            $this->addError('Error creating category:' . $e->getMessage());
        }
        return $this;
    }

    public function update(mixed $validated, Category $category): static
    {
        try {
            $this->categoryRepository->update([
                'title' => $validated['title']
            ], $category);
        } catch (\Exception $e) {
            $this->addError('Error updating category:' . $e->getMessage());
        }
        return $this;
    }
}
