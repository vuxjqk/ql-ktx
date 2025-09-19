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
}
