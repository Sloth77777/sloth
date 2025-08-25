<?php

declare(strict_types=1);

namespace App\Parts\API\Controllers\Category;

use App\Http\Controllers\Controller;
use App\Models\Category\Category;
use App\Parts\API\Requests\Category\StoreCategoryRequest;
use App\Parts\API\Requests\Category\UpdateCategoryRequest;
use App\Parts\API\Resources\CategoryResource;
use App\Parts\API\Services\Category\CategoryService;
use App\Traits\ErrorsTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $categoryService)
    {
    }

    /**
     * Display a listing of the categories.
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::query()->paginate(10);

        return CategoryResource::collection($categories);
    }

    /**
     * Display the specified category.
     *
     * @param Category $category
     * @return CategoryResource
     */
    public function show(Category $category): CategoryResource
    {
        return new CategoryResource($category);
    }

    /**
     * Store a newly created category in storage.
     *
     * @param StoreCategoryRequest $request
     * @return JsonResponse
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->store($request->validated());

        if ($category->hasErrors()) {
            return response()->json(['message' => 'Error creating category', 'errors' => $category->getErrors()], 422);
        }

        return response()->json(['message' => 'Category created successfully', 'data' => new CategoryResource($category)], 201);
    }

    /**
     * Update the specified category in storage.
     *
     * @param UpdateCategoryRequest $request
     * @param Category $category
     * @return JsonResponse
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $categoryUpdate = $this->categoryService->update($request->validated(), $category);

        if ($categoryUpdate->hasErrors()) {
            return response()->json(['message' => 'Error updating category', 'errors' => $categoryUpdate->getErrors()], 422);
        }

        return response()->json(['message' => 'Category updated successfully', 'data' => new CategoryResource($categoryUpdate)]);
    }

    /**
     * Remove the specified category from storage.
     *
     * @param Category $category
     * @return JsonResponse
     */
    public function destroy(Category $category): JsonResponse
    {
        try {
            $category->delete();

            return response()->json([
                'message' => 'Category deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error deleting category',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
