<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceUsageShare extends Model
{
    protected $fillable = [
        'service_usage_id',
        'user_id',
        'share_amount',
    ];

    public function serviceUsage()
    {
        return $this->belongsTo(ServiceUsage::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
