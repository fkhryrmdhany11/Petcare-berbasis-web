@extends('layouts.admin')

@section('title', 'Detail Pengajuan Adopsi | Admin PawPaw')
@section('heading', 'Detail Pengajuan Adopsi #' . $adoption->adoption_request_id)
@section('subheading', 'Informasi lengkap calon pengadopsi dan hewan yang diajukan.')

@section('content')
  <div class="mb-3">
    <a href="{{ route('admin.adoptions.index') }}" class="btn btn-outline-secondary btn-sm">← Kembali ke Daftar Pengajuan</a>
  </div>

  <div class="row g-4">
    <div class="col-md-5">
      <section class="admin-card h-100">
        <h2>🐾 Data Hewan</h2>
        @if($adoption->animal)
          <img src="{{ asset($adoption->animal->image) }}" alt="{{ $adoption->animal->nama }}" class="img-fluid rounded-4 mb-3" style="max-height:220px;object-fit:cover;width:100%;">
          <h4 class="fw-bold mb-1">{{ $adoption->animal->nama }}</h4>
          <p class="text-muted mb-2">{{ $adoption->animal->species }} • {{ $adoption->animal->breed }} • {{ $adoption->animal->gender }} ({{ $adoption->animal->age }})</p>
          <p class="mb-2">{{ $adoption->animal->description }}</p>
          <span class="badge-status status-{{ $adoption->animal->status }}">Status Hewan: {{ ucfirst($adoption->animal->status) }}</span>
        @endif
      </section>
    </div>

    <div class="col-md-7">
      <section class="admin-card h-100">
        <h2>👤 Formulir Calon Pengadopsi</h2>
        <dl class="row mb-4">
          <dt class="col-sm-4">Nama Lengkap</dt>
          <dd class="col-sm-8 fw-semibold">{{ $adoption->applicant_name }}</dd>

          <dt class="col-sm-4">Email</dt>
          <dd class="col-sm-8">{{ $adoption->email }}</dd>

          <dt class="col-sm-4">No. Telepon</dt>
          <dd class="col-sm-8">{{ $adoption->phone }}</dd>

          <dt class="col-sm-4">Alamat Tempat Tinggal</dt>
          <dd class="col-sm-8">{{ $adoption->address }}</dd>

          <dt class="col-sm-4">Alasan Mengadopsi</dt>
          <dd class="col-sm-8">{{ $adoption->reason }}</dd>

          <dt class="col-sm-4">Tanggal Pengajuan</dt>
          <dd class="col-sm-8">{{ optional($adoption->request_date)->format('d M Y H:i') }}</dd>

          <dt class="col-sm-4">Status Saat Ini</dt>
          <dd class="col-sm-8"><span class="badge-status status-{{ $adoption->status }}">{{ ucfirst($adoption->status) }}</span></dd>
        </dl>

        <hr>

        <form action="{{ route('admin.adoptions.status', $adoption->adoption_request_id) }}" method="POST" class="row g-3">
          @csrf
          <div class="col-md-5">
            <label class="form-label fw-semibold">Keputusan Status</label>
            <select name="status" class="form-select">
              @foreach(['pending' => 'Pending', 'approved' => 'Setujui (Approve)', 'rejected' => 'Tolak (Reject)'] as $val => $lbl)
                <option value="{{ $val }}" @selected($adoption->status === $val)>{{ $lbl }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-7">
            <label class="form-label fw-semibold">Catatan Admin</label>
            <input type="text" name="admin_note" class="form-control" value="{{ old('admin_note', $adoption->admin_note) }}" placeholder="Opsional: pesan untuk pemohon">
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary fw-bold">Simpan Keputusan</button>
          </div>
        </form>
      </section>
    </div>
  </div>
@endsection
