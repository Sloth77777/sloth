<?php

namespace App\Models\Category;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Category extends Model
{
    /** @use HasFactory<\Database\Factories\Category\CategoryFactory> */
    use HasFactory;

    protected $table = 'categories';
    protected $fillable = [
        'title',
        'parent_id',
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(__CLASS__, 'parent_id');
    }
    public function getFullHierarchyAttribute(): string
    {
        if ($this->parent) {
            return $this->parent->full_hierarchy . ' -> ' . $this->title;
        }

        return $this->title;
    }

}
