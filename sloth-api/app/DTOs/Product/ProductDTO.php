<?php

declare(strict_types=1);

namespace App\DTOs\Product;

use App\DTOs\BaseDTO;

class ProductDTO extends BaseDTO
{
    public function __construct(
        public string $title,
        public string $slug,
        public string $description,
        public string $previewImage,
        public float $price,
        public float $priceLatest,
        public float $discount,
        public float $main_category_id,
        public array $images,
    )
    {
    }
}
