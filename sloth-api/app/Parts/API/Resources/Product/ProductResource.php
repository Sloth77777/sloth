<?php

declare(strict_types=1);

namespace App\Parts\API\Resources\Product;

use App\Parts\API\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $discount
 * @property mixed $description
 * @property mixed $slug
 * @property mixed $title
 * @property mixed $id
 * @property mixed $imageUrl
 * @property mixed $price
 * @property mixed $price_latest
 * @property mixed $images
 * @property mixed $category
 */
class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'description' => $this->description,
            'discount' => $this->discount,
            'preview_image' => $this->imageUrl,
            'price' => $this->price,
            'price_latest' => $this->price_latest,
            'images' => $this->images->flatMap(fn($img) => $img->image_urls),
            'category' => $this->category ? CategoryResource::make($this->category) : null,
        ];
    }
}
