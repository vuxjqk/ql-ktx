<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'contract_code',
        'booking_id',
        'monthly_fee',
        'deposit',
        'contract_file',
    ];
}
