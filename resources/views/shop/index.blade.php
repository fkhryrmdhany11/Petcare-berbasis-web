@extends('layouts.app')

@section('title', 'Toko Produk | PawPaw Pet Care')
@section('content_class', 'shop-page')

@section('content')
  <section class="shop-catalog">
    <div class="shop-heading">
      <div>
        <p class="shop-eyebrow">PAWPAW SHOP</p>
        <h2>Katalog Produk</h2>
        <p class="shop-subtitle">
          Temukan makanan, mainan, dan kebutuhan perawatan untuk hewan peliharaan langsung dari database kami.
        </p>
      </div>
      <div class="shop-heading-actions">
        @auth
          <a class="shop-history-link" href="{{ route('orders.history') }}">
            📦 Riwayat Pesanan
          </a>
        @endauth
        <a class="shop-cart-link" href="{{ route('cart.index') }}">
          🛒 Keranjang
          <span class="shop-cart-count">{{ $cartCount }}</span>
        </a>
      </div>
    </div>

    <div class="shop-search-wrap">
      <label class="shop-search" for="product-search">
        <span class="shop-search-icon" aria-hidden="true">⌕</span>
        <input id="product-search" type="search" placeholder="Cari produk, kategori, atau kebutuhan..." autocomplete="off">
      </label>
    </div>

    @php
      $categories = $products->pluck('category')->filter()->map(fn ($c) => strtolower(trim($c)))->unique()->values();
    @endphp
    <div class="shop-filters" role="group" aria-label="Filter kategori produk">
      <button type="button" class="filter-btn active" data-filter-category="semua" aria-pressed="true">Semua</button>
      @foreach($categories as $cat)
        <button type="button" class="filter-btn" data-filter-category="{{ $cat }}" aria-pressed="false">{{ ucfirst($cat) }}</button>
      @endforeach
    </div>

    <p id="search-empty" class="alert alert-info text-center" role="status" aria-live="polite" hidden>
      Tidak ada produk yang cocok dengan pencarian.
    </p>

    <div class="product-grid">
      @foreach($products as $product)
        <article class="product-card" data-category="{{ strtolower($product->category) }}">
          <div class="product-image">
            <img src="{{ asset($product->image) }}" alt="{{ $product->product_name }}" loading="lazy">
          </div>
          <div class="product-info">
            <h3>{{ $product->product_name }}</h3>
            <p class="product-category">{{ ucfirst(strtolower($product->category)) }} • Stok: {{ $product->stock }}</p>
            <p class="product-description">
              {{ $product->description ?: ($product->price >= 90000 ? 'Produk perawatan & nutrisi premium' : 'Pilihan hemat berkualitas untuk anabul') }}
            </p>
            <div class="product-footer">
              <p class="product-price">Rp {{ number_format((float) $product->price, 0, ',', '.') }}</p>
              <form action="{{ route('cart.add') }}" method="POST">
                @csrf
                <input type="hidden" name="product_id" value="{{ $product->product_id }}">
                <input type="hidden" name="quantity" value="1">
                <button class="cart-button" type="submit" {{ $product->stock < 1 ? 'disabled' : '' }} aria-label="Tambah {{ $product->product_name }} ke keranjang" title="Tambah {{ $product->product_name }} ke keranjang">
                  <span aria-hidden="true">{{ $product->stock > 0 ? '🛒' : '✕' }}</span>
                </button>
              </form>
            </div>
          </div>
        </article>
      @endforeach
    </div>
  </section>
@endsection
