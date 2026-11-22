<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthBooking extends Model
{
    protected $table = 'health_booking';
    protected $primaryKey = 'booking_id';

    protected $fillable = [
        'user_id',
        'pet_id',
        'service_id',
        'owner_name',
        'pet_name',
        'pet_species',
        'booking_date',
        'status',
        'rejection_reason',
    ];

    protected function casts(): array
    {
        return [
            'booking_date' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function pet()
    {
        return $this->belongsTo(Pet::class, 'pet_id', 'pet_id');
    }

    public function service()
    {
        return $this->belongsTo(HealthService::class, 'service_id', 'service_id');
    }
}
