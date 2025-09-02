<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'user_name',
        'role',
        'action',
        'model',
        'model_id',
        'details',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'details' => 'array', // JSON column will be cast to array automatically
    ];

    // Optional: helper to log activity
    public static function log($action, $model = null, $details = [], $user = null)
    {
        $user = $user ?? auth()->user();

        return self::create([
            'user_id' => $user->id ?? null,
            'user_name' => $user->name ?? null,
            'role' => $user->role ?? null,
            'action' => $action,
            'model' => $model ? get_class($model) : null,
            'model_id' => $model->id ?? null,
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => substr(request()->userAgent(), 0, 255),
        ]);
    }
}
