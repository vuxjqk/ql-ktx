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
}
