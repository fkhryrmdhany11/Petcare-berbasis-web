@extends('layouts.admin')

@section('title', 'Dashboard Admin | PawPaw Pet Care')
@section('heading', 'Dashboard Operasional')
@section('subheading', 'Ringkasan metrik real-time dan aksi cepat pengelolaan PawPaw Pet Care.')

@section('content')
  <section class="stats-grid">
    <div class="stat-card">
      <div class="stat-icon">🛍️</div>
      <div class="stat-label">Total Produk</div>
      <div class="stat-value">{{ $stats['products_count'] }}</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">📦</div>
      <div class="stat-label">Total Pesanan</div>
      <div class="stat-value">{{ $stats['orders_count'] }}</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">🐾</div>
      <div class="stat-label">Hewan Adopsi</div>
      <div class="stat-value">{{ $stats['animals_count'] }}</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">📋</div>
      <div class="stat-label">Pengajuan Adopsi</div>
      <div class="stat-value">{{ $stats['adoptions_count'] }}</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">📅</div>
      <div class="stat-label">Booking Kesehatan</div>
      <div class="stat-value">{{ $stats['bookings_count'] }}</div>
    </div>
    <div class="stat-card">
      <div class="stat-icon">💰</div>
      <div class="stat-label">Total Pendapatan</div>
      <div class="stat-value">Rp{{ number_format($stats['revenue'], 0, ',', '.') }}</div>
    </div>
  </section>

  <!-- Quick Actions Panel -->
  <section class="admin-card">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
      <h2 class="mb-0">⚡ Aksi Cepat (Quick Action)</h2>
      <div class="d-flex gap-2">
        <button type="button" class="btn btn-sm btn-primary active" data-quick-tab="tabQuickProduct">+ Produk</button>
        <button type="button" class="btn btn-sm btn-outline-primary" data-quick-tab="tabQuickAnimal">+ Hewan Adopsi</button>
        <button type="button" class="btn btn-sm btn-outline-primary" data-quick-tab="tabQuickService">+ Layanan Kesehatan</button>
      </div>
    </div>

    <div id="tabQuickProduct" data-quick-panel>
      <form action="{{ route('admin.quick-action') }}" method="POST" class="row g-3">
        @csrf
        <input type="hidden" name="action_type" value="product">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Nama Produk</label>
          <input type="text" name="product_name" class="form-control" placeholder="Contoh: Whiskas Tuna 1.2kg" required>
        </div>
        <div class="col-md-2">
          <label class="form-label fw-semibold">Kategori</label>
          <select name="category" class="form-select" required>
            <option value="Makanan">Makanan</option>
            <option value="Perawatan">Perawatan</option>
            <option value="Vitamin">Vitamin</option>
            <option value="Aksesoris">Aksesoris</option>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label fw-semibold">Harga (Rp)</label>
          <input type="number" name="price" class="form-control" min="0" value="45000" required>
        </div>
        <div class="col-md-2">
          <label class="form-label fw-semibold">Stok</label>
          <input type="number" name="stock" class="form-control" min="0" value="20" required>
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button type="submit" class="btn btn-primary w-100 fw-bold">Simpan Produk</button>
        </div>
      </form>
    </div>

    <div id="tabQuickAnimal" data-quick-panel class="d-none">
      <form action="{{ route('admin.quick-action') }}" method="POST" class="row g-3">
        @csrf
        <input type="hidden" name="action_type" value="animal">
        <div class="col-md-3">
          <label class="form-label fw-semibold">Nama Hewan</label>
          <input type="text" name="nama" class="form-control" placeholder="Contoh: Milo" required>
        </div>
        <div class="col-md-2">
          <label class="form-label fw-semibold">Spesies</label>
          <select name="species" class="form-select" required>
            <option value="Kucing">Kucing</option>
            <option value="Anjing">Anjing</option>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Ras (Breed)</label>
          <input type="text" name="breed" class="form-control" placeholder="Contoh: Persian" required>
        </div>
        <div class="col-md-2">
          <label class="form-label fw-semibold">Gender & Usia</label>
          <input type="hidden" name="gender" value="Jantan">
          <input type="text" name="age" class="form-control" placeholder="1 Tahun" required>
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button type="submit" class="btn btn-primary w-100 fw-bold">Simpan Hewan</button>
        </div>
      </form>
    </div>

    <div id="tabQuickService" data-quick-panel class="d-none">
      <form action="{{ route('admin.quick-action') }}" method="POST" class="row g-3">
        @csrf
        <input type="hidden" name="action_type" value="service">
        <div class="col-md-4">
          <label class="form-label fw-semibold">Nama Layanan</label>
          <input type="text" name="service_name" class="form-control" placeholder="Contoh: Pemeriksaan Umum" required>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Biaya (Rp)</label>
          <input type="number" name="price" class="form-control" min="0" value="120000" required>
        </div>
        <div class="col-md-3">
          <label class="form-label fw-semibold">Deskripsi Singkat</label>
          <input type="text" name="description" class="form-control" placeholder="Keterangan layanan">
        </div>
        <div class="col-md-2 d-flex align-items-end">
          <button type="submit" class="btn btn-primary w-100 fw-bold">Simpan Layanan</button>
        </div>
      </form>
    </div>
  </section>

  <!-- Recent Activity Tables -->
  <div class="row g-4">
    <div class="col-lg-6">
      <section class="admin-card h-100">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h2 class="mb-0">📦 Pesanan Terbaru</h2>
          <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">Semua Pesanan →</a>
        </div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Kode</th>
                <th>Pelanggan</th>
                <th>Total</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentOrders as $order)
                <tr>
                  <td class="fw-bold">{{ $order->order_code }}</td>
                  <td>{{ $order->recipient_name }}</td>
                  <td>Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
                  <td><span class="badge-status status-{{ $order->status }}">{{ ucfirst($order->status) }}</span></td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-center text-muted py-3">Belum ada pesanan.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </section>
    </div>

    <div class="col-lg-6">
      <section class="admin-card h-100">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h2 class="mb-0">📋 Pengajuan Adopsi Terbaru</h2>
          <a href="{{ route('admin.adoptions.index') }}" class="btn btn-sm btn-outline-primary">Semua Adopsi →</a>
        </div>
        <div class="table-responsive">
          <table class="table align-middle">
            <thead>
              <tr>
                <th>Pemohon</th>
                <th>Hewan</th>
                <th>Tanggal</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              @forelse($recentAdoptions as $adoption)
                <tr>
                  <td class="fw-bold">{{ $adoption->applicant_name }}</td>
                  <td>{{ $adoption->animal->nama ?? '-' }}</td>
                  <td>{{ optional($adoption->request_date)->format('d M Y') }}</td>
                  <td><span class="badge-status status-{{ $adoption->status }}">{{ ucfirst($adoption->status) }}</span></td>
                </tr>
              @empty
                <tr><td colspan="4" class="text-center text-muted py-3">Belum ada pengajuan adopsi.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
      </section>
    </div>
  </div>
@endsection
