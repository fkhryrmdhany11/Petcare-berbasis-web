@extends('layouts.app')

@section('title', 'Layanan Kesehatan | PawPaw Pet Care')

@section('content')
  <div class="health-main">
    <h1 class="header-title">LAYANAN KESEHATAN HEWAN</h1>

    <section class="section">
      <h2 class="section-title">Pilihan Layanan Klinik &amp; Jadwal Praktik Dokter</h2>
      <div class="cards-grid">
        @foreach($services as $svc)
          <div class="service-card {{ $svc->card_color ?: 'card-green' }}">
            <div class="card-icon">{{ $svc->icon ?: '🩺' }}</div>
            <h3 class="card-title">{{ $svc->service_name }}</h3>
            <p class="small fw-bold mb-1">👨‍⚕️ {{ $svc->doctor_name }} • 🗓️ {{ $svc->practice_days }}</p>
            <p class="mb-2">{{ $svc->description ?: 'Layanan pemeriksaan profesional dokter hewan PawPaw.' }}</p>
            <div class="d-flex flex-wrap justify-content-center gap-1 mb-2">
              @foreach($svc->time_slots_array as $slot)
                <span class="badge bg-white text-dark border px-2 py-1">🕒 {{ $slot }} WIB</span>
              @endforeach
            </div>
            <div class="small text-secondary mb-2">Kuota: <strong>{{ $svc->daily_quota }} pasien / sesi</strong></div>
            <div class="card-price">Rp {{ number_format((float) $svc->price, 0, ',', '.') }}</div>
          </div>
        @endforeach
      </div>
    </section>

    <section class="section" id="booking-section">
      <h2 class="section-title">Form Booking Jadwal Layanan Kesehatan</h2>
      @auth
        @php
          $firstServiceSlots = $services->first()?->time_slots_array ?? ['09:00', '13:00', '16:00'];
        @endphp
        <form action="{{ route('health.book') }}" method="POST" class="booking-form">
          @csrf
          <div class="booking-grid">
            <label>
              Nama Pemilik *
              <input type="text" name="owner_name" value="{{ old('owner_name', auth()->user()->name) }}" placeholder="Masukkan nama Anda" required>
            </label>

            <label>
              Nama Hewan *
              <input type="text" name="pet_name" list="userPetsList" value="{{ old('pet_name') }}" placeholder="Ketik atau pilih hewan peliharaan" required>
              <datalist id="userPetsList">
                @foreach($userPets as $up)
                  <option value="{{ $up->pet_name }}">{{ $up->species }} - {{ $up->breed }}</option>
                @endforeach
              </datalist>
            </label>

            <label>
              Jenis Hewan *
              <select name="pet_species" required>
                <option value="Kucing" {{ old('pet_species') === 'Kucing' ? 'selected' : '' }}>Kucing</option>
                <option value="Anjing" {{ old('pet_species') === 'Anjing' ? 'selected' : '' }}>Anjing</option>
                <option value="Kelinci" {{ old('pet_species') === 'Kelinci' ? 'selected' : '' }}>Kelinci</option>
                <option value="Lainnya" {{ old('pet_species') === 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
              </select>
            </label>

            <label>
              Pilih Layanan &amp; Dokter *
              <select name="service_id" id="healthServiceSelect" required>
                @foreach($services as $svc)
                  <option
                    value="{{ $svc->service_id }}"
                    data-slots="{{ implode(',', $svc->time_slots_array) }}"
                    {{ (int) old('service_id') === $svc->service_id ? 'selected' : '' }}
                  >
                    {{ $svc->service_name }} — {{ $svc->doctor_name }} (Rp {{ number_format((float) $svc->price, 0, ',', '.') }})
                  </option>
                @endforeach
              </select>
            </label>

            <label>
              Tanggal Kunjungan *
              <input type="date" name="booking_date" min="{{ now()->toDateString() }}" value="{{ old('booking_date', now()->addDay()->toDateString()) }}" required>
            </label>

            <label>
              Pilih Sesi Jam Praktik *
              <select name="booking_time" id="healthTimeSlotSelect" required>
                @foreach($firstServiceSlots as $slot)
                  <option value="{{ $slot }}" {{ old('booking_time') === $slot ? 'selected' : '' }}>
                    🕒 Sesi {{ $slot }} WIB
                  </option>
                @endforeach
              </select>
            </label>
          </div>

          <button type="submit" class="booking-submit">Kirim Booking Jadwal 🐾</button>
        </form>
      @else
        <div class="booking-form text-center py-4">
          <p class="mb-3">Silakan masuk ke akun Anda terlebih dahulu untuk memilih jadwal pemeriksaan atau vaksinasi.</p>
          <a href="{{ route('login') }}" class="btn-pawpaw">Login / Daftar Sekarang</a>
        </div>
      @endauth
    </section>

    <section class="section" id="jadwal-saya">
      <h2 class="section-title">Riwayat &amp; Jadwal Appointment Saya</h2>
      @php
        $statusLabels = [
          'pending' => 'Menunggu Konfirmasi',
          'approved' => 'Disetujui',
          'completed' => 'Selesai',
          'rejected' => 'Ditolak',
          'cancelled' => 'Dibatalkan',
        ];
      @endphp

      @guest
        <p class="text-secondary">Login untuk melihat riwayat jadwal layanan kesehatan hewan Anda.</p>
      @else
        @if($bookings->isEmpty())
          <div class="shop-panel text-center py-4 text-secondary">
            Belum ada riwayat booking layanan kesehatan di database.
          </div>
        @else
          <div class="appointment-container">
            @foreach($bookings as $b)
              <article class="appointment-card">
                <div class="pet-icon-wrapper">🐾</div>
                <div class="appt-info">
                  <p><strong>Nama Hewan:</strong> {{ $b->pet_name }} ({{ $b->pet_species }})</p>
                  <p><strong>Layanan:</strong> {{ $b->service?->service_name ?? '-' }} ({{ $b->service?->doctor_name ?? 'Dokter Klinik' }}) — Rp {{ number_format((float) ($b->service?->price ?? 0), 0, ',', '.') }}</p>
                  @if($b->status === 'rejected' && $b->rejection_reason)
                    <p class="text-danger small mt-1"><strong>Alasan Ditolak:</strong> {{ $b->rejection_reason }}</p>
                  @endif
                </div>
                <div class="appt-time">
                  <p class="fw-bold small text-secondary">Jadwal Kunjungan</p>
                  <p class="fw-semibold">{{ $b->booking_date?->format('d F Y, H:i') }} WIB</p>
                </div>
                <div class="d-flex flex-column align-items-end gap-2">
                  <span class="order-status {{ $b->status }}">
                    {{ $statusLabels[$b->status] ?? ucfirst($b->status) }}
                  </span>
                  <div class="d-flex gap-1">
                    <a href="{{ route('health.card', $b->booking_id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-2">
                      🖨️ Kartu
                    </a>
                    @if($b->status === 'pending')
                      <form action="{{ route('health.cancel', $b->booking_id) }}" method="POST" onsubmit="return confirm('Batalkan jadwal kunjungan klinik ini?');">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2">
                          ✕ Batal
                        </button>
                      </form>
                    @endif
                  </div>
                </div>
              </article>
            @endforeach
          </div>
        @endif
      @endguest
    </section>
  </div>
@endsection
