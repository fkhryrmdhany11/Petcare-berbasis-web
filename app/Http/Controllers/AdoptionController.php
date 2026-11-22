<?php

namespace App\Http\Controllers;

use App\Models\AdoptionRequest;
use App\Models\Animal;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdoptionController extends Controller
{
    public function index()
    {
        $animals = Animal::orderBy('animal_id')->get();

        return view('adoption.index', compact('animals'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'animal_id' => ['required', 'integer', 'exists:animal,animal_id'],
            'applicant_name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:100'],
            'phone' => ['required', 'string', 'max:30'],
            'address' => ['required', 'string', 'max:500'],
            'reason' => ['required', 'string', 'max:1000'],
        ]);

        $animal = Animal::findOrFail($data['animal_id']);

        if ($animal->status !== 'available') {
            return back()->with('error', 'Maaf, hewan ini sudah tidak tersedia untuk diadopsi.');
        }

        AdoptionRequest::create([
            'user_id' => Auth::id(),
            'animal_id' => $animal->animal_id,
            'applicant_name' => trim($data['applicant_name']),
            'email' => trim($data['email']),
            'phone' => trim($data['phone']),
            'address' => trim($data['address']),
            'reason' => trim($data['reason']),
            'request_date' => now(),
            'status' => 'pending',
        ]);

        return redirect()->route('adoption.history')->with('success', "Pengajuan adopsi untuk {$animal->nama} berhasil dikirim! Menunggu verifikasi Admin.");
    }

    public function history()
    {
        $requests = AdoptionRequest::with('animal')
            ->where('user_id', Auth::id())
            ->orderByDesc('adoption_request_id')
            ->get();

        return view('adoption.history', compact('requests'));
    }

    public function cancel(AdoptionRequest $adoption)
    {
        if ($adoption->user_id !== Auth::id()) {
            abort(403);
        }

        if ($adoption->status !== 'pending') {
            return back()->with('error', 'Hanya pengajuan adopsi yang masih berstatus Menunggu Verifikasi (Pending) yang dapat dibatalkan.');
        }

        $adoption->update([
            'status' => 'cancelled',
            'admin_note' => 'Dibatalkan sendiri oleh pemohon.',
        ]);

        return redirect()->route('adoption.history')->with('success', 'Pengajuan adopsi berhasil dibatalkan.');
    }
}
