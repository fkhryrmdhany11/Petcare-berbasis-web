<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthBooking;
use App\Models\HealthService;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class AdminHealthController extends Controller
{
    public function services(Request $request)
    {
        $editService = null;
        if ($request->filled('edit')) {
            $editService = HealthService::find($request->query('edit'));
        }

        $services = HealthService::orderBy('service_id')->get();
        return view('admin.services', compact('services', 'editService'));
    }

    public function storeService(Request $request)
    {
        $validated = $request->validate([
            'service_name' => ['required', 'string', 'max:100'],
            'doctor_name' => ['nullable', 'string', 'max:100'],
            'practice_days' => ['nullable', 'string', 'max:100'],
            'time_slots' => ['nullable', 'string', 'max:150'],
            'daily_quota' => ['nullable', 'integer', 'min:1', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'icon' => ['nullable', 'string', 'max:20'],
            'card_color' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        HealthService::create([
            'service_name' => $validated['service_name'],
            'doctor_name' => ($validated['doctor_name'] ?? null) ?: 'drh. Clara Wijaya',
            'practice_days' => ($validated['practice_days'] ?? null) ?: 'Senin - Sabtu',
            'time_slots' => ($validated['time_slots'] ?? null) ?: '09:00, 13:00, 16:00',
            'daily_quota' => (int) (($validated['daily_quota'] ?? null) ?: 5),
            'price' => $validated['price'],
            'icon' => ($validated['icon'] ?? null) ?: '🩺',
            'card_color' => ($validated['card_color'] ?? null) ?: 'card-green',
            'description' => $validated['description'] ?? '',
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Layanan & jadwal kesehatan berhasil ditambahkan.');
    }

    public function updateService(Request $request, HealthService $service)
    {
        $validated = $request->validate([
            'service_name' => ['required', 'string', 'max:100'],
            'doctor_name' => ['nullable', 'string', 'max:100'],
            'practice_days' => ['nullable', 'string', 'max:100'],
            'time_slots' => ['nullable', 'string', 'max:150'],
            'daily_quota' => ['nullable', 'integer', 'min:1', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'icon' => ['nullable', 'string', 'max:20'],
            'card_color' => ['nullable', 'string', 'max:30'],
            'description' => ['nullable', 'string', 'max:255'],
        ]);

        $service->update([
            'service_name' => $validated['service_name'],
            'doctor_name' => ($validated['doctor_name'] ?? null) ?: $service->doctor_name,
            'practice_days' => ($validated['practice_days'] ?? null) ?: $service->practice_days,
            'time_slots' => ($validated['time_slots'] ?? null) ?: $service->time_slots,
            'daily_quota' => (int) (($validated['daily_quota'] ?? null) ?: $service->daily_quota),
            'price' => $validated['price'],
            'icon' => ($validated['icon'] ?? null) ?: $service->icon,
            'card_color' => ($validated['card_color'] ?? null) ?: $service->card_color,
            'description' => $validated['description'] ?? $service->description,
        ]);

        return redirect()->route('admin.services.index')->with('success', 'Layanan & jadwal kesehatan berhasil diperbarui.');
    }

    public function destroyService(HealthService $service)
    {
        try {
            if ($service->bookings()->exists()) {
                return redirect()->route('admin.services.index')->with(
                    'error',
                    'Layanan tidak dapat dihapus karena masih memiliki jadwal booking pelanggan.'
                );
            }
            $service->delete();
            return redirect()->route('admin.services.index')->with('success', 'Layanan kesehatan berhasil dihapus.');
        } catch (QueryException $e) {
            return redirect()->route('admin.services.index')->with(
                'error',
                'Layanan tidak dapat dihapus karena masih terhubung dengan data booking.'
            );
        }
    }

    public function bookings()
    {
        $bookings = HealthBooking::with(['service', 'user', 'pet'])
            ->orderByDesc('booking_date')
            ->get();

        return view('admin.bookings', compact('bookings'));
    }

    public function updateBookingStatus(Request $request, HealthBooking $booking)
    {
        $status = $request->input('status');
        if ($status === 'confirmed') {
            $status = 'approved';
            $request->merge(['status' => 'approved']);
        }

        $validated = $request->validate([
            'status' => ['required', 'in:pending,approved,completed,rejected'],
            'rejection_reason' => ['nullable', 'string', 'max:500'],
        ]);

        $booking->update([
            'status' => $validated['status'],
            'rejection_reason' => $validated['status'] === 'rejected'
                ? ($validated['rejection_reason'] ?? 'Jadwal penuh / tidak tersedia')
                : null,
        ]);

        return redirect()->route('admin.bookings.index')->with('success', 'Status booking kesehatan berhasil diperbarui.');
    }

    public function destroyBooking(HealthBooking $booking)
    {
        $booking->delete();
        return redirect()->route('admin.bookings.index')->with('success', 'Data booking kesehatan berhasil dihapus.');
    }
}
