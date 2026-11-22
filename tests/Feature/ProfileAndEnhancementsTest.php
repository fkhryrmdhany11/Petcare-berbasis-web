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
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProfileAndEnhancementsTest extends TestCase
{
    use RefreshDatabase;

    public function test_top_right_profile_and_cart_bar_and_no_sidebar_on_profile_page(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::where('email', 'arini@pawpaw.com')->firstOrFail();

        // Add 2 items to cart
        $product = Product::firstOrFail();
        $this->actingAs($user)->post('/cart/add', [
            'product_id' => $product->product_id,
            'quantity' => 2,
        ]);

        // Home page has sidebar and top-right bar with cart count 2
        $homeResp = $this->actingAs($user)->get('/');
        $homeResp->assertStatus(200);
        $homeResp->assertSee('top-user-bar', false);
        $homeResp->assertSee('<aside class="sidebar">', false);
        $homeResp->assertSee('<span class="nav-icon" aria-hidden="true">🏠</span>', false);
        $homeResp->assertSee('<span class="nav-icon" aria-hidden="true">🐾</span>', false);
        $homeResp->assertSee('<span class="nav-icon" aria-hidden="true">🛍️</span>', false);
        $homeResp->assertSee('<span class="nav-icon" aria-hidden="true">🩺</span>', false);

        // Profile page has no sidebar (app-no-sidebar) and back button
        $profileResp = $this->actingAs($user)->get('/profile');
        $profileResp->assertStatus(200);
        $profileResp->assertSee('app-no-sidebar', false);
        $profileResp->assertDontSee('<aside class="sidebar">', false);
    }

    public function test_file_upload_for_avatar_product_and_animal(): void
    {
        $this->seed(DatabaseSeeder::class);
        $admin = User::where('email', 'admin@pawpaw.com')->firstOrFail();
        $user = User::where('email', 'arini@pawpaw.com')->firstOrFail();

        // 1. Customer uploads avatar
        $avatarFile = UploadedFile::fake()->create('avatar.png', 64, 'image/png');
        $this->actingAs($user)->put('/profile', [
            'name' => 'Arini Putri Updated',
            'username' => 'arini',
            'email' => 'arini@pawpaw.com',
            'phone' => '081234567890',
            'address' => 'Pekanbaru',
            'bio' => 'Pecinta Kucing',
            'avatar_file' => $avatarFile,
        ])->assertRedirect('/profile');

        $user->refresh();
        $this->assertNotNull($user->avatar);
        $this->assertStringStartsWith('assets/img/uploads/avatars/', $user->avatar);

        // 2. Admin uploads product image
        $prodFile = UploadedFile::fake()->create('kalung.jpg', 64, 'image/jpeg');
        $this->actingAs($admin)->post('/admin/products', [
            'product_name' => 'Kalung Lonceng Emas',
            'category' => 'Aksesoris',
            'price' => 55000,
            'stock' => 12,
            'description' => 'Kalung lonceng cantik',
            'image_file' => $prodFile,
        ])->assertRedirect('/admin/products');

        $newProd = Product::where('product_name', 'Kalung Lonceng Emas')->firstOrFail();
        $this->assertStringStartsWith('assets/img/uploads/products/', $newProd->image);

        // 3. Admin uploads animal image
        $animalFile = UploadedFile::fake()->create('kucing.png', 64, 'image/png');
        $this->actingAs($admin)->post('/admin/animals', [
            'nama' => 'Choco',
            'species' => 'Kucing',
            'breed' => 'British Shorthair',
            'gender' => 'Jantan',
            'age' => '1 Tahun',
            'status' => 'available',
            'description' => 'Kucing sehat menggemaskan',
            'image_file' => $animalFile,
        ])->assertRedirect('/admin/animals');

        $newAnimal = Animal::where('nama', 'Choco')->firstOrFail();
        $this->assertStringStartsWith('assets/img/uploads/animals/', $newAnimal->image);
    }

    public function test_customer_can_view_printable_invoice_and_booking_card(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::where('email', 'arini@pawpaw.com')->firstOrFail();
        $otherUser = User::create([
            'username' => 'other',
            'name' => 'Other User',
            'email' => 'other@pawpaw.com',
            'password' => 'user123',
            'role' => 'user',
        ]);

        $product = Product::firstOrFail();
        $order = Order::create([
            'order_code' => 'ORD-INV-001',
            'user_id' => $user->user_id,
            'recipient_name' => 'Arini Putri',
            'recipient_phone' => '081234567890',
            'delivery_method' => 'delivery',
            'shipping_address' => 'Jl. Panam Raya',
            'shipping_fee' => 15000,
            'total_price' => $product->price + 15000,
            'order_date' => now(),
            'status' => 'paid',
        ]);

        OrderDetail::create([
            'order_id' => $order->order_id,
            'product_id' => $product->product_id,
            'quantity' => 1,
            'unit_price' => $product->price,
            'subtotal' => $product->price,
        ]);

        $service = HealthService::firstOrFail();
        $booking = HealthBooking::create([
            'user_id' => $user->user_id,
            'service_id' => $service->service_id,
            'owner_name' => 'Arini Putri',
            'pet_name' => 'Mochi',
            'pet_species' => 'Kucing',
            'booking_date' => now()->addDay(),
            'status' => 'approved',
        ]);

        // Owner can view invoice and booking card
        $this->actingAs($user)->get("/orders/{$order->order_id}/invoice")
            ->assertStatus(200)
            ->assertSee('ORD-INV-001');

        $this->actingAs($user)->get("/health-services/bookings/{$booking->booking_id}/card")
            ->assertStatus(200)
            ->assertSee('Mochi');

        // Other user is forbidden
        $this->actingAs($otherUser)->get("/orders/{$order->order_id}/invoice")
            ->assertStatus(403);
    }

    public function test_customer_can_cancel_pending_order_restoring_stock_and_cancel_pending_adoption_and_booking(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::where('email', 'arini@pawpaw.com')->firstOrFail();
        $product = Product::firstOrFail();
        $initialStock = $product->stock;

        // Place an order via checkout (paid or pending - let's test pending order cancellation)
        $order = Order::create([
            'order_code' => 'ORD-CANCEL-001',
            'user_id' => $user->user_id,
            'recipient_name' => 'Arini Putri',
            'recipient_phone' => '081234567890',
            'delivery_method' => 'pickup',
            'shipping_address' => null,
            'shipping_fee' => 0,
            'total_price' => $product->price * 2,
            'order_date' => now(),
            'status' => 'pending',
        ]);

        OrderDetail::create([
            'order_id' => $order->order_id,
            'product_id' => $product->product_id,
            'quantity' => 2,
            'unit_price' => $product->price,
            'subtotal' => $product->price * 2,
        ]);

        $product->decrement('stock', 2);
        $this->assertEquals($initialStock - 2, $product->fresh()->stock);

        // Cancel order
        $this->actingAs($user)->post("/orders/{$order->order_id}/cancel")
            ->assertRedirect();

        $this->assertDatabaseHas('orders', [
            'order_id' => $order->order_id,
            'status' => 'cancelled',
        ]);
        $this->assertEquals($initialStock, $product->fresh()->stock);

        // Cancel pending adoption
        $animal = Animal::where('status', 'available')->firstOrFail();
        $adoption = AdoptionRequest::create([
            'user_id' => $user->user_id,
            'animal_id' => $animal->animal_id,
            'applicant_name' => 'Arini Putri',
            'email' => 'arini@pawpaw.com',
            'phone' => '081234567890',
            'address' => 'Pekanbaru',
            'reason' => 'Ingin merawat',
            'request_date' => now(),
            'status' => 'pending',
        ]);

        $this->actingAs($user)->post("/adoption/{$adoption->adoption_request_id}/cancel")
            ->assertRedirect();

        $this->assertDatabaseHas('adoption_request', [
            'adoption_request_id' => $adoption->adoption_request_id,
            'status' => 'cancelled',
        ]);

        // Cancel pending health booking
        $service = HealthService::firstOrFail();
        $booking = HealthBooking::create([
            'user_id' => $user->user_id,
            'service_id' => $service->service_id,
            'owner_name' => 'Arini Putri',
            'pet_name' => 'Mochi',
            'pet_species' => 'Kucing',
            'booking_date' => now()->addDays(2),
            'status' => 'pending',
        ]);

        $this->actingAs($user)->post("/health-services/bookings/{$booking->booking_id}/cancel")
            ->assertRedirect();

        $this->assertDatabaseHas('health_booking', [
            'booking_id' => $booking->booking_id,
            'status' => 'cancelled',
        ]);
    }
}
