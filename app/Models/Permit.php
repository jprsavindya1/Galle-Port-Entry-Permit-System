<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permit extends Model
{
    protected $fillable = [
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
];



}
