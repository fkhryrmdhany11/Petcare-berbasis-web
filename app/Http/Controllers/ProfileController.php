<?php

namespace App\Http\Controllers;

use App\Models\AdoptionRequest;
use App\Models\HealthBooking;
use App\Models\Order;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $pets = Pet::where('user_id', $user->user_id)->orderByDesc('pet_id')->get();
        $ordersCount = Order::where('user_id', $user->user_id)->count();
        $bookingsCount = HealthBooking::where('user_id', $user->user_id)->count();
        $adoptionsCount = AdoptionRequest::where('user_id', $user->user_id)->count();

        // Gabungkan aktivitas terbaru dari 3 modul
        $recentOrders = Order::where('user_id', $user->user_id)
            ->latest('order_date')
            ->take(3)
            ->get()
            ->map(fn ($o) => [
                'icon' => '🛍️',
                'title' => "Pesanan {$o->order_code}",
                'desc' => 'Total Rp ' . number_format((float) $o->total_price, 0, ',', '.') . ' • Status: ' . ucfirst($o->status),
                'date' => $o->order_date ?? $o->created_at,
            ]);

        $recentBookings = HealthBooking::with('service')
            ->where('user_id', $user->user_id)
            ->latest('created_at')
            ->take(3)
            ->get()
            ->map(fn ($b) => [
                'icon' => '🩺',
                'title' => $b->service?->service_name ?? 'Janji Kesehatan',
                'desc' => "Untuk {$b->pet_name} ({$b->pet_species}) • Status: " . ucfirst($b->status),
                'date' => $b->booking_date ?? $b->created_at,
            ]);

        $recentAdoptions = AdoptionRequest::with('animal')
            ->where('user_id', $user->user_id)
            ->latest('request_date')
            ->take(3)
            ->get()
            ->map(fn ($a) => [
                'icon' => '🏠',
                'title' => 'Pengajuan Adopsi ' . ($a->animal?->nama ?? 'Hewan'),
                'desc' => 'Status: ' . ucfirst($a->status),
                'date' => $a->request_date ?? $a->created_at,
            ]);

        $activities = $recentOrders
            ->concat($recentBookings)
            ->concat($recentAdoptions)
            ->sortByDesc('date')
            ->take(5);

        return view('profile.index', compact(
            'user',
            'pets',
            'ordersCount',
            'bookingsCount',
            'adoptionsCount',
            'activities'
        ));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'username' => ['required', 'string', 'max:50', 'unique:user,username,' . $user->user_id . ',user_id'],
            'email' => ['required', 'email', 'max:100', 'unique:user,email,' . $user->user_id . ',user_id'],
            'phone' => ['nullable', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:300'],
            'bio' => ['nullable', 'string', 'max:255'],
            'avatar_file' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        if ($request->hasFile('avatar_file')) {
            $file = $request->file('avatar_file');
            $dir = public_path('assets/img/uploads/avatars');
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $filename = 'avatar-' . $user->user_id . '-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $filename);
            $data['avatar'] = 'assets/img/uploads/avatars/' . $filename;
        }
        unset($data['avatar_file']);

        $user->update($data);

        return redirect()->route('profile.index')->with('success', 'Profil Anda berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.current_password' => 'Kata sandi saat ini tidak sesuai.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        Auth::user()->update([
            'password' => Hash::make($data['password']),
        ]);

        return redirect()->route('profile.index')->with('success', 'Kata sandi berhasil diperbarui.');
    }

    public function storePet(Request $request)
    {
        $data = $request->validate([
            'pet_name' => ['required', 'string', 'max:100'],
            'species' => ['required', 'string', 'max:50'],
            'breed' => ['nullable', 'string', 'max:50'],
            'gender' => ['nullable', 'string', 'max:20'],
            'age' => ['nullable', 'string', 'max:30'],
            'health_status' => ['nullable', 'string', 'max:50'],
        ]);

        Pet::create([
            'user_id' => Auth::id(),
            'pet_name' => trim($data['pet_name']),
            'species' => trim($data['species']),
            'breed' => trim($data['breed'] ?? 'Campuran') ?: 'Campuran',
            'gender' => trim($data['gender'] ?? 'Jantan') ?: 'Jantan',
            'age' => trim($data['age'] ?? '1 Tahun') ?: '1 Tahun',
            'health_status' => trim($data['health_status'] ?? 'Sehat') ?: 'Sehat',
        ]);

        return redirect()->route('profile.index')->with('success', 'Data hewan peliharaan berhasil ditambahkan!');
    }

    public function destroyPet(Pet $pet)
    {
        if ($pet->user_id !== Auth::id()) {
            abort(403);
        }

        $pet->delete();

        return redirect()->route('profile.index')->with('success', 'Data hewan peliharaan berhasil dihapus.');
    }
}
