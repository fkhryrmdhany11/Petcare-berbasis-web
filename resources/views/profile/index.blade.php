@extends('layouts.app')

@section('title', 'Profil Saya | PawPaw Pet Care')
@section('hide_sidebar', 'true')

@section('content')
  <div class="paw-profile-container">
    <!-- Top Navigation Bar untuk Halaman Profil Tanpa Sidebar -->
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
      <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('home') }}" class="btn btn-outline-primary rounded-pill px-3 fw-bold">
          ← Kembali ke Beranda
        </a>
        <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
          🛍️ Toko
        </a>
        <a href="{{ route('adoption.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
          🐾 Adopsi
        </a>
        <a href="{{ route('health.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
          🩺 Kesehatan
        </a>
      </div>

      <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-danger rounded-pill px-3 fw-bold">
          🚪 Keluar Akun
        </button>
      </form>
    </div>

    <header class="paw-header">
      <div class="paw-header-text">
        <h1>Profil Saya</h1>
        <p>Halaman profil mandiri tanpa sidebar — kelola akun, foto profil, hewan peliharaan, dan riwayat aktivitas Anda.</p>
      </div>
      <div class="paw-header-icon">🐱</div>
    </header>

    <!-- Kartu Utama Profil -->
    <section class="paw-card paw-main-profile">
      <img
        src="{{ $user->avatar_url }}"
        alt="{{ $user->name }}"
        class="paw-avatar">

      <div class="flex-grow-1">
        <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
          <h2 class="h4 mb-0">{{ $user->name }}</h2>
          <span class="paw-badge">{{ $user->isAdmin() ? 'Administrator' : 'Pet Lover' }}</span>
        </div>
        <p class="text-secondary mb-1">{{ '@' . $user->username }} • {{ $user->email }}</p>
        <p class="mb-3">{{ $user->bio ?: 'Cat lover & proud pet parent ♡' }}</p>
        <div class="d-flex flex-wrap gap-2">
          <button class="paw-btn-edit" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEditProfile">
            ✏️ Edit Profil & Foto
          </button>
          <button class="btn btn-outline-secondary btn-sm rounded-pill px-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseChangePassword">
            🔑 Ubah Kata Sandi
          </button>
        </div>
      </div>
    </section>

    <!-- Collapse Edit Profil -->
    <div class="collapse mb-4" id="collapseEditProfile">
      <section class="paw-card">
        <h3 class="paw-section-title">✏️ Perbarui Informasi Pribadi & Foto Profil</h3>
        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="row g-3">
          @csrf
          @method('PUT')
          <div class="col-md-6">
            <label for="profName" class="form-label fw-semibold">Nama Lengkap *</label>
            <input type="text" id="profName" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name', $user->name) }}" required>
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6">
            <label for="profUsername" class="form-label fw-semibold">Username *</label>
            <input type="text" id="profUsername" class="form-control @error('username') is-invalid @enderror" name="username" value="{{ old('username', $user->username) }}" required>
            @error('username')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6">
            <label for="profEmail" class="form-label fw-semibold">Email *</label>
            <input type="email" id="profEmail" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $user->email) }}" required>
            @error('email')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6">
            <label for="profPhone" class="form-label fw-semibold">Nomor Telepon</label>
            <input type="text" id="profPhone" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $user->phone) }}">
            @error('phone')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6">
            <label for="profBio" class="form-label fw-semibold">Bio Singkat</label>
            <input type="text" id="profBio" class="form-control @error('bio') is-invalid @enderror" name="bio" value="{{ old('bio', $user->bio) }}">
            @error('bio')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6">
            <label for="profAvatar" class="form-label fw-semibold">Upload Foto Avatar Baru (JPG/PNG/WEBP)</label>
            <input type="file" id="profAvatar" class="form-control @error('avatar_file') is-invalid @enderror" name="avatar_file" accept="image/*">
            @error('avatar_file')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-12">
            <label for="profAddress" class="form-label fw-semibold">Alamat Lengkap</label>
            <input type="text" id="profAddress" class="form-control @error('address') is-invalid @enderror" name="address" value="{{ old('address', $user->address) }}">
            @error('address')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-12">
            <button type="submit" class="btn-pawpaw">Simpan Perubahan Profil</button>
          </div>
        </form>
      </section>
    </div>

    <!-- Collapse Ubah Password -->
    <div class="collapse mb-4" id="collapseChangePassword">
      <section class="paw-card">
        <h3 class="paw-section-title">🔑 Ubah Kata Sandi</h3>
        <form action="{{ route('profile.password') }}" method="POST" class="row g-3">
          @csrf
          @method('PUT')
          <div class="col-md-4">
            <label class="form-label fw-semibold">Kata Sandi Saat Ini *</label>
            <input type="password" class="form-control" name="current_password" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Kata Sandi Baru *</label>
            <input type="password" class="form-control" name="password" required>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-semibold">Konfirmasi Kata Sandi Baru *</label>
            <input type="password" class="form-control" name="password_confirmation" required>
          </div>
          <div class="col-12">
            <button type="submit" class="btn-pawpaw">Perbarui Kata Sandi</button>
          </div>
        </form>
      </section>
    </div>

    <!-- Informasi Pribadi -->
    <section class="paw-card">
      <h3 class="paw-section-title">📄 Informasi Pribadi</h3>
      <div class="paw-info-grid">
        <div class="paw-info-item">
          <div class="paw-info-icon">👤</div>
          <div>
            <small class="text-secondary d-block">Nama Lengkap</small>
            <strong>{{ $user->name }}</strong>
          </div>
        </div>
        <div class="paw-info-item">
          <div class="paw-info-icon">🆔</div>
          <div>
            <small class="text-secondary d-block">Username</small>
            <strong>{{ '@' . $user->username }}</strong>
          </div>
        </div>
        <div class="paw-info-item">
          <div class="paw-info-icon">✉️</div>
          <div>
            <small class="text-secondary d-block">Email</small>
            <strong>{{ $user->email }}</strong>
          </div>
        </div>
        <div class="paw-info-item">
          <div class="paw-info-icon">📱</div>
          <div>
            <small class="text-secondary d-block">Nomor Telepon</small>
            <strong>{{ $user->phone ?: '-' }}</strong>
          </div>
        </div>
        <div class="paw-info-item">
          <div class="paw-info-icon">📍</div>
          <div>
            <small class="text-secondary d-block">Alamat</small>
            <strong>{{ $user->address ?: '-' }}</strong>
          </div>
        </div>
        <div class="paw-info-item">
          <div class="paw-info-icon">🗓️</div>
          <div>
            <small class="text-secondary d-block">Tanggal Bergabung</small>
            <strong>{{ $user->created_at?->format('d F Y') ?? '2026' }}</strong>
          </div>
        </div>
      </div>
    </section>

    <!-- Hewan Peliharaanku -->
    <section class="paw-card">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h3 class="paw-section-title mb-0">🐾 Hewan Peliharaanku ({{ $pets->count() }})</h3>
        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAddPet">
          + Tambah Hewan Peliharaan
        </button>
      </div>

      <div class="collapse mb-3" id="collapseAddPet">
        <form action="{{ route('profile.pets.store') }}" method="POST" class="row g-3 p-3 bg-light rounded-4 border">
          @csrf
          <div class="col-md-4">
            <label class="form-label small fw-bold">Nama Hewan *</label>
            <input type="text" class="form-control" name="pet_name" placeholder="Contoh: Mochi" required>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Spesies *</label>
            <select class="form-select" name="species" required>
              <option value="Kucing">Kucing</option>
              <option value="Anjing">Anjing</option>
              <option value="Kelinci">Kelinci</option>
              <option value="Lainnya">Lainnya</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Ras / Breed</label>
            <input type="text" class="form-control" name="breed" placeholder="Contoh: Persia / Anggora">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Jenis Kelamin</label>
            <select class="form-select" name="gender">
              <option value="Betina">Betina</option>
              <option value="Jantan">Jantan</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Umur</label>
            <input type="text" class="form-control" name="age" placeholder="Contoh: 1 Tahun">
          </div>
          <div class="col-md-4">
            <label class="form-label small fw-bold">Status Kesehatan</label>
            <input type="text" class="form-control" name="health_status" value="Sehat">
          </div>
          <div class="col-12">
            <button type="submit" class="btn-pawpaw">Simpan Hewan</button>
          </div>
        </form>
      </div>

      <div class="paw-pets-grid">
        @forelse($pets as $pet)
          <div class="paw-pet-card">
            <div class="fs-1 mb-2">{{ strtolower($pet->species) === 'anjing' ? '🐶' : (strtolower($pet->species) === 'kelinci' ? '🐰' : '🐱') }}</div>
            <h4 class="h6 fw-bold mb-1">{{ $pet->pet_name }}</h4>
            <p class="small text-secondary mb-2">{{ $pet->species }} ({{ $pet->breed }}) • {{ $pet->age }}</p>
            <span class="paw-pet-status mb-2">{{ $pet->health_status }}</span>
            <form action="{{ route('profile.pets.destroy', $pet->pet_id) }}" method="POST" class="mt-2">
              @csrf
              @method('DELETE')
              <button type="submit" class="btn btn-link text-danger btn-sm p-0 text-decoration-none">Hapus</button>
            </form>
          </div>
        @empty
          <p class="text-secondary mb-0">Belum ada hewan peliharaan terdaftar.</p>
        @endforelse
      </div>
    </section>

    <!-- Ringkasan Aktivitas Real-Time -->
    <section class="paw-card">
      <h3 class="paw-section-title">📊 Ringkasan Aktivitas Database</h3>
      <div class="paw-stats-grid">
        <a href="{{ route('orders.history') }}" class="paw-stat-card paw-bg-pink text-decoration-none text-dark">
          <div class="paw-stat-icon">🛍️</div>
          <div>
            <h3 class="h4 fw-bold mb-0">{{ $ordersCount }}</h3>
            <p class="small mb-0">Pesanan Toko</p>
          </div>
        </a>
        <a href="{{ route('health.index') }}#jadwal-saya" class="paw-stat-card paw-bg-blue text-decoration-none text-dark">
          <div class="paw-stat-icon">🩺</div>
          <div>
            <h3 class="h4 fw-bold mb-0">{{ $bookingsCount }}</h3>
            <p class="small mb-0">Janji Kesehatan</p>
          </div>
        </a>
        <a href="{{ route('adoption.history') }}" class="paw-stat-card paw-bg-lavender text-decoration-none text-dark">
          <div class="paw-stat-icon">🏠</div>
          <div>
            <h3 class="h4 fw-bold mb-0">{{ $adoptionsCount }}</h3>
            <p class="small mb-0">Pengajuan Adopsi</p>
          </div>
        </a>
      </div>
    </section>

    <!-- Aktivitas Terbaru -->
    <section class="paw-card">
      <h3 class="paw-section-title">🕒 Aktivitas Terbaru Saya</h3>
      @if($activities->isEmpty())
        <p class="text-secondary mb-0">Belum ada aktivitas transaksi tercatat.</p>
      @else
        <div class="paw-activity-list">
          @foreach($activities as $act)
            <div class="paw-activity-item">
              <div class="paw-activity-icon">{{ $act['icon'] }}</div>
              <div class="paw-activity-text">
                <h4 class="h6 fw-bold mb-1">{{ $act['title'] }}</h4>
                <p class="small text-secondary mb-0">{{ $act['desc'] }}</p>
              </div>
              <div class="small text-secondary">
                {{ \Carbon\Carbon::parse($act['date'])->format('d M Y') }}
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </section>
  </div>
@endsection
