<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'bill_code',
        'user_id',
        'booking_id',
        'total_amount',
        'status',
        'due_date',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when(
                $filters['bill_code'] ?? null,
                fn($q, $bill_code) =>
                $q->where('bill_code', 'like', "%$bill_code%")
            )
            ->when(
                $filters['status'] ?? null,
                fn($q, $status) =>
                $q->where('status', $status)
            );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function booking()
    {
        return $this->belongsTo(Booking::class);
    }

    public function bill_items()
    {
        return $this->hasMany(BillItem::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
