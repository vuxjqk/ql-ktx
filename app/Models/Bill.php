<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'user_id',
        'room_assignment_id',
        'amount',
        'status',
        'due_date',
    ];
}
