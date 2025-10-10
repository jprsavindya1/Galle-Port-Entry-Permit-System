<?php

namespace App\Helpers;

use App\Models\ActivityLog;
use App\Models\BlacklistHistory;
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

        // Log to ActivityLog
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

    /**
     * Log to BlacklistHistory
     * $action: created | updated | deleted | reinstated
     * $blacklist: Blacklist model instance
     */
public static function logBlacklistHistory(string $action, $blacklist)
{
    $user = Auth::user();

    if (!$blacklist) return;

    $data = [
        'nic'            => $blacklist->nic,
        'full_name'      => $blacklist->full_name,
        'company_name'   => $blacklist->company_name,
        'vehicle_number' => $blacklist->vehicle_number,
        'reason'         => $blacklist->reason,
        'action'         => $action,                  // original action
        'blacklist_id'   => $blacklist->id,
        'admin_id'       => $user->id ?? null,
        'admin_name'     => $user->name ?? null,
        'admin_role'     => $user->role ?? null,
        // New columns
        'status'         => $action === 'deleted' ? 'Deleted' : ($action === 'reinstated' ? 'Reinstated' : ucfirst($action)),
        'reinstated_by'  => $action === 'reinstated' ? ($user->name ?? null) : null,
        'reinstated_on'  => $action === 'reinstated' ? now() : null,
    ];

    BlacklistHistory::create($data);
}

}
