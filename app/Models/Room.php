<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'room_code',
        'block',
        'floor',
        'gender_type',
        'price_per_month',
        'capacity',
        'current_occupancy',
        'description',
    ];

    protected const SORT_OPTIONS = [
        'capacity_asc' => ['capacity', 'asc'],
        'capacity_desc' => ['capacity', 'desc'],
        'current_occupancy_asc' => ['current_occupancy', 'asc'],
        'current_occupancy_desc' => ['current_occupancy', 'desc'],
    ];

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when(
                $filters['room_code'] ?? null,
                fn($q, $room_code) =>
                $q->where('room_code', 'like', "%$room_code%")
            )
            ->when(
                $filters['block'] ?? null,
                fn($q, $block) =>
                $q->where('block', $block)
            )
            ->when(
                $filters['floor'] ?? null,
                fn($q, $floor) =>
                $q->where('floor', $floor)
            )
            ->when(
                $filters['gender_type'] ?? null,
                fn($q, $gender_type) =>
                $q->where('gender_type', $gender_type)
            )
            ->when(
                $filters['branch_id'] ?? null,
                fn($q, $branch_id) =>
                $q->where('branch_id', $branch_id)
            )
            ->when(
                $filters['sort'] ?? null,
                fn($q, $sort) =>
                self::SORT_OPTIONS[$sort] ?? false
                    ? $q->orderBy(...self::SORT_OPTIONS[$sort])
                    : $q->orderBy('updated_at', 'desc'),
                fn($q) => $q->orderBy('updated_at', 'desc')
            );
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
