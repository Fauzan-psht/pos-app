<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ActivityLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'table_name', 'record_id', 'old_data', 'new_data'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}