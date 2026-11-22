<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>@yield('title', 'PawPaw Pet Care')</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap">
  <link rel="stylesheet" href="{{ asset('css/app.css') }}?v=2">
</head>
<body>
  @php
    $cartItems = session('cart', []);
    $cartTotalQty = is_array($cartItems) ? array_sum($cartItems) : 0;
    $hideSidebar = View::hasSection('hide_sidebar');
  @endphp

  <div class="app {{ $hideSidebar ? 'app-no-sidebar' : '' }}" id="app">
    <div class="marquee">
      <span><span aria-hidden="true">🐾</span> 50+ Adopsi Setiap Bulan • <span aria-hidden="true">💉</span> Jadwalkan Vaksin Kapan Saja • <span aria-hidden="true">🛍️</span> Semua Item Toko Diskon 25% <span aria-hidden="true">🐾</span></span>
    </div>

    <!-- Top-Right Action Bar (Cart Badge + Profile Avatar Dropdown) -->
    <div class="top-user-bar" role="region" aria-label="Aksi Cepat Pelanggan">
      <a href="{{ route('cart.index') }}" class="top-cart-btn" aria-label="Keranjang Belanja ({{ $cartTotalQty }} item)" title="Keranjang Belanja">
        <span aria-hidden="true">🛒</span>
        @if($cartTotalQty > 0)
          <span class="top-cart-badge" aria-live="polite">{{ $cartTotalQty }}</span>
        @endif
      </a>

      @auth
        <div class="dropdown">
          <button
            class="top-profile-btn dropdown-toggle"
            type="button"
            data-bs-toggle="dropdown"
            aria-expanded="false"
            aria-label="Menu Profil {{ auth()->user()->name }}"
            title="Menu Profil ({{ auth()->user()->name }})"
          >
            <img src="{{ auth()->user()->avatar_url }}" alt="Foto profil {{ auth()->user()->name }}" class="top-profile-avatar">
            <span class="top-profile-name d-none d-sm-inline">{{ auth()->user()->username }}</span>
          </button>
          <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-4 p-2 mt-2" style="min-width: 230px;">
            <li class="px-3 py-2 border-bottom mb-1">
              <div class="fw-bold text-dark">{{ auth()->user()->name }}</div>
              <small class="text-muted">{{ auth()->user()->email }}</small>
            </li>
            <li>
              <a class="dropdown-item rounded-3 py-2 fw-semibold" href="{{ route('profile.index') }}">
                <span aria-hidden="true">👤</span> Profil Saya
              </a>
            </li>
            <li>
              <a class="dropdown-item rounded-3 py-2" href="{{ route('orders.history') }}">
                <span aria-hidden="true">🛍️</span> Riwayat Pesanan
              </a>
            </li>
            <li>
              <a class="dropdown-item rounded-3 py-2" href="{{ route('adoption.history') }}">
                <span aria-hidden="true">🏠</span> Riwayat Adopsi
              </a>
            </li>
            <li>
              <a class="dropdown-item rounded-3 py-2" href="{{ route('health.index') }}#jadwal-saya">
                <span aria-hidden="true">🩺</span> Jadwal Klinik Saya
              </a>
            </li>
            @if(auth()->user()->isAdmin())
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item rounded-3 py-2 fw-bold text-primary" href="{{ route('admin.dashboard') }}">
                  <span aria-hidden="true">⚙️</span> Panel Admin
                </a>
              </li>
            @endif
            <li><hr class="dropdown-divider"></li>
            <li>
              <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="dropdown-item rounded-3 py-2 text-danger fw-semibold">
                  <span aria-hidden="true">🚪</span> Keluar
                </button>
              </form>
            </li>
          </ul>
        </div>
      @else
        <a href="{{ route('login') }}" class="top-login-pill">
          <span aria-hidden="true">🔐</span> Masuk / Daftar
        </a>
      @endauth
    </div>

    @unless($hideSidebar)
      <aside class="sidebar">
        <button class="sidebar-logo" id="sidebarToggle" type="button" aria-label="Buka atau kecilkan menu samping" aria-expanded="true" title="Buka/tutup menu">
          <img src="{{ asset('assets/img/logo.png') }}" alt="Logo PawPaw Pet Care">
        </button>

        <ul class="nav-list">
          <li>
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}" @if(request()->routeIs('home')) aria-current="page" @endif title="Beranda">
              <span class="nav-icon" aria-hidden="true">🏠</span>
              <span class="label">Beranda</span>
            </a>
          </li>
          <li>
            <a href="{{ route('adoption.index') }}" class="{{ request()->routeIs('adoption.*') ? 'active' : '' }}" @if(request()->routeIs('adoption.*')) aria-current="page" @endif title="Adopsi">
              <span class="nav-icon" aria-hidden="true">🐾</span>
              <span class="label">Adopsi</span>
            </a>
          </li>
          <li>
            <a href="{{ route('shop.index') }}" class="{{ request()->routeIs('shop.*', 'cart.*', 'checkout.*', 'orders.*') ? 'active' : '' }}" @if(request()->routeIs('shop.*', 'cart.*', 'checkout.*', 'orders.*')) aria-current="page" @endif title="Toko">
              <span class="nav-icon" aria-hidden="true">🛍️</span>
              <span class="label">Toko</span>
            </a>
          </li>
          <li>
            <a href="{{ route('health.index') }}" class="{{ request()->routeIs('health.*') ? 'active' : '' }}" @if(request()->routeIs('health.*')) aria-current="page" @endif title="Layanan Kesehatan">
              <span class="nav-icon" aria-hidden="true">🩺</span>
              <span class="label">Kesehatan</span>
            </a>
          </li>
        </ul>

        <div class="sidebar-footer" aria-hidden="true">
          ✦ <span class="label">PawPaw</span>
        </div>
      </aside>

      <header class="page-header">
        <div class="mascot">
          <img src="{{ asset('assets/img/cuking.png') }}" alt="Maskot PawPaw Pet Care">
        </div>
        <div>
          <h1>PAWPAW PET CARE</h1>
          <p>
            Pet Care No. 1 Se-Panam 🐾<br>
            Sahabat Terbaik Hewan Peliharaan Anda 🐾<br>
            Rawat, adopsi, dan jaga kesehatan hewan kesayangan dengan PawPaw!
          </p>
        </div>
      </header>
    @endunless

    <main class="page-content @yield('content_class')">
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
          🐾 {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
      @endif

      @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
          ⚠️ {{ session('error') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
        </div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger mb-4">
          <ul class="mb-0 ps-3">
            @foreach($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      @yield('content')
    </main>

    <footer class="site-footer">
      <div class="footer-top">
        <div class="footer-brand">
          <div class="footer-brand-head">
            <div class="footer-logo-wrap">
              <img src="{{ asset('assets/img/logo.png') }}" alt="PawPaw Logo" class="footer-logo">
            </div>
            <div class="footer-brand-title">PAWPAW PET CARE</div>
          </div>
          <p>Sahabat terbaik hewan peliharaan Anda. Adopsi, rawat, dan jaga kesehatan si bulu dengan mudah.</p>
        </div>
        <div class="footer-col">
          <h4>Kontak</h4>
          <p>📍 Jl. Panam Raya No. 12, Pekanbaru</p>
          <p>📞 0812-3456-7890</p>
          <p>✉️ hello@pawpawpetcare.id</p>
        </div>
        <div class="footer-col">
          <h4>Jam Operasional</h4>
          <p>Senin - Sabtu: 08.00 - 20.00</p>
          <p>Minggu: 09.00 - 17.00</p>
          <p>Layanan darurat: 24 Jam</p>
        </div>
        <div class="footer-col">
          <h4>Ikuti Kami</h4>
          <div class="footer-socials">
            <a href="#" aria-label="Instagram">📷</a>
            <a href="#" aria-label="Facebook">📘</a>
            <a href="#" aria-label="WhatsApp">💬</a>
          </div>
        </div>
      </div>
      <div class="footer-bottom">
        <p>&copy; 2026 PawPaw Pet Care. All rights reserved.</p>
      </div>
    </footer>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="{{ asset('js/app.js') }}"></script>
</body>
</html>
