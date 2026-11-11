<?php

namespace App\Traits;

use App\Models\ActivityLog;

trait LogsActivity
{
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            self::recordActivity('create', $model, null, $model->getAttributes());
        });

        static::updated(function ($model) {
            self::recordActivity('update', $model, $model->getOriginal(), $model->getChanges());
        });

        static::deleted(function ($model) {
            self::recordActivity('delete', $model, $model->getOriginal(), null);
        });
    }

    protected static function recordActivity($action, $model, $oldData, $newData)
    {
        ActivityLog::create([
            'user_id' => auth()->id(),
            'action' => $action,
            'table_name' => $model->getTable(),
            'record_id' => $model->id,
            'old_data' => $oldData ? json_encode($oldData) : null,
            'new_data' => $newData ? json_encode($newData) : null,
        ]);
    }
}