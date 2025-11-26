<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    protected $fillable = [
        'floor_number',
        'branch_id',
        'gender_type',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
