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
                $filters['room_code'] ?? null,
                fn($q, $room_code) =>
                $q->where('room_code', 'like', "%$room_code%")
            )
            ->when(
                $filters['branch_id'] ?? null,
                fn($q, $branch_id) =>
                $q->whereHas(
                    'floor',
                    fn($q) =>
                    $q->where('branch_id', $branch_id)
                )
            )
            ->when(
                $filters['floor_id'] ?? null,
                fn($q, $floor) =>
                $q->where('floor_id', $floor)
            )
            ->when(
                $filters['gender_type'] ?? null,
                fn($q, $gender_type) =>
                $q->whereHas(
                    'floor',
                    fn($q) =>
                    $q->where('gender_type', $gender_type)
                )
            )
            ->when(
                $filters['is_status'] ?? null,
                fn($q, $is_status) =>
                $q->where('is_status', $is_status)
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
        return $this->hasOne(RoomImage::class)->latest();
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
