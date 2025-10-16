<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'contract_code',
        'booking_id',
        'start_date',
        'end_date',
        'monthly_fee',
        'deposit',
        'contract_file',
        'status',
    ];
}
