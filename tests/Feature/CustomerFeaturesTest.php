<?php

namespace Tests\Feature;

use App\Models\Animal;
use App\Models\HealthService;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerFeaturesTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_submit_adoption_request_and_health_booking_and_manage_pets(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::where('role', 'user')->first();
        $animal = Animal::where('status', 'available')->first();
        $service = HealthService::first();

        $this->actingAs($user)->post('/adoption', [
            'animal_id' => $animal->animal_id,
            'applicant_name' => $user->name,
            'email' => $user->email,
            'phone' => '081234567890',
            'address' => 'Jl. Panam Raya',
            'reason' => 'Ingin merawat dengan penuh kasih sayang',
        ])->assertRedirect('/adoption/history');

        $this->assertDatabaseHas('adoption_request', [
            'user_id' => $user->user_id,
            'animal_id' => $animal->animal_id,
            'status' => 'pending',
        ]);

        $this->actingAs($user)->post('/health-services/book', [
            'service_id' => $service->service_id,
            'owner_name' => $user->name,
            'pet_name' => 'Mochi',
            'pet_species' => 'Kucing',
            'booking_date' => '2026-11-10 10:00:00',
        ])->assertRedirect('/health-services');

        $this->assertDatabaseHas('health_booking', [
            'user_id' => $user->user_id,
            'service_id' => $service->service_id,
            'pet_name' => 'Mochi',
            'status' => 'pending',
        ]);

        $this->actingAs($user)->post('/profile/pets', [
            'pet_name' => 'Coco',
            'species' => 'Anjing',
            'breed' => 'Poodle',
            'gender' => 'Betina',
            'age' => '6 Bulan',
            'health_status' => 'Sehat',
        ])->assertRedirect('/profile');

        $this->assertDatabaseHas('pet', ['user_id' => $user->user_id, 'pet_name' => 'Coco']);
    }
}
