<?php

declare(strict_types=1);

namespace App\Parts\API\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Parts\API\Requests\FilterAndSearchRequest;
use App\Parts\API\Requests\Product\StoreProductRequest;
use App\Parts\API\Requests\Product\UpdateProductRequest;
use App\Parts\API\Resources\Product\ProductResource;
use App\Parts\API\Services\Product\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService)
    {
    }

    /**
     * Display a listing of the resource.
     */
    public function index(FilterAndSearchRequest $request): JsonResponse|AnonymousResourceCollection
    {
        $this->productService->index($request->validated());

        if ($this->productService->hasErrors()) {
            return $this->getErrorsResponse($this->productService->getErrors());
        }

        return ProductResource::collection($this->productService->getData());
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $this->productService->store($request->toDTO());

        if ($this->productService->hasErrors()) {
            return $this->getErrorsResponse($this->productService->getErrors());
        }

        return $this->getSuccessResponse((array)'Product created.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): ProductResource
    {
        $words = explode(' ', $product->title);
        $related = Product::query()->where('id', '!=', $product->id)
            ->where(function($query) use ($words) {
                foreach ($words as $word) {
                    $query->orWhere('title', 'like', "%{$word}%");
                }
            })
            ->take(4)
            ->get();


        return (new ProductResource($product))->additional([
            'related_products' => ProductResource::collection($related),
        ]);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $productUpdate = $this->productService->update($request->toDTO(), $product);

        if ($productUpdate->hasErrors()) {
            $this->getErrorsResponse($productUpdate->getErrors());
        }

        return $this->getSuccessResponse((array)'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): JsonResponse
    {
        $this->productService->destroy($product);

        if ($this->productService->hasErrors()) {
            return $this->getErrorsResponse($this->productService->getErrors());
        }

        return $this->getSuccessResponse((array)'Product deleted');
    }
}
