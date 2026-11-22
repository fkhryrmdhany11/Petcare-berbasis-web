@extends('layouts.app')

@section('title', 'Keranjang Belanja | PawPaw Pet Care')
@section('content_class', 'shop-page')

@section('content')
  <div class="container-fluid px-0" style="max-width: 1120px">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
      <div>
        <p class="shop-eyebrow mb-1">PAWPAW SHOP</p>
        <h2 class="mb-0">Keranjang Belanja</h2>
      </div>
      <div class="d-flex flex-wrap gap-2">
        @auth
          <a class="shop-history-link" href="{{ route('orders.history') }}">📦 Riwayat Pesanan</a>
        @endauth
        <a class="back-cart-link" href="{{ route('shop.index') }}">← Lanjut Belanja</a>
      </div>
    </div>

    <div class="row g-4">
      <div class="col-lg-8">
        <section class="shop-panel">
          @if(empty($items))
            <div class="text-center py-5">
              <span class="display-5" aria-hidden="true">🛒</span>
              <h3 class="h5 mt-3">Keranjang masih kosong</h3>
              <p class="text-secondary">Tambahkan produk dari katalog terlebih dahulu.</p>
              <a class="btn-pawpaw mt-2" href="{{ route('shop.index') }}">Lihat Katalog Produk</a>
            </div>
          @else
            @foreach($items as $item)
              @php($p = $item['product'])
              <div class="cart-item d-flex align-items-center justify-content-between gap-3 flex-wrap">
                <div class="d-flex align-items-center gap-3">
                  <img src="{{ asset($p->image) }}" alt="{{ $p->product_name }}" class="cart-item-image">
                  <div>
                    <strong class="d-block">{{ $p->product_name }}</strong>
                    <small class="text-secondary">Rp {{ number_format((float) $p->price, 0, ',', '.') }} / item (Stok: {{ $p->stock }})</small>
                  </div>
                </div>

                <div class="d-flex align-items-center gap-2">
                  <form action="{{ route('cart.update') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $p->product_id }}">
                    <input type="hidden" name="action" value="decrease">
                    <button type="submit" class="btn btn-outline-secondary btn-sm">−</button>
                  </form>

                  <span class="fw-bold px-2">{{ $item['quantity'] }}</span>

                  <form action="{{ route('cart.update') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $p->product_id }}">
                    <input type="hidden" name="action" value="increase">
                    <button type="submit" class="btn btn-outline-secondary btn-sm" {{ $item['quantity'] >= $p->stock ? 'disabled' : '' }}>+</button>
                  </form>

                  <form action="{{ route('cart.update') }}" method="POST" class="d-inline">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $p->product_id }}">
                    <input type="hidden" name="action" value="remove">
                    <button type="submit" class="btn btn-link text-danger btn-sm text-decoration-none">Hapus</button>
                  </form>
                </div>

                <strong class="text-end" style="min-width: 110px">
                  Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                </strong>
              </div>
            @endforeach
          @endif
        </section>
      </div>

      <div class="col-lg-4">
        <aside class="shop-panel">
          <h3 class="h5 mb-3">Ringkasan</h3>
          <div class="d-flex justify-content-between mb-3">
            <span>Subtotal ({{ $cartCount }} item)</span>
            <strong>Rp {{ number_format($subtotal, 0, ',', '.') }}</strong>
          </div>
          <p class="small text-secondary">Biaya pengantaran dipilih pada halaman checkout.</p>
          @if(!empty($items))
            <a href="{{ route('checkout.index') }}" class="btn-pawpaw w-100">Lanjut ke Checkout</a>
          @else
            <button class="btn btn-secondary w-100" disabled>Lanjut ke Checkout</button>
          @endif
        </aside>
      </div>
    </div>
  </div>
@endsection
