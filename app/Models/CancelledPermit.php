<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CancelledPermit extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'permit_id',
        'submission_id',
        'invoice_id',
        'type',
        'id_type',
        'id_number',
        'full_name',
        'initials',
        'designation',
        'company_name',
        'company_address',
        'residence_address',
        'vehicle_type',
        'vehicle_number',
        'revenue_license_number',
        'insurance_number',
        'owner_name',
        'owner_address',
        'from_date',
        'to_date',
        'police_issue_date',
        'police_expire_date',
        'pass_type',
        'issue_type',
        'reason',
        'remarks',
        'id_document',
        'cancel_reason',
        'cancelled_at',
        'cancelled_by',
        'blacklist_status',
        'blacklist_reason',
        'activity_log', // keep activity logs in the same model
    ];

    protected $dates = ['cancelled_at', 'deleted_at'];

    /**
     * Append a log entry (action, user, timestamp)
     */
   public function addLog($action, $userId, $details = [])
{
    $user = \App\Models\User::find($userId);

    $log = [
        'action'    => $action,
        'user_id'   => $userId,
        'user_name' => $user->name ?? 'N/A',
        'role'      => $user->role ?? 'N/A',
        'timestamp' => now()->toDateTimeString(),
        'details'   => $details,
    ];

    $logs = $this->activity_log ? json_decode($this->activity_log, true) : [];
    $logs[] = $log;

    $this->activity_log = json_encode($logs);
    $this->save();
}
public function payment()
{
    return $this->hasOne(\App\Models\Payment::class, 'submission_id', 'submission_id');
}
    
}
