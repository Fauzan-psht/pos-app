<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\LogsActivity; // <-- Tambahkan ini

class Sale extends Model
{
    use LogsActivity; // <-- Tambahkan ini

    protected $fillable = [
        'invoice_number', 'user_id', 'customer_id', 'sale_date', 'subtotal', 
        'discount', 'total', 'payment_method', 'paid_amount', 'change_amount', 'status', 'note'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SaleItem::class);
    }
}