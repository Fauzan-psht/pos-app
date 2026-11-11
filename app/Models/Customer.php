<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\LogsActivity; // <-- Tambahkan ini

class Customer extends Model
{
    use LogsActivity; // <-- Tambahkan ini

    protected $fillable = ['name', 'phone', 'email', 'address'];

    public function sales(): HasMany
    {
        return $this->hasMany(Sale::class);
    }
}