<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyPermit extends Model
{    /*
     ***********  monthly fill data *********   
    */
    protected $fillable = [
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
        'police_report_issue_date',
        'police_report_expire_date',
        'submission_id',
    ];
}
