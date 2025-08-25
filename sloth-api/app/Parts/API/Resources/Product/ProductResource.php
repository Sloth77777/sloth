<?php

namespace App\Parts\API\Resources\Product;

use App\Parts\API\Resources\CategoryResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'id' => $this->resource->id,
            'title' => $this->resource->title,
            'slug' => $this->resource->slug,
            'description' => $this->resource->description,
            'discount' => $this->resource->discount,
            'preview_image' => $this->resource->imageUrl,
            'price' => $this->resource->price,
            'price_latest' => $this->resource->price_latest,
            'images' => $this->resource->images->flatMap(fn($img) => $img->image_urls),
            'category' => $this->resource->category ? CategoryResource::make($this->resource->category) : null,
        ];
    }
}
