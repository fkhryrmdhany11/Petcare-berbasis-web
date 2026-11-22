@extends('layouts.app')

@section('title', 'Invoice ' . $order->order_code . ' | PawPaw Pet Care')
@section('hide_sidebar', 'true')

@section('content')
  <div class="print-sheet">
    <div class="d-flex justify-content-between align-items-center mb-4 no-print">
      <a href="{{ route('orders.history') }}" class="btn btn-outline-secondary rounded-pill px-3">
        ← Kembali ke Riwayat Pesanan
      </a>
      <button type="button" onclick="window.print()" class="btn btn-primary rounded-pill px-4 fw-bold">
        🖨️ Cetak / Simpan PDF
      </button>
    </div>

    <div class="d-flex justify-content-between align-items-start flex-wrap border-bottom pb-3 mb-4 gap-3">
      <div class="d-flex align-items-center gap-3">
        <img src="{{ asset('assets/img/logo.png') }}" alt="PawPaw Logo" style="width:64px;height:64px;object-fit:contain;">
        <div>
          <h2 class="h4 fw-bold mb-0">PAWPAW PET CARE</h2>
          <p class="small text-secondary mb-0">Jl. Panam Raya No. 12, Pekanbaru • Telp: 0812-3456-7890</p>
        </div>
      </div>
      <div class="text-md-end">
        <span class="badge bg-primary-subtle text-primary px-3 py-2 rounded-pill fw-bold mb-1">INVOICE PEMBELIAN</span>
        <div class="fw-bold fs-5">{{ $order->order_code }}</div>
        <small class="text-secondary">{{ $order->order_date?->format('d F Y, H:i') }} WIB</small>
      </div>
    </div>

    <div class="row g-3 mb-4">
      <div class="col-sm-6">
        <small class="text-secondary d-block">Penerima</small>
        <div class="fw-bold">{{ $order->recipient_name }}</div>
        <div class="small">{{ $order->recipient_phone }}</div>
      </div>
      <div class="col-sm-6 text-sm-end">
        <small class="text-secondary d-block">Metode &amp; Alamat Pengiriman</small>
        <div class="fw-bold">{{ $order->delivery_method === 'delivery' ? 'Pengantaran Kurir' : 'Ambil di Toko (Pickup)' }}</div>
        <div class="small">{{ $order->shipping_address ?: 'Toko Utama PawPaw Pet Care' }}</div>
      </div>
    </div>

    <div class="table-responsive mb-4">
      <table class="table align-middle">
        <thead class="table-light">
          <tr>
            <th>Produk</th>
            <th class="text-center">Jumlah</th>
            <th class="text-end">Harga Satuan</th>
            <th class="text-end">Subtotal</th>
          </tr>
        </thead>
        <tbody>
          @foreach($order->details as $detail)
            <tr>
              <td class="fw-semibold">{{ $detail->product?->product_name ?? 'Produk PawPaw' }}</td>
              <td class="text-center">{{ $detail->quantity }}</td>
              <td class="text-end">Rp {{ number_format((float) $detail->unit_price, 0, ',', '.') }}</td>
              <td class="text-end fw-semibold">Rp {{ number_format((float) $detail->subtotal, 0, ',', '.') }}</td>
            </tr>
          @endforeach
        </tbody>
        <tfoot>
          @if($order->shipping_fee > 0)
            <tr>
              <td colspan="3" class="text-end text-secondary">Biaya Pengantaran</td>
              <td class="text-end">Rp {{ number_format((float) $order->shipping_fee, 0, ',', '.') }}</td>
            </tr>
          @endif
          <tr>
            <td colspan="3" class="text-end fw-bold fs-5">Total Pembayaran</td>
            <td class="text-end fw-bold fs-5 text-primary">Rp {{ number_format((float) $order->total_price, 0, ',', '.') }}</td>
          </tr>
        </tfoot>
      </table>
    </div>

    <div class="d-flex justify-content-between align-items-center flex-wrap pt-2 border-top small text-secondary">
      <div>Status Pesanan: <strong class="text-dark">{{ strtoupper($order->status) }}</strong></div>
      <div>Terima kasih telah berbelanja di PawPaw Pet Care 🐾</div>
    </div>
  </div>
@endsection
