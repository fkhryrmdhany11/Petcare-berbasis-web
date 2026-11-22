<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdoptionRequest extends Model
{
    protected $table = 'adoption_request';
    protected $primaryKey = 'adoption_request_id';

    protected $fillable = [
        'user_id',
        'animal_id',
        'applicant_name',
        'email',
        'phone',
        'address',
        'reason',
        'request_date',
        'status',
        'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'request_date' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id');
    }

    public function animal()
    {
        return $this->belongsTo(Animal::class, 'animal_id', 'animal_id');
    }
}
