<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthAndMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_registration_creates_unique_username_from_email(): void
    {
        $this->post('/register', [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertRedirect('/');

        $this->post('/logout');

        $this->post('/register', [
            'name' => 'Budi Lain',
            'email' => 'budi@other.com',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
        ])->assertRedirect('/');

        $this->assertDatabaseHas('user', ['username' => 'budi']);
        $this->assertDatabaseHas('user', ['username' => 'budi1']);
    }

    public function test_non_admin_cannot_access_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $this->actingAs($user)->get('/admin/dashboard')->assertStatus(403);
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@pawpaw.com',
            'password' => 'admin123',
            'role' => 'admin',
        ]);

        $this->post('/login', [
            'email' => 'admin@pawpaw.com',
            'password' => 'admin123',
        ])->assertRedirect('/admin/dashboard');

        $this->assertAuthenticatedAs($admin);
    }
}
