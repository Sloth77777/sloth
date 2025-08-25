<?php

namespace Database\Seeders;

use App\Models\Product\ProductImages;
use Illuminate\Database\Seeder;

class ProductImagesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ProductImages::factory()->count(30)->create();
    }
}
