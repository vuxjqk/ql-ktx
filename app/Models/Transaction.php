<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    protected $fillable = [
        'bill_id',
        'vnp_transaction_no',
        'vnp_amount',
        'vnp_bank_code',
        'vnp_bank_tran_no',
        'vnp_card_type',
        'vnp_order_info',
        'vnp_response_code',
        'vnp_transaction_status',
        'vnp_pay_date',
        'vnp_txn_ref',
        'vnp_secure_hash',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }
}
