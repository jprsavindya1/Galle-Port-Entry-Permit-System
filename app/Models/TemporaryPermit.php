<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TemporaryPermit extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'permit_id',
        'application_number',
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
        'doc_nic',
        'doc_passport',
        'doc_driving_licence',
        'rate',
        'ssl',
        'vat',
        'total',
        'submission_id',
        'status',
        'cancel_reason',
        'is_printed',
        'printed_at',
        'printed_by',
    ];

    protected $casts = [
        'from_date' => 'date',
        'to_date' => 'date',
        'doc_nic' => 'boolean',
        'doc_passport' => 'boolean',
        'doc_driving_licence' => 'boolean',
        'rate' => 'decimal:2',
        'ssl' => 'decimal:2',
        'vat' => 'decimal:2',
        'total' => 'decimal:2',
        'is_printed' => 'boolean',
        'printed_at' => 'datetime',
    ];

    public function payment()
    {
        return $this->hasOne(\App\Models\Payment::class, 'submission_id', 'submission_id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function cancelledPermitTrashed()
    {
        return $this->hasOne(\App\Models\CancelledPermit::class, 'permit_id', 'permit_id')
                    ->onlyTrashed();
    }

    public function printedBy()
    {
        return $this->belongsTo(\App\Models\User::class, 'printed_by');
    }

    public function getTypeAttribute()
    {
        return 'TP';
    }
}
