<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'user';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'bio',
        'avatar',
    ];

    public function getAvatarUrlAttribute(): string
    {
        if (!empty($this->avatar)) {
            return asset($this->avatar);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=D6E8FF&color=0F2854&size=150';
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function isAdmin(): bool
    {
        return strtolower((string) $this->role) === 'admin';
    }

    public function pets()
    {
        return $this->hasMany(Pet::class, 'user_id', 'user_id');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'user_id', 'user_id');
    }

    public function adoptionRequests()
    {
        return $this->hasMany(AdoptionRequest::class, 'user_id', 'user_id');
    }

    public function healthBookings()
    {
        return $this->hasMany(HealthBooking::class, 'user_id', 'user_id');
    }
}
