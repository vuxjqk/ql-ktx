<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Refund extends Model
{
    protected $fillable = [
        'user_id',
        'bill_id',
        'room_assignment_id',
        'amount',
        'status',
        'reason',
        'requested_at',
        'processed_at',
    ];

    // Quan hệ với bảng users (người yêu cầu hoàn tiền)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Quan hệ với bảng bills (hoàn tiền theo hóa đơn)
    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    // Quan hệ với bảng room_assignments (hoàn tiền theo phân công phòng)
    public function roomAssignment()
    {
        return $this->belongsTo(RoomAssignment::class);
    }
}
