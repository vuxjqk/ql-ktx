<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'bill_id',
        'transaction_no',
        'amount',
        'bank_code',
        'bank_tran_no',
        'card_type',
        'order_info',
        'response_code',
        'transaction_status',
        'pay_date',
        'txn_ref',
        'secure_hash',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
