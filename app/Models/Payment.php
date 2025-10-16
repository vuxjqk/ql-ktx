<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'bill_id',
        'payment_type',
        'amount',
        'paid_at',
        'user_id',
    ];
}
