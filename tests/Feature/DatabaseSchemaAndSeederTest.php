<?php

namespace Tests\Feature;

use App\Models\Animal;
use App\Models\HealthService;
use App\Models\Pet;
use App\Models\Product;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseSchemaAndSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_populates_initial_pet_care_records(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('user', ['email' => 'admin@pawpaw.com', 'role' => 'admin']);
        $this->assertDatabaseHas('user', ['email' => 'arini@pawpaw.com', 'role' => 'user']);
        $this->assertEquals(6, Product::count());
        $this->assertEquals(4, Animal::count());
        $this->assertEquals(3, HealthService::count());
        $this->assertEquals(2, Pet::count());
    }
}
