<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomRegistration extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'status',
        'notes',
        'processed_at',
        'processed_by',
    ];

    protected function casts(): array
    {
        return [
            'requested_at' => 'datetime',
            'processed_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function processor()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    public function assignment()
    {
        return $this->hasOne(RoomAssignment::class, 'registration_id');
    }

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when(
                $filters['status'] ?? null,
                fn($q, $status) =>
                $q->where('status', $status)
            );
    }
}
