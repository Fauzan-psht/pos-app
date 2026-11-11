<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity; // <-- 1. Import Trait

class Product extends Model
{
    use LogsActivity; // <-- 2. Panggil Trait-nya

    protected $fillable = [
        'category_id', 'sku', 'name', 'unit', 'purchase_price', 
        'selling_price', 'stock', 'min_stock', 'image', 'is_active'
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}