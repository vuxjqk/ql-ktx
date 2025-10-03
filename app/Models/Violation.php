<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Violation extends Model
{
    protected $fillable = [
        'user_id',
        'room_id',
        'type',
        'description',
        'fine_amount',
        'status',
    ];

    // Quan hệ: Vi phạm thuộc về một người dùng (sinh viên)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ: Vi phạm có thể liên quan đến một phòng
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
