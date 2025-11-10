<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $fillable = [
        'bill_id',
        'user_id',
        'amount',
        'reason',
        'refund_date',
        'processed_by',
    ];

    protected function casts(): array
    {
        return [
            'refund_date' => 'date',
        ];
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }
}
