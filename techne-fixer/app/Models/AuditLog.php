<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'action', 'model',
        'model_id', 'old_values', 'new_values',
        'ip_address', 'user_agent'
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function log(string $action, $model = null, array $oldValues = [], array $newValues = [])
    {
        static::create([
            'user_id'    => Auth::id(),
            'action'     => $action,
            'model'      => $model ? class_basename($model) : null,
            'model_id'   => $model?->id,
            'old_values' => $oldValues ?: null,
            'new_values' => $newValues ?: null,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]);
    }
}