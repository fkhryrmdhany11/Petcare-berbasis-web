@extends('layouts.admin')

@section('title', 'Layanan & Jadwal Kesehatan | Admin PawPaw')
@section('heading', 'Kelola Layanan & Jadwal Klinik Kesehatan')
@section('subheading', 'Atur paket layanan, dokter praktik, hari operasional, slot jam praktik, dan kuota harian (CRUD).')

@section('content')
  <section class="admin-card">
    <h2>{{ $editService ? '✏️ Edit Layanan & Jadwal #' . $editService->service_id : '➕ Tambah Layanan & Jadwal Praktik' }}</h2>
    <form
      action="{{ $editService ? route('admin.services.update', $editService->service_id) : route('admin.services.store') }}"
      method="POST"
      class="row g-3"
    >
      @csrf
      <div class="col-md-4">
        <label class="form-label fw-semibold">Nama Layanan *</label>
        <input type="text" name="service_name" class="form-control" value="{{ old('service_name', $editService->service_name ?? '') }}" placeholder="Contoh: Pemeriksaan Umum" required>
      </div>
      <div class="col-md-4">
        <label class="form-label fw-semibold">Dokter Penanggung Jawab *</label>
        <input type="text" name="doctor_name" class="form-control" value="{{ old('doctor_name', $editService->doctor_name ?? 'drh. Clara Wijaya') }}" placeholder="Contoh: drh. Clara Wijaya" required>
      </div>
      <div class="col-md-4">
        <label class="form-label fw-semibold">Hari Praktik *</label>
        <input type="text" name="practice_days" class="form-control" value="{{ old('practice_days', $editService->practice_days ?? 'Senin - Sabtu') }}" placeholder="Contoh: Senin - Sabtu" required>
      </div>
      <div class="col-md-4">
        <label class="form-label fw-semibold">Slot Jam Praktik (Pisahkan Koma) *</label>
        <input type="text" name="time_slots" class="form-control" value="{{ old('time_slots', $editService->time_slots ?? '09:00, 13:00, 16:00') }}" placeholder="09:00, 13:00, 16:00" required>
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold">Kuota / Slot *</label>
        <input type="number" name="daily_quota" class="form-control" min="1" max="100" value="{{ old('daily_quota', $editService->daily_quota ?? 5) }}" required>
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold">Biaya (Rp) *</label>
        <input type="number" name="price" class="form-control" min="0" value="{{ old('price', isset($editService) ? (int) $editService->price : 120000) }}" required>
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold">Ikon</label>
        <input type="text" name="icon" class="form-control" value="{{ old('icon', $editService->icon ?? '🩺') }}">
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold">Warna Kartu</label>
        <select name="card_color" class="form-select">
          @foreach(['card-green' => 'Hijau', 'card-blue' => 'Biru', 'card-purple' => 'Ungu', 'card-orange' => 'Oranye'] as $cls => $label)
            <option value="{{ $cls }}" @selected(old('card_color', $editService->card_color ?? 'card-green') === $cls)>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-10">
        <label class="form-label fw-semibold">Deskripsi Layanan</label>
        <input type="text" name="description" class="form-control" value="{{ old('description', $editService->description ?? '') }}" placeholder="Keterangan singkat layanan...">
      </div>
      <div class="col-md-2 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary w-100 fw-bold">{{ $editService ? 'Update' : 'Simpan' }}</button>
        @if($editService)
          <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary">Batal</a>
        @endif
      </div>
    </form>
  </section>

  <section class="admin-card">
    <h2>🩺 Daftar Layanan &amp; Jadwal Praktik ({{ $services->count() }})</h2>
    <div class="table-responsive">
      <table class="table align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Layanan &amp; Dokter</th>
            <th>Hari &amp; Slot Jam Praktik</th>
            <th>Kuota/Slot</th>
            <th>Biaya</th>
            <th class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($services as $service)
            <tr>
              <td>#{{ $service->service_id }}</td>
              <td>
                <div class="fw-bold">{{ $service->icon }} {{ $service->service_name }}</div>
                <small class="text-primary fw-semibold">👨‍⚕️ {{ $service->doctor_name }}</small>
                <div class="small text-muted">{{ $service->description }}</div>
              </td>
              <td>
                <div class="fw-semibold">{{ $service->practice_days }}</div>
                <div class="d-flex flex-wrap gap-1 mt-1">
                  @foreach($service->time_slots_array as $slot)
                    <span class="badge bg-info-subtle text-info-emphasis">🕒 {{ $slot }}</span>
                  @endforeach
                </div>
              </td>
              <td>
                <span class="badge bg-success-subtle text-success">{{ $service->daily_quota }} pasien / sesi</span>
              </td>
              <td class="fw-semibold">Rp{{ number_format($service->price, 0, ',', '.') }}</td>
              <td class="text-end">
                <div class="d-inline-flex gap-1">
                  <a href="{{ route('admin.services.index', ['edit' => $service->service_id]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                  <form action="{{ route('admin.services.destroy', $service->service_id) }}" method="POST" data-confirm="Hapus layanan {{ $service->service_name }}?">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada layanan kesehatan.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
@endsection
