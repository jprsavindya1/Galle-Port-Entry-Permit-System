<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class BlacklistHistory extends Model
{
    protected $fillable = [
        'nic', 'full_name', 'company_name', 'vehicle_number', 'reason',
        'action', 'blacklist_id', 'admin_id', 'admin_name', 'admin_role', 'reinstated_by', 'reinstated_on', 'status'
    ];
}
