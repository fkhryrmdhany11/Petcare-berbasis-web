@extends('layouts.app')

@section('title', 'Checkout | PawPaw Pet Care')
@section('content_class', 'shop-page')

@section('content')
  <div class="container-fluid px-0" style="max-width: 1120px">
    <div class="mb-4">
      <a href="{{ route('cart.index') }}" class="back-cart-link">← Kembali ke Keranjang</a>
      <h2 class="mt-3">Checkout Pesanan</h2>
      <p class="text-secondary">
        Pesanan akan langsung disimpan ke database MySQL <strong>pet_care</strong> dan dapat diproses oleh Admin.
      </p>
    </div>

    <div class="row g-4">
      <div class="col-lg-7">
        <section class="shop-panel">
          <h3 class="h5 mb-4">Data Pemesan</h3>
          <form action="{{ route('checkout.store') }}" method="POST" id="checkout-form">
            @csrf
            <div class="mb-3">
              <label class="form-label fw-semibold">Username Akun</label>
              <input class="form-control bg-light" value="{{ auth()->user()->username }}" readonly>
              <div class="form-text">Pesanan terikat otomatis ke akun Anda yang sedang login.</div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold" for="recipient_name">Nama Penerima *</label>
              <input class="form-control" id="recipient_name" name="recipient_name" value="{{ old('recipient_name', auth()->user()->name) }}" maxlength="100" required>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold" for="recipient_phone">Nomor Telepon *</label>
              <input class="form-control" id="recipient_phone" name="recipient_phone" value="{{ old('recipient_phone', auth()->user()->phone) }}" maxlength="30" required>
            </div>

            <div class="mb-3">
              <label class="form-label fw-semibold" for="delivery-method">Metode Penerimaan Barang *</label>
              <select class="form-select" id="delivery-method" name="delivery_method">
                <option value="pickup" {{ old('delivery_method') === 'pickup' ? 'selected' : '' }}>Ambil di Toko (Rp 0)</option>
                <option value="delivery" {{ old('delivery_method') === 'delivery' ? 'selected' : '' }}>Pengantaran Kurir (Rp 15.000)</option>
              </select>
            </div>

            <div class="mb-3" id="address-field-wrapper" {{ old('delivery_method') === 'delivery' ? '' : 'hidden' }}>
              <label class="form-label fw-semibold" for="delivery-address">Alamat Pengantaran *</label>
              <textarea class="form-control" id="delivery-address" name="shipping_address" rows="3" maxlength="300">{{ old('shipping_address', auth()->user()->address) }}</textarea>
            </div>

            <button class="btn-pawpaw w-100 py-2 mt-2" type="submit">
              Buat Pesanan Sekarang
            </button>
          </form>
        </section>
      </div>

      <div class="col-lg-5">
        <aside class="shop-panel">
          <h3 class="h5 mb-3">Ringkasan Pesanan</h3>
          @foreach($items as $item)
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
              <div>
                <strong>{{ $item['product']->product_name }}</strong>
                <div class="small text-secondary">{{ $item['quantity'] }} x Rp {{ number_format((float) $item['product']->price, 0, ',', '.') }}</div>
              </div>
              <strong>Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</strong>
            </div>
          @endforeach

          <div class="d-flex justify-content-between mt-3 mb-2">
            <span>Subtotal</span>
            <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
          </div>
          <div class="d-flex justify-content-between mb-2">
            <span>Biaya Pengantaran</span>
            <span id="checkout-shipping">Rp 0</span>
          </div>
          <hr>
          <div class="d-flex justify-content-between fw-bold fs-5">
            <span>Total Bayar</span>
            <span id="checkout-total" data-subtotal="{{ $subtotal }}">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
          </div>
        </aside>
      </div>
    </div>
  </div>
@endsection
