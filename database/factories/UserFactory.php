<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    protected static ?string $password;

    public function definition(): array
    {
        $username = strtolower(Str::slug(fake()->unique()->userName(), ''));
        return [
            'name' => fake()->name(),
            'username' => substr($username ?: 'user' . rand(100, 999), 0, 40),
            'email' => fake()->unique()->safeEmail(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => 'user',
            'phone' => '081234567890',
            'address' => 'Jl. Panam Raya No. 12, Pekanbaru',
            'bio' => 'Pet lover & proud pet parent',
            'remember_token' => Str::random(10),
        ];
    }
}
