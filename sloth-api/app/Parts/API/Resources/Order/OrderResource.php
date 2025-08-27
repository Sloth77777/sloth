<?php

declare(strict_types=1);

namespace App\Parts\API\Resources\Order;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $status
 * @property mixed $currency
 * @property mixed $subtotal
 * @property mixed $total
 * @property mixed $discount
 * @property mixed $customer_name
 * @property mixed $customer_email
 * @property mixed $customer_phone
 * @property mixed $shipping_address
 * @property mixed $created_at
 * @property mixed $items
 */
class OrderResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'currency' => $this->currency,
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'total' => $this->total,
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'customer_phone' => $this->customer_phone,
            'shipping_address' => $this->shipping_address,
            'created_at' => $this->created_at,
            'items' => $this->when(
                $this->relationLoaded('items'),
                OrderItemResource::collection($this->items)
            ),
        ];
    }
}
