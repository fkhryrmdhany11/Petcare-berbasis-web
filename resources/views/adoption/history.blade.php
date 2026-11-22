@extends('layouts.app')

@section('title', 'Riwayat Adopsi | PawPaw Pet Care')

@section('content')
  <div class="history-head">
    <div>
      <h2>RIWAYAT ADOPSI SAYA</h2>
      <p class="text-secondary mb-0">Daftar pengajuan adopsi yang pernah kamu kirimkan ke database PawPaw.</p>
    </div>
    <a href="{{ route('adoption.index') }}" class="back-adoption">← Kembali ke Katalog Adopsi</a>
  </div>

  @php
    $badgeClass = [
      'pending' => 'status-waiting',
      'approved' => 'status-success',
      'rejected' => 'status-failed',
      'cancelled' => 'status-failed',
    ];
    $badgeLabel = [
      'pending' => 'Menunggu Verifikasi',
      'approved' => 'Disetujui 🎉',
      'rejected' => 'Ditolak',
      'cancelled' => 'Dibatalkan',
    ];
  @endphp

  @if($requests->isEmpty())
    <div class="shop-panel text-center py-5">
      <span class="display-5">🐾</span>
      <h3 class="h5 mt-3">Belum ada riwayat pengajuan adopsi</h3>
      <p class="text-secondary">Pilih sahabat bulu di halaman Adopsi untuk mengajukan permohonan.</p>
      <a href="{{ route('adoption.index') }}" class="btn-pawpaw mt-2">Lihat Hewan Adopsi</a>
    </div>
  @else
    <div class="history-list">
      @foreach($requests as $req)
        <div class="history-card">
          <img src="{{ asset($req->animal?->image ?? 'assets/img/meng.png') }}" alt="{{ $req->animal?->nama }}" class="history-image">
          <div class="history-info">
            <h3 class="mb-1">{{ $req->animal?->nama ?? 'Hewan' }}</h3>
            <p class="mb-1 text-secondary small">
              Spesies: {{ ucfirst($req->animal?->species ?? '-') }} • Ras: {{ $req->animal?->breed ?? '-' }} • Gender: {{ $req->animal?->gender ?? '-' }}
            </p>
            <p class="mb-1 small"><strong>Alasan Adopsi:</strong> {{ $req->reason }}</p>
            @if($req->admin_note)
              <p class="mb-1 small text-primary"><strong>Catatan Admin:</strong> {{ $req->admin_note }}</p>
            @endif
            <p class="small text-muted mb-0">Diajukan pada: {{ $req->request_date?->format('d M Y, H:i') }} WIB</p>
          </div>
          <div class="d-flex flex-column align-items-end gap-2">
            <span class="history-status {{ $badgeClass[$req->status] ?? 'status-waiting' }}">
              {{ $badgeLabel[$req->status] ?? ucfirst($req->status) }}
            </span>
            @if($req->status === 'pending')
              <form action="{{ route('adoption.cancel', $req->adoption_request_id) }}" method="POST" onsubmit="return confirm('Batalkan pengajuan adopsi ini?');">
                @csrf
                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                  ✕ Batalkan
                </button>
              </form>
            @endif
          </div>
        </div>
      @endforeach
    </div>
  @endif
@endsection
