<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'booking_id',
        'booking_type',
        'rental_type',
        'check_in_date',
        'expected_check_out_date',
        'actual_check_out_date',
        'status',
        'reason',
        'processed_at',
        'processed_by',
    ];

    protected function casts(): array
    {
        return [
            'check_in_date' => 'date',
            'expected_check_out_date' => 'date',
            'actual_check_out_date' => 'date',
        ];
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when(
                $filters['search'] ?? null,
                fn($q, $search) =>
                $q->where(
                    fn($q) =>
                    $q->whereHas(
                        'user',
                        fn($q) =>
                        $q->where('name', 'like', "%$search%")
                    )->orWhereHas(
                        'room',
                        fn($q) =>
                        $q->where('room_code', 'like', "%$search%")
                    )
                )
            )
            ->when(
                $filters['booking_type'] ?? null,
                fn($q, $booking_type) =>
                $q->where('booking_type', $booking_type)
            )
            ->when(
                $filters['rental_type'] ?? null,
                fn($q, $rental_type) =>
                $q->where('rental_type', $rental_type)
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

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }
}
