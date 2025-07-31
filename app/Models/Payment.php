<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    // Table name (optional if it's not the plural of 'Payment')
    protected $table = 'payments';

    // Fillable fields for mass assignment
    protected $fillable = [
        'submission_id',
        'permit_type',
        'entry_count',
        'rate_total',
        'nbt_total',
        'vat_total',
        'amount_total',
        'status',
        'payment_date',
        'paid_at',
    ];

    // Optional: if you want to cast some columns
    protected $casts = [
        'payment_date' => 'datetime',
        'paid_at' => 'datetime',
        'rate_total' => 'float',
        'nbt_total' => 'float',
        'vat_total' => 'float',
        'amount_total' => 'float',
    ];
}
