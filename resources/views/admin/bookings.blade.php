@extends('layouts.admin')

@section('title', 'Booking Kesehatan | Admin PawPaw')
@section('heading', 'Jadwal Booking Layanan Kesehatan')
@section('subheading', 'Kelola reservasi klinik hewan pelanggan dan perbarui status kunjungan.')

@section('content')
  <section class="admin-card">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
      <h2 class="mb-0">📅 Semua Booking Kesehatan ({{ $bookings->count() }})</h2>
      <input type="search" class="form-control w-auto" placeholder="Cari pemilik / hewan..." data-admin-filter="#bookingsTable">
    </div>

    <div class="table-responsive">
      <table class="table align-middle" id="bookingsTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Pemilik & Hewan</th>
            <th>Layanan</th>
            <th>Jadwal</th>
            <th>Status</th>
            <th class="text-end">Ubah Status / Hapus</th>
          </tr>
        </thead>
        <tbody>
          @forelse($bookings as $booking)
            <tr>
              <td>#{{ $booking->booking_id }}</td>
              <td>
                <div class="fw-bold">{{ $booking->owner_name }}</div>
                <small class="text-muted">Hewan: {{ $booking->pet_name }} ({{ $booking->pet_species }})</small>
              </td>
              <td>
                <div class="fw-semibold">{{ $booking->service->service_name ?? '-' }}</div>
                <small class="text-muted">Rp{{ number_format($booking->service->price ?? 0, 0, ',', '.') }}</small>
              </td>
              <td>{{ optional($booking->booking_date)->format('d M Y H:i') }}</td>
              <td>
                <span class="badge-status status-{{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
              </td>
              <td class="text-end">
                <div class="d-inline-flex gap-1 align-items-center">
                  <form action="{{ route('admin.bookings.status', $booking->booking_id) }}" method="POST" class="d-inline-flex gap-1">
                    @csrf
                    <select name="status" class="form-select form-select-sm">
                      @foreach(['pending', 'approved', 'completed', 'rejected'] as $st)
                        <option value="{{ $st }}" @selected($booking->status === $st)>{{ ucfirst($st) }}</option>
                      @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                  </form>
                  <form action="{{ route('admin.bookings.destroy', $booking->booking_id) }}" method="POST" data-confirm="Hapus jadwal booking ini?">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada jadwal booking kesehatan.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
@endsection
