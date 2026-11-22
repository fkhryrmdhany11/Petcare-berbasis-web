<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Animal extends Model
{
    protected $table = 'animal';
    protected $primaryKey = 'animal_id';

    protected $fillable = [
        'nama',
        'species',
        'breed',
        'gender',
        'age',
        'status',
        'image',
        'description',
    ];

    public function adoptionRequests()
    {
        return $this->hasMany(AdoptionRequest::class, 'animal_id', 'animal_id');
    }
}
