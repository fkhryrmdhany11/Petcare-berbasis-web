@extends('layouts.admin')

@section('title', 'Kelola Hewan Adopsi | Admin PawPaw')
@section('heading', 'Kelola Hewan Adopsi')
@section('subheading', 'Tambah atau perbarui daftar hewan yang siap diadopsi oleh pelanggan.')

@section('content')
  <section class="admin-card">
    <h2>{{ $editAnimal ? '✏️ Edit Hewan #' . $editAnimal->animal_id : '➕ Tambah Hewan Adopsi' }}</h2>
    <form
      action="{{ $editAnimal ? route('admin.animals.update', $editAnimal->animal_id) : route('admin.animals.store') }}"
      method="POST"
      enctype="multipart/form-data"
      class="row g-3"
    >
      @csrf
      <div class="col-md-3">
        <label class="form-label fw-semibold">Nama Hewan</label>
        <input type="text" name="nama" class="form-control" value="{{ old('nama', $editAnimal->nama ?? '') }}" required>
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold">Spesies</label>
        <select name="species" class="form-select" required>
          @foreach(['Kucing', 'Anjing'] as $sp)
            <option value="{{ $sp }}" @selected(old('species', $editAnimal->species ?? '') === $sp)>{{ $sp }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold">Ras (Breed)</label>
        <input type="text" name="breed" class="form-control" value="{{ old('breed', $editAnimal->breed ?? '') }}" required>
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold">Jenis Kelamin</label>
        <select name="gender" class="form-select" required>
          @foreach(['Jantan', 'Betina'] as $g)
            <option value="{{ $g }}" @selected(old('gender', $editAnimal->gender ?? '') === $g)>{{ $g }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold">Usia</label>
        <input type="text" name="age" class="form-control" value="{{ old('age', $editAnimal->age ?? '1 Tahun') }}" required>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold">Status</label>
        <select name="status" class="form-select" required>
          @foreach(['available' => 'Tersedia (Available)', 'adopted' => 'Sudah Diadopsi (Adopted)', 'unavailable' => 'Tidak Tersedia'] as $val => $label)
            <option value="{{ $val }}" @selected(old('status', $editAnimal->status ?? 'available') === $val)>{{ $label }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label fw-semibold">Upload Foto Hewan (JPG/PNG)</label>
        <input type="file" name="image_file" class="form-control" accept="image/*">
        <input type="hidden" name="image" value="{{ old('image', $editAnimal->image ?? 'assets/img/meng.png') }}">
      </div>
      <div class="col-md-4">
        <label class="form-label fw-semibold">Deskripsi Karakter / Kesehatan</label>
        <input type="text" name="description" class="form-control" value="{{ old('description', $editAnimal->description ?? '') }}">
      </div>
      <div class="col-md-2 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary w-100 fw-bold">{{ $editAnimal ? 'Update' : 'Simpan' }}</button>
        @if($editAnimal)
          <a href="{{ route('admin.animals.index') }}" class="btn btn-outline-secondary">Batal</a>
        @endif
      </div>
    </form>
  </section>

  <section class="admin-card">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
      <h2 class="mb-0">🐾 Daftar Hewan Adopsi ({{ $animals->count() }})</h2>
      <input type="search" class="form-control w-auto" placeholder="Cari hewan..." data-admin-filter="#animalsTable">
    </div>

    <div class="table-responsive">
      <table class="table align-middle" id="animalsTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Hewan</th>
            <th>Spesies / Ras</th>
            <th>Gender / Usia</th>
            <th>Status</th>
            <th class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($animals as $animal)
            <tr>
              <td>#{{ $animal->animal_id }}</td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <img src="{{ asset($animal->image) }}" alt="{{ $animal->nama }}" style="width:44px;height:44px;object-fit:cover;border-radius:10px;">
                  <div>
                    <div class="fw-bold">{{ $animal->nama }}</div>
                    <small class="text-muted">{{ $animal->description }}</small>
                  </div>
                </div>
              </td>
              <td>{{ $animal->species }} • {{ $animal->breed }}</td>
              <td>{{ $animal->gender }} ({{ $animal->age }})</td>
              <td><span class="badge-status status-{{ $animal->status }}">{{ ucfirst($animal->status) }}</span></td>
              <td class="text-end">
                <div class="d-inline-flex gap-1">
                  <a href="{{ route('admin.animals.index', ['edit' => $animal->animal_id]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                  <form action="{{ route('admin.animals.destroy', $animal->animal_id) }}" method="POST" data-confirm="Hapus data hewan {{ $animal->nama }}?">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada data hewan adopsi.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
@endsection
