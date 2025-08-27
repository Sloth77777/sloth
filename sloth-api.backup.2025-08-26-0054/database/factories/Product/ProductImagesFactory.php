<?php

namespace Database\Factories\Product;

use App\Models\Product\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Random\RandomException;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product\ProductImages>
 */
class ProductImagesFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     * @throws RandomException
     */
    public function definition(): array
    {
        $images = Storage::disk('public')->files('products');
        $imagesArray = $images ? collect($images)
            ->shuffle()
            ->take(random_int(1, 3))
            ->map(fn($file) => $file)
            ->toArray()
            : [];

        return [
            'product_id' => Product::all()->random()->id,
            'images' => $imagesArray,
        ];
    }
}
