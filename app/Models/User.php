<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'provider',
        'provider_id',
        'avatar',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected const SORT_OPTIONS = [
        'name_asc' => ['name', 'asc'],
        'name_desc' => ['name', 'desc'],
    ];

    public function scopeFilter($query, array $filters)
    {
        return $query
            ->when(
                $filters['search'] ?? null,
                fn($q, $search) =>
                $q->where(
                    fn($q) =>
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhereHas(
                            'student',
                            fn($q) =>
                            $q->where('student_code', 'like', "%{$search}%")
                        )
                )
            )
            ->when(
                $filters['role'] ?? null,
                fn($q, $role) =>
                $q->where('role', $role)
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

    public function student()
    {
        return $this->hasOne(Student::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'user_branches');
    }

    public function activeBooking()
    {
        return $this->hasOne(Booking::class)->where('status', 'active');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    public function repairs()
    {
        return $this->hasMany(Repair::class);
    }

    public function favouriteRooms()
    {
        return $this->belongsToMany(Room::class, 'favourites', 'user_id', 'room_id')
            ->withTimestamps(); // nếu bảng favourites có created_at, updated_at
    }
}
