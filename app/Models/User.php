<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'date_of_birth',
        'gender',
        'phone',
        'address',
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
        'a_to_z' => ['name', 'asc'],
        'z_to_a' => ['name', 'desc'],
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
                )
            )
            ->when(
                $filters['student_code'] ?? null,
                fn($q, $student_code) =>
                $q->whereHas(
                    'student',
                    fn($q) =>
                    $q->where('student_code', 'like', "%{$student_code}%")
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
                    : $q->orderBy('updated_at', 'desc'),
                fn($q) => $q->orderBy('updated_at', 'desc')
            );
    }

    public function student()
    {
        return $this->hasOne(Student::class);
    }
}
