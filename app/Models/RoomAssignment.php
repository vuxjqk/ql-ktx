<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomAssignment extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'checked_in_at',
        'checked_out_at',
        'registration_id',
    ];

    protected function casts(): array
    {
        return [
            'checked_in_at' => 'datetime',
            'checked_out_at' => 'datetime',
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

    public function registration()
    {
        return $this->belongsTo(RoomRegistration::class, 'registration_id');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
}
