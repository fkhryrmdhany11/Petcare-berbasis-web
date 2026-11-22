<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdoptionRequest;
use App\Models\Animal;
use App\Models\HealthBooking;
use App\Models\HealthService;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'products_count' => Product::count(),
            'orders_count' => Order::count(),
            'animals_count' => Animal::count(),
            'adoptions_count' => AdoptionRequest::count(),
            'bookings_count' => HealthBooking::count(),
            'users_count' => User::where('role', 'user')->count(),
            'revenue' => Order::whereIn('status', ['paid', 'processing', 'shipped', 'completed'])->sum('total_price'),
        ];

        $recentOrders = Order::with('user')->latest('order_date')->take(5)->get();
        $recentAdoptions = AdoptionRequest::with(['animal', 'user'])->latest('request_date')->take(5)->get();
        $recentBookings = HealthBooking::with('service')->latest('booking_date')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'recentAdoptions', 'recentBookings'));
    }

    public function quickAction(Request $request)
    {
        $type = $request->input('action_type', 'product');

        if ($type === 'product') {
            $name = $request->input('product_name') ?? $request->input('name');
            $request->merge(['product_name' => $name]);

            $validated = $request->validate([
                'product_name' => ['required', 'string', 'max:150'],
                'category' => ['required', 'string', 'max:50'],
                'price' => ['required', 'numeric', 'min:0'],
                'stock' => ['required', 'integer', 'min:0'],
                'image' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
            ]);

            $slugBase = Str::slug($validated['product_name']) ?: 'produk';
            $slug = $slugBase;
            $counter = 1;
            while (Product::where('slug', $slug)->exists()) {
                $slug = $slugBase . '-' . $counter++;
            }

            Product::create([
                'slug' => $slug,
                'product_name' => $validated['product_name'],
                'category' => $validated['category'],
                'price' => $validated['price'],
                'stock' => $validated['stock'],
                'image' => ($validated['image'] ?? null) ?: 'assets/img/shop/whiskas.png',
                'description' => $validated['description'] ?? 'Produk perawatan hewan PawPaw.',
            ]);

            return redirect()->route('admin.dashboard')->with('success', 'Produk baru berhasil ditambahkan melalui Quick Action.');
        }

        if ($type === 'animal') {
            $nama = $request->input('nama') ?? $request->input('name');
            $request->merge(['nama' => $nama]);

            $validated = $request->validate([
                'nama' => ['required', 'string', 'max:100'],
                'species' => ['required', 'string', 'max:50'],
                'breed' => ['required', 'string', 'max:50'],
                'gender' => ['required', 'string', 'max:20'],
                'age' => ['required', 'string', 'max:50'],
                'image' => ['nullable', 'string', 'max:255'],
                'description' => ['nullable', 'string'],
            ]);

            Animal::create([
                'nama' => $validated['nama'],
                'species' => $validated['species'],
                'breed' => $validated['breed'],
                'gender' => $validated['gender'],
                'age' => $validated['age'],
                'status' => 'available',
                'image' => ($validated['image'] ?? null) ?: 'assets/img/meng.png',
                'description' => $validated['description'] ?? 'Hewan sehat siap diadopsi.',
            ]);

            return redirect()->route('admin.dashboard')->with('success', 'Hewan adopsi berhasil ditambahkan melalui Quick Action.');
        }

        if ($type === 'service') {
            $validated = $request->validate([
                'service_name' => ['required', 'string', 'max:100'],
                'price' => ['required', 'numeric', 'min:0'],
                'icon' => ['nullable', 'string', 'max:20'],
                'card_color' => ['nullable', 'string', 'max:30'],
                'description' => ['nullable', 'string', 'max:255'],
            ]);

            HealthService::create([
                'service_name' => $validated['service_name'],
                'price' => $validated['price'],
                'icon' => ($validated['icon'] ?? null) ?: '🩺',
                'card_color' => ($validated['card_color'] ?? null) ?: 'card-green',
                'description' => $validated['description'] ?? 'Layanan kesehatan klinik PawPaw.',
            ]);

            return redirect()->route('admin.dashboard')->with('success', 'Layanan kesehatan berhasil ditambahkan melalui Quick Action.');
        }

        return redirect()->route('admin.dashboard')->with('error', 'Tipe aksi cepat tidak dikenali.');
    }
}
