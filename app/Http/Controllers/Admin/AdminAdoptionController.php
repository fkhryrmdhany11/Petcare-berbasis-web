<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdoptionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAdoptionController extends Controller
{
    public function index()
    {
        $adoptions = AdoptionRequest::with(['animal', 'user'])
            ->orderByDesc('request_date')
            ->get();

        return view('admin.adoptions', compact('adoptions'));
    }

    public function show(AdoptionRequest $adoption)
    {
        $adoption->load(['animal', 'user']);
        return view('admin.adoption-detail', compact('adoption'));
    }

    public function updateStatus(Request $request, AdoptionRequest $adoption)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,approved,rejected'],
            'admin_note' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($adoption, $validated) {
            $adoption->update([
                'status' => $validated['status'],
                'admin_note' => $validated['admin_note'] ?? $adoption->admin_note,
            ]);

            if ($validated['status'] === 'approved' && $adoption->animal) {
                $adoption->animal->update(['status' => 'adopted']);

                AdoptionRequest::where('animal_id', $adoption->animal_id)
                    ->where('adoption_request_id', '!=', $adoption->adoption_request_id)
                    ->where('status', 'pending')
                    ->update([
                        'status' => 'rejected',
                        'admin_note' => 'Hewan telah disetujui untuk diadopsi oleh pemohon lain.',
                    ]);
            }
        });

        return redirect()->route('admin.adoptions.index')->with('success', 'Status pengajuan adopsi berhasil diperbarui.');
    }
}
