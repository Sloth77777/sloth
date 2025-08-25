<?php

namespace App\Models\Product;

use App\Models\Category\Category;
use App\Traits\Filterable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    /** @use HasFactory<\Database\Factories\Product\ProductFactory> */
    use HasFactory,Filterable;
    protected $table = 'products';
    protected $fillable = [
        'title',
        'slug',
        'description',
        'price',
        'discount',
        'category_id',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageUrlAttribute(): string
    {
        return url('storage/' . $this->preview_image);
    }
    public function images(): HasMany
    {
        return $this->hasMany(ProductImages::class);
    }
}
