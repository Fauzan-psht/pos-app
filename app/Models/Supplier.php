<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\LogsActivity; // <-- Tambahkan ini

class Supplier extends Model
{
    use LogsActivity; // <-- Tambahkan ini

    protected $fillable = ['name', 'contact_person', 'phone', 'address'];

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }
}