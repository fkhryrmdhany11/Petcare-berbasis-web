<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopCheckoutFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_checkout_creates_order_details_payment_and_decrements_stock(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::where('role', 'user')->first();
        $product = Product::first();
        $initialStock = $product->stock;

        $this->actingAs($user)->post('/cart/add', [
            'product_id' => $product->product_id,
            'quantity' => 2,
        ]);

        $response = $this->actingAs($user)->post('/checkout', [
            'recipient_name' => 'Arini Dewinda',
            'recipient_phone' => '081234567890',
            'delivery_method' => 'delivery',
            'shipping_address' => 'Jl. Panam Raya No. 12',
        ]);

        $response->assertRedirect('/orders/history');
        $this->assertEquals($initialStock - 2, $product->fresh()->stock);
        $this->assertDatabaseHas('orders', ['user_id' => $user->user_id, 'delivery_method' => 'delivery']);
        $this->assertDatabaseHas('order_detail', ['product_id' => $product->product_id, 'quantity' => 2]);
        $this->assertDatabaseHas('payment', ['payment_status' => 'paid']);
    }

    public function test_checkout_fails_when_requested_quantity_exceeds_stock(): void
    {
        $this->seed(DatabaseSeeder::class);
        $user = User::where('role', 'user')->first();
        $product = Product::first();
        $product->update(['stock' => 1]);

        $this->actingAs($user)->withSession([
            'cart' => [$product->product_id => 5],
        ])->post('/checkout', [
            'recipient_name' => 'Arini',
            'recipient_phone' => '081234567890',
            'delivery_method' => 'pickup',
        ])->assertSessionHasErrors();

        $this->assertEquals(1, $product->fresh()->stock);
    }
}
