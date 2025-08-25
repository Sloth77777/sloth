<?php

namespace Database\Factories\Product;

use App\Models\Category\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Storage;
use Random\RandomException;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product\Product>
 */
class ProductFactory extends Factory
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
        $price = $this->faker->randomFloat(2, 1, 999_999.99);
        $priceLatest = (random_int(1, 3) === 1)
            ? $this->faker->randomFloat(2, 1, 999_999.99)
            : null;
        $maxDiscount = max(0.0, round($price * 0.7, 2));
        $discount = $this->faker->randomFloat(2, 0, $maxDiscount);

        return [
            'title' => $this->faker->sentence(),
            'slug' => $this->faker->slug(),
            'description' => $this->faker->paragraph(),
            'preview_image' => $images ? collect($images)->random() : null,
            'price' => $price,
            'price_latest' => $priceLatest,
            'discount' => $discount,
            'category_id' => Category::query()->inRandomOrder()->value('id'),
        ];
    }
}
