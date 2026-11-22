@extends('layouts.app')

@section('title', 'Kartu Jadwal Klinik #' . $booking->booking_id . ' | PawPaw Pet Care')
@section('hide_sidebar', 'true')

@section('content')
  <div class="print-sheet">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
      <a href="{{ route('health.index') }}#jadwal-saya" class="btn btn-outline-secondary rounded-pill px-3">
        ← Kembali ke Layanan Kesehatan
      </a>
      <button type="button" onclick="window.print()" class="btn btn-primary rounded-pill px-4 fw-bold">
        🖨️ Cetak Kartu Jadwal
      </button>
    </div>

    <div class="d-flex justify-content-between align-items-start flex-wrap border-bottom pb-3 mb-4 gap-3">
      <div class="d-flex align-items-center gap-3">
        <img src="{{ asset('assets/img/logo.png') }}" alt="PawPaw Logo" style="width:64px;height:64px;object-fit:contain;">
        <div>
          <h2 class="h4 fw-bold mb-0">KLINIK KESEHATAN PAWPAW</h2>
          <p class="small text-secondary mb-0">Kartu Bukti Reservasi Pemeriksaan &amp; Vaksinasi Hewan</p>
        </div>
      </div>
      <div class="text-md-end">
        <span class="badge bg-info-subtle text-info-emphasis px-3 py-2 rounded-pill fw-bold mb-1">
          BOOKING #{{ str_pad((string) $booking->booking_id, 4, '0', STR_PAD_LEFT) }}
        </span>
        <div class="fw-bold text-uppercase">{{ $booking->status }}</div>
      </div>
    </div>

    <div class="row g-4 mb-4">
      <div class="col-sm-6">
        <div class="p-3 bg-light rounded-4 h-100">
          <small class="text-secondary d-block mb-1">Pemilik &amp; Pasien Hewan</small>
          <div class="fw-bold fs-5">{{ $booking->pet_name }} ({{ $booking->pet_species }})</div>
          <div class="text-secondary">Pemilik: <strong>{{ $booking->owner_name }}</strong></div>
        </div>
      </div>
      <div class="col-sm-6">
        <div class="p-3 bg-light rounded-4 h-100">
          <small class="text-secondary d-block mb-1">Layanan &amp; Jadwal Kunjungan</small>
          <div class="fw-bold fs-5">{{ $booking->service?->service_name ?? 'Layanan Klinik' }}</div>
          <div class="text-primary fw-semibold">{{ $booking->booking_date?->format('d F Y, H:i') }} WIB</div>
          <div class="small text-secondary mt-1">Estimasi Biaya: Rp {{ number_format((float) ($booking->service?->price ?? 0), 0, ',', '.') }}</div>
        </div>
      </div>
    </div>

    <div class="p-3 border rounded-4 small text-secondary">
      <strong>Catatan Kunjungan:</strong> Harap tunjukkan kartu jadwal ini kepada petugas resepsionis Klinik PawPaw Pet Care 15 menit sebelum jadwal pemeriksaan dimulai.
    </div>
  </div>
@endsection
