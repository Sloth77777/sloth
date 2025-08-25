<?php

namespace App\Parts\API\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->resource->product_id,
            'product_title' => $this->resource->product_title,
            'product_slug' => $this->resource->product_slug,
            'quantity' => $this->resource->quantity,
            'unit_price' => $this->resource->unit_price,
            'total' => $this->resource->total,
        ];
    }
}
