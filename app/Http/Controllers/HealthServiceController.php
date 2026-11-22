<?php

namespace App\Http\Controllers;

use App\Models\HealthBooking;
use App\Models\HealthService;
use App\Models\Pet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HealthServiceController extends Controller
{
    public function index()
    {
        $services = HealthService::orderBy('service_id')->get();
        $bookings = collect();
        $userPets = collect();

        if (Auth::check()) {
            $bookings = HealthBooking::with(['service', 'pet'])
                ->where('user_id', Auth::id())
                ->orderByDesc('booking_date')
                ->get();
            $userPets = Pet::where('user_id', Auth::id())->get();
        }

        return view('health.index', compact('services', 'bookings', 'userPets'));
    }

    public function storeBooking(Request $request)
    {
        $data = $request->validate([
            'service_id' => ['required', 'integer', 'exists:health_service,service_id'],
            'owner_name' => ['required', 'string', 'max:100'],
            'pet_name' => ['required', 'string', 'max:100'],
            'pet_species' => ['required', 'string', 'max:50'],
            'booking_date' => ['required', 'date'],
            'booking_time' => ['nullable', 'string', 'max:20'],
        ]);

        $service = HealthService::findOrFail((int) $data['service_id']);
        $dateOnly = \Carbon\Carbon::parse($data['booking_date'])->format('Y-m-d');

        if (!empty($data['booking_time'])) {
            $timeSlot = trim($data['booking_time']);
            $finalDateTime = \Carbon\Carbon::parse("{$dateOnly} {$timeSlot}");
        } else {
            $parsed = \Carbon\Carbon::parse($data['booking_date']);
            $defaultSlot = $service->time_slots_array[0] ?? '09:00';
            $finalDateTime = $parsed->format('H:i') !== '00:00'
                ? $parsed
                : \Carbon\Carbon::parse("{$dateOnly} {$defaultSlot}");
        }

        // Cek kuota slot pada tanggal & jam tersebut
        $activeBookingsInSlot = HealthBooking::where('service_id', $service->service_id)
            ->where('booking_date', $finalDateTime->format('Y-m-d H:i:s'))
            ->whereIn('status', ['pending', 'approved'])
            ->count();

        if ($activeBookingsInSlot >= max(1, (int) $service->daily_quota)) {
            return back()->withInput()->with('error', "Maaf, kuota untuk layanan {$service->service_name} pada sesi jam tersebut sudah penuh ({$service->daily_quota} pasien). Silakan pilih jam atau tanggal lain.");
        }

        $user = Auth::user();

        $pet = Pet::firstOrCreate(
            [
                'user_id' => $user->user_id,
                'pet_name' => trim($data['pet_name']),
            ],
            [
                'species' => trim($data['pet_species']),
                'breed' => 'Tidak diketahui',
                'gender' => 'Tidak diketahui',
                'age' => '1 Tahun',
                'health_status' => 'Pemeriksaan',
            ]
        );

        HealthBooking::create([
            'user_id' => $user->user_id,
            'pet_id' => $pet->pet_id,
            'service_id' => $service->service_id,
            'owner_name' => trim($data['owner_name']),
            'pet_name' => trim($data['pet_name']),
            'pet_species' => trim($data['pet_species']),
            'booking_date' => $finalDateTime,
            'status' => 'pending',
        ]);

        return redirect()->route('health.index')->with('success', "Booking jadwal {$service->service_name} bersama {$service->doctor_name} pada " . $finalDateTime->format('d M Y, H:i') . " WIB berhasil disimpan!");
    }

    public function bookingCard(HealthBooking $booking)
    {
        if ($booking->user_id !== Auth::id() && ! Auth::user()->isAdmin()) {
            abort(403);
        }

        $booking->load(['service', 'pet', 'user']);
        return view('health.card', compact('booking'));
    }

    public function cancelBooking(HealthBooking $booking)
    {
        if ($booking->user_id !== Auth::id()) {
            abort(403);
        }

        if ($booking->status !== 'pending') {
            return back()->with('error', 'Hanya jadwal booking yang masih berstatus Menunggu Konfirmasi (Pending) yang dapat dibatalkan.');
        }

        $booking->update([
            'status' => 'cancelled',
            'rejection_reason' => 'Dibatalkan oleh pemilik hewan.',
        ]);

        return redirect()->route('health.index')->with('success', 'Jadwal booking layanan kesehatan berhasil dibatalkan.');
    }
}
