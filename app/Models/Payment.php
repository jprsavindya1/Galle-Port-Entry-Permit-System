<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
   
    protected $table = 'payments';

    // Fillable fields 
    protected $fillable = [
        'submission_id',
         'invoice_id',
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

    
    protected $casts = [
        'payment_date' => 'datetime',
        'paid_at' => 'datetime',
        'rate_total' => 'float',
        'nbt_total' => 'float',
        'vat_total' => 'float',
        'amount_total' => 'float',
    ];
}
