<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    protected $table = 'pet';
    protected $primaryKey = 'pet_id';

    protected $fillable = [
        'user_id',
        'pet_name',
        'species',
        'breed',
        'gender',
        'age',
        'health_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function healthBookings()
    {
        return $this->hasMany(HealthBooking::class, 'pet_id', 'pet_id');
    }
}
