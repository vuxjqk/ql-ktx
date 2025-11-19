<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceUsage extends Model
{
    protected $fillable = [
        'room_id',
        'service_id',
        'usage_date',
        'usage_amount',
        'unit_price',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'usage_date' => 'date',
        ];
    }
<<<<<<< HEAD
=======

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function shares()
    {
        return $this->hasMany(ServiceUsageShare::class);
    }
>>>>>>> upstream-main
}
