@extends('layouts.admin')

@section('title', 'Pesanan Toko | Admin PawPaw')
@section('heading', 'Pesanan Toko Pelanggan')
@section('subheading', 'Pantau rincian produk yang dibeli dan perbarui status pengiriman pesanan.')

@section('content')
  <section class="admin-card">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
      <h2 class="mb-0">📦 Semua Pesanan ({{ $orders->count() }})</h2>
      <input type="search" class="form-control w-auto" placeholder="Cari kode / penerima..." data-admin-filter="#ordersTable">
    </div>

    <div class="table-responsive">
      <table class="table align-middle" id="ordersTable">
        <thead>
          <tr>
            <th>Kode Order</th>
            <th>Penerima & Pengiriman</th>
            <th>Item Produk</th>
            <th>Total</th>
            <th>Status</th>
            <th>Ubah Status</th>
          </tr>
        </thead>
        <tbody>
          @forelse($orders as $order)
            <tr>
              <td>
                <div class="fw-bold">{{ $order->order_code }}</div>
                <small class="text-muted">{{ optional($order->order_date)->format('d M Y H:i') }}</small>
              </td>
              <td>
                <div class="fw-semibold">{{ $order->recipient_name }}</div>
                <div class="small text-muted">{{ $order->recipient_phone }} • {{ strtoupper($order->delivery_method) }}</div>
                @if($order->shipping_address)
                  <div class="small text-secondary">{{ $order->shipping_address }}</div>
                @endif
              </td>
              <td>
                <ul class="list-unstyled mb-0 small">
                  @foreach($order->details as $detail)
                    <li>• {{ $detail->product->product_name ?? 'Produk' }} ({{ $detail->quantity }}x)</li>
                  @endforeach
                </ul>
              </td>
              <td class="fw-bold">Rp{{ number_format($order->total_price, 0, ',', '.') }}</td>
              <td>
                <span class="badge-status status-{{ $order->status }}">{{ ucfirst($order->status) }}</span>
              </td>
              <td>
                <form action="{{ route('admin.orders.status', $order->order_id) }}" method="POST" class="d-flex gap-1">
                  @csrf
                  <select name="status" class="form-select form-select-sm" style="min-width: 130px;">
                    @foreach(['pending', 'paid', 'processing', 'shipped', 'completed', 'cancelled'] as $st)
                      <option value="{{ $st }}" @selected($order->status === $st)>{{ ucfirst($st) }}</option>
                    @endforeach
                  </select>
                  <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                </form>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pesanan masuk.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
@endsection
