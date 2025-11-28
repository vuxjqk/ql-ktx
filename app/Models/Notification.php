<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Notification extends Model
{
    protected $fillable = [
        'title',
        'content',
        'attachment',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reads()
    {
        return $this->hasMany(NotificationRead::class);
    }

    public function getIsReadAttribute()
    {
        $userId = Auth::id();

        if (!$userId) {
            return false;
        }

        return $this->reads()->where('user_id', $userId)->exists();
    }
}
