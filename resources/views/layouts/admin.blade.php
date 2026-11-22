<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'Admin Panel | PawPaw Pet Care')</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="{{ asset('css/admin.css') }}?v=2">
</head>
<body>
  <div class="sb-layout" id="sbLayout">
    <div class="sb-overlay" id="sbOverlay"></div>

    <aside class="sb-sidebar" id="sbSidebar">
      <button type="button" class="sb-brand" id="adminSidebarToggle" title="Buka/kecilkan sidebar">
        <div class="sb-brand-logo">
          <img src="{{ asset('assets/img/logo.png') }}" alt="Logo PawPaw Pet Care">
        </div>
        <span class="sb-label">PawPaw Admin</span>
      </button>

      <nav class="sb-nav">
        <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard', 'admin.index') ? 'active' : '' }}" title="Dashboard">
          <span class="sb-icon">📊</span>
          <span class="sb-label">Dashboard</span>
        </a>
        <a href="{{ route('admin.products.index') }}" class="{{ request()->routeIs('admin.products.*') ? 'active' : '' }}" title="Produk Toko">
          <span class="sb-icon">🛍️</span>
          <span class="sb-label">Produk Toko</span>
        </a>
        <a href="{{ route('admin.orders.index') }}" class="{{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" title="Pesanan Toko">
          <span class="sb-icon">📦</span>
          <span class="sb-label">Pesanan Toko</span>
        </a>
        <a href="{{ route('admin.animals.index') }}" class="{{ request()->routeIs('admin.animals.*') ? 'active' : '' }}" title="Hewan Adopsi">
          <span class="sb-icon">🐾</span>
          <span class="sb-label">Hewan Adopsi</span>
        </a>
        <a href="{{ route('admin.adoptions.index') }}" class="{{ request()->routeIs('admin.adoptions.*') ? 'active' : '' }}" title="Pengajuan Adopsi">
          <span class="sb-icon">📋</span>
          <span class="sb-label">Pengajuan Adopsi</span>
        </a>
        <a href="{{ route('admin.services.index') }}" class="{{ request()->routeIs('admin.services.*') ? 'active' : '' }}" title="Layanan Kesehatan">
          <span class="sb-icon">🩺</span>
          <span class="sb-label">Layanan Kesehatan</span>
        </a>
        <a href="{{ route('admin.bookings.index') }}" class="{{ request()->routeIs('admin.bookings.*') ? 'active' : '' }}" title="Booking Kesehatan">
          <span class="sb-icon">📅</span>
          <span class="sb-label">Booking Kesehatan</span>
        </a>
      </nav>

      <div class="sb-footer">
        <a href="{{ route('home') }}" title="Lihat Halaman Pelanggan">
          <span class="sb-icon">🏠</span>
          <span class="sb-label">Halaman Pelanggan</span>
        </a>
        <form action="{{ route('logout') }}" method="POST">
          @csrf
          <button type="submit" title="Keluar ({{ auth()->user()->username }})">
            <span class="sb-icon">↩</span>
            <span class="sb-label">Keluar ({{ auth()->user()->username }})</span>
          </button>
        </form>
      </div>
    </aside>

    <main class="admin-main">
      <header class="admin-topbar">
        <div class="d-flex align-items-center gap-3">
          <button type="button" class="sb-menu-button" id="sbMenuButton" aria-label="Buka menu">☰</button>
          <div>
            <h1>@yield('heading', 'Dashboard Admin')</h1>
            <p>@yield('subheading', 'Kelola seluruh operasional PawPaw Pet Care langsung dari database pet_care (phpMyAdmin).')</p>
          </div>
        </div>
        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold">
          🗄️ Database: pet_care (phpMyAdmin)
        </span>
      </header>

      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          ✅ {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
          ⚠️ {{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger">
          <ul class="mb-0 ps-3">
            @foreach($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @yield('content')
    </main>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/admin.js') }}"></script>
</body>
</html>
