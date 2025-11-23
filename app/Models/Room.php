<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $fillable = [
        'room_code',
        'floor_id',
        'price_per_day',
        'price_per_month',
        'capacity',
        'current_occupancy',
        'is_active',
        'description',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected const SORT_OPTIONS = [
        'room_code_asc' => ['room_code', 'asc'],
        'room_code_desc' => ['room_code', 'desc'],
        'capacity_asc' => ['capacity', 'asc'],
        'capacity_desc' => ['capacity', 'desc'],
        'current_occupancy_asc' => ['current_occupancy', 'asc'],
        'current_occupancy_desc' => ['current_occupancy', 'desc'],
    ];

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when(
                $filters['q'] ?? null,
                fn($q, $search) =>
                $q->where('room_code', 'like', "%$search%")
            )

            ->when(
                $filters['branch_id'] ?? null,
                fn($q, $branchId) =>
                $q->whereHas(
                    'floor',
                    fn($sub) => $sub->where('branch_id', $branchId)
                )
            )

            ->when(
                $filters['floor_id'] ?? null,
                fn($q, $floorId) => $q->where('floor_id', $floorId)
            )

            ->when(
                $filters['gender_type'] ?? null,
                fn($q, $gender) =>
                $q->whereHas(
                    'floor',
                    fn($sub) => $sub->where('gender_type', $gender)
                )
            )

            ->when(
                $filters['min_price'] ?? null,
                fn($q, $min) => $q->where('price_per_month', '>=', $min)
            )

            ->when(
                $filters['max_price'] ?? null,
                fn($q, $max) => $q->where('price_per_month', '<=', $max)
            )

            ->when(
                $filters['capacity'] ?? null,
                fn($q, $capacity) => $q->where('capacity', '>=', $capacity)
            )

            ->when(
                isset($filters['available_only']) && $filters['available_only'],
                fn($q) => $q->whereColumn('current_occupancy', '<', 'capacity')
            )

            ->when(
                $filters['favourite_only'] ?? null,
                fn($q, $userId) =>
                $q->whereHas('favourites', fn($fav) => $fav->where('user_id', $userId))
            )

            ->when(
                $filters['sort'] ?? null,
                fn($q, $sort) =>
                self::SORT_OPTIONS[$sort] ?? false
                    ? $q->orderBy(...self::SORT_OPTIONS[$sort])
                    : $q->orderBy('created_at', 'desc'),
                fn($q) => $q->orderBy('created_at', 'desc')
            );
    }

    public function floor()
    {
        return $this->belongsTo(Floor::class);
    }

    public function images()
    {
        return $this->hasMany(RoomImage::class);
    }

    public function image()
    {
        return $this->hasOne(RoomImage::class)->latestOfMany();
    }

    public function services()
    {
        return $this->belongsToMany(Service::class, 'room_services');
    }

    public function serviceUsages()
    {
        return $this->hasMany(ServiceUsage::class);
    }

    public function activeBookings()
    {
        return $this->hasMany(Booking::class)->where('status', 'active')->with('user');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function repairs()
    {
        return $this->hasMany(Repair::class);
    }

    public function favourites()
    {
        return $this->hasMany(Favourite::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'room_amenities');
    }
}
