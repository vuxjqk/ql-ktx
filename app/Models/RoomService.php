<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomService extends Model
{
    protected $fillable = [
        'room_id',
        'service_id',
    ];
}
