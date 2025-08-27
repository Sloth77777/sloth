<?php

declare(strict_types=1);

namespace App\Parts\API\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $total
 * @property mixed $product_id
 * @property mixed $product_slug
 * @property mixed $product_title
 * @property mixed $quantity
 * @property mixed $unit_price
 */
class OrderItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->product_id,
            'product_title' => $this->product_title,
            'product_slug' => $this->product_slug,
            'quantity' => $this->quantity,
            'unit_price' => $this->unit_price,
            'total' => $this->total,
        ];
    }
}
