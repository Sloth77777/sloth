<?php

declare(strict_types=1);

namespace App\Parts\API\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $product_id
 * @property mixed $id
 * @property mixed $quantity
 * @property mixed $product
 */
class CartItemResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'quantity' => $this->quantity,
            'title' => $this->product->title,
            'price' => $this->product->price,
            'preview_image' => $this->product->imageUrl,
        ];
    }
}
