<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HealthService extends Model
{
    protected $table = 'health_service';
    protected $primaryKey = 'service_id';

    protected $fillable = [
        'service_name',
        'doctor_name',
        'practice_days',
        'time_slots',
        'daily_quota',
        'description',
        'icon',
        'card_color',
        'price',
    ];

    public function getIconAttribute(?string $value): string
    {
        if (! empty($value) && ! str_contains($value, '?')) {
            return $value;
        }

        $name = strtolower((string) ($this->attributes['service_name'] ?? ''));

        return match (true) {
            str_contains($name, 'vaksin anjing') => '🐕',
            str_contains($name, 'vaksin') => '💉',
            str_contains($name, 'rumah') || str_contains($name, 'kunjungan') => '🏥',
            str_contains($name, 'nutrisi') => '🥗',
            str_contains($name, 'kulit') => '🔬',
            str_contains($name, 'telinga') => '👂',
            str_contains($name, 'gigi') => '🦷',
            str_contains($name, 'cacing') || str_contains($name, 'obat') => '💊',
            str_contains($name, 'mata') => '👁️',
            default => '🩺',
        };
    }

    public function getTimeSlotsArrayAttribute(): array
    {
        $raw = $this->time_slots ?: '09:00, 13:00, 16:00';
        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    }

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
        ];
    }

    public function bookings()
    {
        return $this->hasMany(HealthBooking::class, 'service_id', 'service_id');
    }
}
