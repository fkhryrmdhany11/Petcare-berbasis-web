@extends('layouts.admin')

@section('title', 'Pengajuan Adopsi | Admin PawPaw')
@section('heading', 'Pengajuan Adopsi Pelanggan')
@section('subheading', 'Verifikasi formulir pengajuan adopsi. Menyetujui satu pemohon otomatis menandai hewan Adopted.')

@section('content')
  <section class="admin-card">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
      <h2 class="mb-0">📋 Daftar Pengajuan Adopsi ({{ $adoptions->count() }})</h2>
      <input type="search" class="form-control w-auto" placeholder="Cari pemohon / hewan..." data-admin-filter="#adoptionsTable">
    </div>

    <div class="table-responsive">
      <table class="table align-middle" id="adoptionsTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Pemohon</th>
            <th>Hewan</th>
            <th>Alasan Adopsi</th>
            <th>Status</th>
            <th class="text-end">Keputusan Admin</th>
          </tr>
        </thead>
        <tbody>
          @forelse($adoptions as $adoption)
            <tr>
              <td>#{{ $adoption->adoption_request_id }}</td>
              <td>
                <div class="fw-bold">{{ $adoption->applicant_name }}</div>
                <small class="text-muted">{{ $adoption->phone }} • {{ $adoption->email }}</small>
              </td>
              <td>
                <div class="fw-semibold">{{ $adoption->animal->nama ?? '-' }}</div>
                <small class="text-muted">{{ $adoption->animal->species ?? '' }} - {{ $adoption->animal->breed ?? '' }}</small>
              </td>
              <td style="max-width: 260px;">
                <div class="small">{{ $adoption->reason }}</div>
                <small class="text-secondary">Alamat: {{ $adoption->address }}</small>
              </td>
              <td>
                <span class="badge-status status-{{ $adoption->status }}">{{ ucfirst($adoption->status) }}</span>
              </td>
              <td class="text-end">
                <div class="d-inline-flex gap-1 align-items-center">
                  <a href="{{ route('admin.adoptions.show', $adoption->adoption_request_id) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                  <form action="{{ route('admin.adoptions.status', $adoption->adoption_request_id) }}" method="POST" class="d-inline-flex gap-1">
                    @csrf
                    <select name="status" class="form-select form-select-sm">
                      @foreach(['pending', 'approved', 'rejected'] as $st)
                        <option value="{{ $st }}" @selected($adoption->status === $st)>{{ ucfirst($st) }}</option>
                      @endforeach
                    </select>
                    <button type="submit" class="btn btn-sm btn-primary">Simpan</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pengajuan adopsi.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
@endsection
