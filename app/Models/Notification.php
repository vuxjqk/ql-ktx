<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'content',
<<<<<<< HEAD
        'is_read',
        'user_id',
    ];
=======
        'attachment',
        'sender_id',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }
>>>>>>> upstream-main
}
