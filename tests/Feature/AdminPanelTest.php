<?php

namespace Tests\Feature;

use App\Models\AdoptionRequest;
use App\Models\Animal;
use App\Models\HealthBooking;
use App\Models\HealthService;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Pet;
use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use RefreshDatabase;

    public function test_approving_adoption_marks_animal_adopted_and_rejects_other_pending_requests(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@pawpaw.com')->firstOrFail();
        $user1 = User::where('email', 'arini@pawpaw.com')->firstOrFail();
        $user2 = User::create([
            'username' => 'budi',
            'name' => 'Budi Santoso',
            'email' => 'budi@pawpaw.com',
            'password' => 'user123',
            'role' => 'user',
        ]);

        $animal = Animal::where('status', 'available')->firstOrFail();

        $req1 = AdoptionRequest::create([
            'user_id' => $user1->user_id,
            'animal_id' => $animal->animal_id,
            'applicant_name' => 'Arini Putri',
            'email' => 'arini@pawpaw.com',
            'phone' => '081234567890',
            'address' => 'Jl. Melati No. 1',
            'reason' => 'Ingin merawat',
            'request_date' => now(),
            'status' => 'pending',
        ]);

        $req2 = AdoptionRequest::create([
            'user_id' => $user2->user_id,
            'animal_id' => $animal->animal_id,
            'applicant_name' => 'Budi Santoso',
            'email' => 'budi@pawpaw.com',
            'phone' => '081999999999',
            'address' => 'Jl. Mawar No. 2',
            'reason' => 'Suka kucing',
            'request_date' => now(),
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->post("/admin/adoptions/{$req1->adoption_request_id}/status", [
            'status' => 'approved',
        ])->assertRedirect();

        $this->assertDatabaseHas('adoption_request', [
            'adoption_request_id' => $req1->adoption_request_id,
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('animal', [
            'animal_id' => $animal->animal_id,
            'status' => 'adopted',
        ]);

        $this->assertDatabaseHas('adoption_request', [
            'adoption_request_id' => $req2->adoption_request_id,
            'status' => 'rejected',
        ]);
    }

    public function test_deleting_product_with_existing_order_detail_returns_graceful_error(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@pawpaw.com')->firstOrFail();
        $user = User::where('email', 'arini@pawpaw.com')->firstOrFail();
        $product = Product::firstOrFail();

        $order = Order::create([
            'order_code' => 'ORD-TEST-001',
            'user_id' => $user->user_id,
            'recipient_name' => 'Arini Putri',
            'recipient_phone' => '081234567890',
            'delivery_method' => 'delivery',
            'shipping_address' => 'Jl. Melati',
            'shipping_fee' => 15000,
            'total_price' => $product->price + 15000,
            'order_date' => now(),
            'status' => 'completed',
        ]);

        OrderDetail::create([
            'order_id' => $order->order_id,
            'product_id' => $product->product_id,
            'quantity' => 1,
            'unit_price' => $product->price,
            'subtotal' => $product->price,
        ]);

        $response = $this->actingAs($admin)->post("/admin/products/{$product->product_id}/delete");
        $response->assertRedirect();
        $response->assertSessionHas('error');

        $this->assertDatabaseHas('product', [
            'product_id' => $product->product_id,
        ]);
    }

    public function test_admin_can_manage_products_animals_services_orders_and_bookings(): void
    {
        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'admin@pawpaw.com')->firstOrFail();
        $user = User::where('email', 'arini@pawpaw.com')->firstOrFail();

        // Dashboard renders
        $this->actingAs($admin)->get('/admin')->assertStatus(200);

        // Quick action adds product
        $this->actingAs($admin)->post('/admin/quick-action', [
            'action_type' => 'product',
            'product_name' => 'Kalung Anjing Kulit',
            'category' => 'Aksesoris',
            'price' => 45000,
            'stock' => 15,
            'description' => 'Kalung kulit asli',
        ])->assertRedirect();

        $this->assertDatabaseHas('product', ['product_name' => 'Kalung Anjing Kulit']);

        // Store animal
        $this->actingAs($admin)->post('/admin/animals', [
            'nama' => 'Snowy',
            'species' => 'Kucing',
            'breed' => 'Anggora',
            'age' => '8 Bulan',
            'gender' => 'Betina',
            'status' => 'available',
            'description' => 'Kucing putih bersih',
        ])->assertRedirect();

        $this->assertDatabaseHas('animal', ['nama' => 'Snowy', 'breed' => 'Anggora']);

        // Store health service
        $this->actingAs($admin)->post('/admin/services', [
            'service_name' => 'Scaling Gigi Hewan',
            'price' => 250000,
            'description' => 'Pembersihan karang gigi',
        ])->assertRedirect();

        $this->assertDatabaseHas('health_service', ['service_name' => 'Scaling Gigi Hewan']);

        // Update order status
        $order = Order::create([
            'order_code' => 'ORD-TEST-002',
            'user_id' => $user->user_id,
            'recipient_name' => 'Arini',
            'recipient_phone' => '08123',
            'delivery_method' => 'pickup',
            'shipping_address' => 'Jakarta',
            'shipping_fee' => 0,
            'total_price' => 50000,
            'order_date' => now(),
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->post("/admin/orders/{$order->order_id}/status", [
            'status' => 'completed',
        ])->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'order_id' => $order->order_id,
            'status' => 'completed',
        ]);

        // Update health booking status
        $pet = Pet::firstOrFail();
        $service = HealthService::firstOrFail();
        $booking = HealthBooking::create([
            'user_id' => $user->user_id,
            'pet_id' => $pet->pet_id,
            'service_id' => $service->service_id,
            'owner_name' => 'Arini',
            'pet_name' => $pet->pet_name,
            'pet_species' => $pet->species,
            'booking_date' => now()->addDay(),
            'status' => 'pending',
        ]);

        $this->actingAs($admin)->post("/admin/bookings/{$booking->booking_id}/status", [
            'status' => 'approved',
        ])->assertRedirect();

        $this->assertDatabaseHas('health_booking', [
            'booking_id' => $booking->booking_id,
            'status' => 'approved',
        ]);
    }
}
