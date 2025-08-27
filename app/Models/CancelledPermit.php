<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CancelledPermit extends Model
{
    use HasFactory;

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
    ];
}
