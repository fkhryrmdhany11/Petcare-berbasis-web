@extends('layouts.app')

@section('title', 'Riwayat Pesanan | PawPaw Pet Care')
@section('content_class', 'shop-page')

@section('content')
  <div class="order-history-shell">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
      <div>
        <p class="shop-eyebrow mb-1">PAWPAW SHOP</p>
        <h2 class="mb-1">Riwayat Pesanan Saya</h2>
        <p class="text-secondary mb-0">Menampilkan riwayat pesanan untuk akun <strong>{{ '@' . auth()->user()->username }}</strong> dari database.</p>
      </div>
      <a href="{{ route('shop.index') }}" class="back-cart-link">← Kembali ke Toko</a>
    </div>

    @php
      $statusLabels = [
        'pending' => 'Menunggu Konfirmasi',
        'paid' => 'Sudah Dibayar',
        'processing' => 'Sedang Diproses',
        'shipped' => 'Sedang Dikirim',
        'completed' => 'Selesai',
        'cancelled' => 'Dibatalkan',
      ];
    @endphp

    @if($orders->isEmpty())
      <div class="shop-panel text-center py-5">
        <span class="display-5">📦</span>
        <h3 class="h5 mt-3">Belum ada pesanan</h3>
        <p class="text-secondary">Pesanan yang Anda checkout akan otomatis muncul di sini.</p>
        <a href="{{ route('shop.index') }}" class="btn-pawpaw mt-2">Mulai Belanja</a>
      </div>
    @else
      <section class="order-history-list">
        @foreach($orders as $order)
          <article class="order-history-card">
            <div class="order-history-head">
              <div>
                <div class="fw-bold fs-5">{{ $order->order_code }}</div>
                <div class="small text-secondary">{{ $order->order_date?->format('d M Y, H:i') }} WIB</div>
              </div>
              <span class="order-status {{ $order->status }}">
                {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
              </span>
            </div>

            <div class="order-history-body">
              <div class="order-history-meta">
                <div>
                  <small class="text-secondary d-block">Penerima</small>
                  <strong>{{ $order->recipient_name }} ({{ $order->recipient_phone }})</strong>
                </div>
                <div>
                  <small class="text-secondary d-block">Metode Pengiriman</small>
                  <strong>{{ $order->delivery_method === 'delivery' ? 'Pengantaran Kurir' : 'Ambil di Toko' }}</strong>
                </div>
                <div>
                  <small class="text-secondary d-block">Alamat Pengiriman</small>
                  <strong>{{ $order->shipping_address ?: '-' }}</strong>
                </div>
              </div>

              @foreach($order->details as $detail)
                <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                  <div>
                    <strong>{{ $detail->product?->product_name ?? 'Produk' }}</strong>
                    <span class="text-secondary small ms-2">x{{ $detail->quantity }}</span>
                  </div>
                  <span>Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}</span>
                </div>
              @endforeach

              @if($order->shipping_fee > 0)
                <div class="d-flex justify-content-between align-items-center py-2 text-secondary small">
                  <span>Biaya Pengantaran</span>
                  <span>Rp {{ number_format((float) $order->shipping_fee, 0, ',', '.') }}</span>
                </div>
              @endif

              <div class="d-flex justify-content-between align-items-center pt-3 mt-2 fw-bold fs-5">
                <span>Total Pesanan</span>
                <span>Rp {{ number_format((float) $order->total_price, 0, ',', '.') }}</span>
              </div>

              <div class="d-flex justify-content-end align-items-center flex-wrap gap-2 mt-3 pt-2 border-top">
                <a href="{{ route('orders.invoice', $order->order_id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold">
                  🧾 Lihat / Cetak Invoice
                </a>
                @if($order->status === 'pending')
                  <form action="{{ route('orders.cancel', $order->order_id) }}" method="POST" onsubmit="return confirm('Batalkan pesanan {{ $order->order_code }}? Stok produk akan dikembalikan.');">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold">
                      ✕ Batalkan Pesanan
                    </button>
                  </form>
                @endif
              </div>
            </div>
          </article>
        @endforeach
      </section>
    @endif
  </div>
@endsection
