@extends('layouts.app')

@section('title', 'Adopsi Hewan | PawPaw Pet Care')

@section('content')
  <div class="adoption-head">
    <div>
      <h2>ADOPSI HEWAN</h2>
      <p>Temukan sahabat berbulu yang siap menjadi bagian dari keluargamu.</p>
    </div>
    @auth
      <a href="{{ route('adoption.history') }}" class="history-link">
        📋 Riwayat Adopsi Saya
      </a>
    @endauth
  </div>

  <div class="category-list" role="group" aria-label="Filter spesies hewan adopsi">
    <button class="category-btn active" data-category="semua" type="button" aria-pressed="true">
      <img src="{{ asset('assets/img/all.png') }}" alt="" aria-hidden="true"> Semua
    </button>
    <button class="category-btn" data-category="kucing" type="button" aria-pressed="false">
      <img src="{{ asset('assets/img/kucing.png') }}" alt="" aria-hidden="true"> Kucing
    </button>
    <button class="category-btn" data-category="anjing" type="button" aria-pressed="false">
      <img src="{{ asset('assets/img/dog.png') }}" alt="" aria-hidden="true"> Anjing
    </button>
    <button class="category-btn" data-category="kelinci" type="button" aria-pressed="false">
      <img src="{{ asset('assets/img/kelinci.png') }}" alt="" aria-hidden="true"> Kelinci
    </button>
    <button class="category-btn" data-category="lainnya" type="button" aria-pressed="false">
      <img src="{{ asset('assets/img/other.png') }}" alt="" aria-hidden="true"> Lainnya
    </button>
  </div>

  <div class="adoption-grid" id="adoptionGrid">
    @foreach($animals as $pet)
      @php($tersedia = $pet->status === 'available')
      <div class="pet-card" data-species="{{ strtolower($pet->species) }}">
        <img src="{{ asset($pet->image) }}" alt="{{ $pet->nama }} ({{ $pet->species }} - {{ $pet->breed }})" class="pet-image" loading="lazy">
        <div class="pet-info">
          <h3>{{ $pet->nama }}</h3>
          <p>Ras: {{ ucfirst(strtolower($pet->breed)) }}</p>
          <p>Gender: {{ $pet->gender }}</p>
          <p>Umur: {{ $pet->age }} tahun</p>
          @if($pet->description)
            <p class="small text-secondary">{{ $pet->description }}</p>
          @endif
          <p class="pet-status {{ $tersedia ? 'status-available' : 'status-unavailable' }}">
            Status: {{ $tersedia ? '✓ Tersedia' : ($pet->status === 'adopted' ? '✕ Sudah Diadopsi' : '✕ Tidak Tersedia') }}
          </p>

          @if($tersedia)
            @auth
              <button
                type="button"
                class="adoption-button"
                data-open-adopt
                data-animal-id="{{ $pet->animal_id }}"
                data-animal-name="{{ $pet->nama }} ({{ $pet->breed }})">
                Ajukan Adopsi
              </button>
            @else
              <a href="{{ route('login') }}" class="adoption-button text-center text-decoration-none">
                Login untuk Mengajukan Adopsi
              </a>
            @endauth
          @else
            <button class="adoption-button" disabled>Tidak Tersedia</button>
          @endif
        </div>
      </div>
    @endforeach
  </div>

  @auth
    <div class="modal-overlay" id="adoptionModal" role="dialog" aria-modal="true" aria-labelledby="adoptionModalTitle" aria-hidden="true">
      <div class="adoption-modal">
        <button class="modal-close" id="closeModal" type="button" aria-label="Tutup formulir pengajuan adopsi" title="Tutup (Esc)">&times;</button>
        <h2 id="adoptionModalTitle">Formulir Pengajuan Adopsi</h2>
        <p class="text-secondary mb-3">Hewan pilihan: <strong id="selectedPet">-</strong></p>

        <form action="{{ route('adoption.store') }}" method="POST">
          @csrf
          <input type="hidden" name="animal_id" id="modalAnimalId" value="{{ old('animal_id') }}">

          <div class="mb-3">
            <label for="adoptApplicantName" class="form-label fw-semibold">Nama Lengkap *</label>
            <input type="text" id="adoptApplicantName" class="form-control @error('applicant_name') is-invalid @enderror" name="applicant_name" value="{{ old('applicant_name', auth()->user()->name) }}" required>
            @error('applicant_name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="adoptEmail" class="form-label fw-semibold">Email *</label>
            <input type="email" id="adoptEmail" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', auth()->user()->email) }}" required>
            @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="adoptPhone" class="form-label fw-semibold">Nomor Telepon / WhatsApp *</label>
            <input type="text" id="adoptPhone" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', auth()->user()->phone) }}" required>
            @error('phone')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="adoptAddress" class="form-label fw-semibold">Alamat Lengkap *</label>
            <textarea id="adoptAddress" class="form-control @error('address') is-invalid @enderror" name="address" rows="2" required>{{ old('address', auth()->user()->address) }}</textarea>
            @error('address')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="mb-3">
            <label for="adoptReason" class="form-label fw-semibold">Alasan Mengadopsi &amp; Kesiapan Merawat *</label>
            <textarea id="adoptReason" class="form-control @error('reason') is-invalid @enderror" name="reason" rows="3" placeholder="Ceritakan pengalaman atau kesiapan Anda merawat hewan ini..." required>{{ old('reason') }}</textarea>
            @error('reason')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <button type="submit" class="btn-pawpaw w-100 py-2">Kirim Pengajuan Adopsi <span aria-hidden="true">🐾</span></button>
        </form>
      </div>
    </div>
  @endauth
@endsection
