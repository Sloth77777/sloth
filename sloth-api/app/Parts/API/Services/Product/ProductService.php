<?php

declare(strict_types=1);

namespace App\Parts\API\Services\Product;

use App\DTOs\Product\ProductDTO;
use App\Models\Product\Product;
use App\Parts\API\Filters\ProductFilter;
use App\Parts\API\Repositories\ProductRepository;
use App\Services\Service;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ProductService extends Service
{
    protected ?LengthAwarePaginator $data = null;

    public function __construct(protected ProductRepository $productRepository)
    {
    }

    public function index(array $filters = [], int $perPage = 16): static
    {
        try {
            $filter = app()->make(ProductFilter::class, [
                'queryParams' => array_filter($filters),
            ]);

            $this->data = Product::with('category')
                ->filter($filter)
                ->paginate($filters['per_page'] ?? $perPage);

        } catch (\Exception $exception) {
            $this->addError($exception->getMessage());
            $this->data = null;
        }
        return $this;
    }


    public function store(ProductDTO $toDTO): static
    {
        try {
            DB::beginTransaction();
            $product = $this->productRepository->store([
                'title' => $toDTO->title,
                'slug' => $toDTO->slug,
                'description' => $toDTO->description,
                'preview_image' => $toDTO->previewImage,
                'price' => $toDTO->price,
                'discount' => $toDTO->discount,
                'category_id' => $toDTO->main_category_id
            ]);
            if (!blank($toDTO->images)) {
                $product->images()->create([
                    'images' => $toDTO->images
                ]);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->addError($exception->getMessage());
        }
        return $this;
    }

    public function update(ProductDTO $toDTO, Product $product): static
    {
        try {
            DB::beginTransaction();
            $this->productRepository->update([
                'title' => $toDTO->title,
                'slug' => $toDTO->slug,
                'description' => $toDTO->description,
                'preview_image' => $toDTO->previewImage,
                'price' => $toDTO->price,
                'discount' => $toDTO->discount,
                'category_id' => $toDTO->main_category_id
            ], $product);

            if (!blank($toDTO->images)) {
                $product->images()->update([
                    'images' => $toDTO->images
                ]);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            $this->addError($exception->getMessage());
        }
        return $this;
    }

    public function destroy(Product $product): bool
    {
        try {
            return (bool)Product::destroy($product->id);
        } catch (\Exception $exception) {
            $this->addError($exception->getMessage());
            return false;
        }
    }

    public function getData(): ?LengthAwarePaginator
    {
        return $this->data;
    }

}
