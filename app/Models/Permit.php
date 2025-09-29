<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permit extends Model
{
    use SoftDeletes; // <-- enables soft deletes

    protected $fillable = [
        'permit_id',
        'type',
        'vehicle_number',
        'vehicle_type',
        'entry_date',
        'entry_time',
        'id_document',
        'id_type',
        'id_number',
        'from_date',
        'to_date',
        'full_name',
        'initials',
        'designation',
        'company_name',
        'company_address',
        'residence_address',
        'pass_type',
        'issue_type',
        'reason',
        'submission_id',
        'owner_name',
        'owner_address',
        'revenue_license_number',
        'insurance_number',
        'remarks',
        'status',
        'cancel_reason',
        'police_issue_date',
        'police_expire_date',
        'rate',
        'ssl',
        'vat',
        'total',
    ];

    public function payment()
    {
        return $this->hasOne(\App\Models\Payment::class, 'submission_id', 'submission_id');
    }

    // Scope to only active permits
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Boot method to add a global scope that ignores trashed permits
     */public function cancelledPermitTrashed()
{
    return $this->hasOne(\App\Models\CancelledPermit::class, 'permit_id', 'permit_id')
                ->onlyTrashed(); // only soft-deleted cancelled permits
}
    
}
