<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogHelper
{
    public static function logActivity(string $action, $model = null, $modelId = null, array $details = [])
    {
        $user = Auth::user();

        if ($model && is_object($model)) {
            $modelId = $model->id;
            $model = get_class($model);
        }

        ActivityLog::create([
            'user_id'    => $user->id ?? null,
            'user_name'  => $user->name ?? null,
            'role'       => $user->role ?? null,
            'action'     => $action,
            'model'      => $model,
            'model_id'   => $modelId,
            'details'    => json_encode($details),
            'ip_address' => request()->ip(),
            'user_agent' => substr(request()->userAgent(), 0, 255),
        ]);
    }
}
