<?php

namespace App\Models\Order;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'guest_token',
        'status',
        'currency',
        'subtotal',
        'discount',
        'total',
        'customer_name',
        'customer_email',
        'customer_phone',
        'shipping_address',
        'meta',
    ];

    protected $casts = [
        'subtotal' => 'integer',
        'discount' => 'integer',
        'total' => 'integer',
        'meta' => 'array',
    ];

    public const STATUS_PENDING = 'pending';
    public const STATUS_PAID = 'paid';
    public const STATUS_CANCELLED = 'cancelled';

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
