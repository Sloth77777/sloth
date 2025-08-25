<?php

declare(strict_types=1);

namespace App\Parts\API\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->id,
            'status' => $this->resource->status,
            'currency' => $this->resource->currency,
            'subtotal' => $this->resource->subtotal,
            'discount' => $this->resource->discount,
            'total' => $this->resource->total,
            'customer_name' => $this->resource->customer_name,
            'customer_email' => $this->resource->customer_email,
            'customer_phone' => $this->resource->customer_phone,
            'shipping_address' => $this->resource->shipping_address,
            'created_at' => $this->resource->created_at,
            'items' => $this->when(
                $this->resource->relationLoaded('items'),
                OrderItemResource::collection($this->items)
            ),
        ];
    }
}
