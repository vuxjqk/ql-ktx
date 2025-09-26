<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Utility extends Model
{
    protected $fillable = [
        'room_id',
        'month',
        'electric_usage',
        'water_usage',
        'electric_cost',
        'water_cost',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'date',
        ];
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
