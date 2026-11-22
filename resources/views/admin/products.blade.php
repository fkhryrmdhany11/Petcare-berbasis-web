@extends('layouts.admin')

@section('title', 'Kelola Produk Toko | Admin PawPaw')
@section('heading', 'Kelola Produk Toko')
@section('subheading', 'Tambah, perbarui stok & harga, atau hapus produk di katalog toko.')

@section('content')
  <section class="admin-card">
    <h2>{{ $editProduct ? '✏️ Edit Produk #' . $editProduct->product_id : '➕ Tambah Produk Baru' }}</h2>
    <form
      action="{{ $editProduct ? route('admin.products.update', $editProduct->product_id) : route('admin.products.store') }}"
      method="POST"
      enctype="multipart/form-data"
      class="row g-3 mb-3"
    >
      @csrf
      <div class="col-md-3">
        <label class="form-label fw-semibold">Nama Produk</label>
        <input type="text" name="product_name" class="form-control" value="{{ old('product_name', $editProduct->product_name ?? '') }}" required>
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold">Kategori</label>
        <select name="category" class="form-select" required>
          @foreach(['Makanan', 'Perawatan', 'Vitamin', 'Aksesoris'] as $cat)
            <option value="{{ $cat }}" @selected(old('category', $editProduct->category ?? '') === $cat)>{{ $cat }}</option>
          @endforeach
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label fw-semibold">Harga (Rp)</label>
        <input type="number" name="price" class="form-control" min="0" value="{{ old('price', isset($editProduct) ? (int) $editProduct->price : 45000) }}" required>
      </div>
      <div class="col-md-1">
        <label class="form-label fw-semibold">Stok</label>
        <input type="number" name="stock" class="form-control" min="0" value="{{ old('stock', $editProduct->stock ?? 15) }}" required>
      </div>
      <div class="col-md-4">
        <label class="form-label fw-semibold">Upload Foto Produk (JPG/PNG)</label>
        <input type="file" name="image_file" class="form-control" accept="image/*">
        <input type="hidden" name="image" value="{{ old('image', $editProduct->image ?? 'assets/img/shop/whiskas.png') }}">
      </div>
      <div class="col-md-10">
        <label class="form-label fw-semibold">Deskripsi</label>
        <input type="text" name="description" class="form-control" value="{{ old('description', $editProduct->description ?? '') }}" placeholder="Deskripsi produk...">
      </div>
      <div class="col-md-2 d-flex align-items-end gap-2">
        <button type="submit" class="btn btn-primary w-100 fw-bold">{{ $editProduct ? 'Update' : 'Simpan' }}</button>
        @if($editProduct)
          <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Batal</a>
        @endif
      </div>
    </form>
  </section>

  <section class="admin-card">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
      <h2 class="mb-0">📦 Daftar Produk ({{ $products->count() }})</h2>
      <input type="search" class="form-control w-auto" placeholder="Cari produk..." data-admin-filter="#productsTable">
    </div>

    <div class="table-responsive">
      <table class="table align-middle" id="productsTable">
        <thead>
          <tr>
            <th>ID</th>
            <th>Produk</th>
            <th>Kategori</th>
            <th>Harga</th>
            <th>Stok</th>
            <th class="text-end">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($products as $product)
            <tr>
              <td>#{{ $product->product_id }}</td>
              <td>
                <div class="d-flex align-items-center gap-2">
                  <img src="{{ asset($product->image) }}" alt="{{ $product->product_name }}" style="width:42px;height:42px;object-fit:contain;border-radius:8px;background:#f8fafc;padding:4px;">
                  <div>
                    <div class="fw-bold">{{ $product->product_name }}</div>
                    <small class="text-muted">{{ $product->description }}</small>
                  </div>
                </div>
              </td>
              <td><span class="badge bg-info-subtle text-info-emphasis">{{ $product->category }}</span></td>
              <td class="fw-semibold">Rp{{ number_format($product->price, 0, ',', '.') }}</td>
              <td>
                <span class="badge {{ $product->stock > 0 ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                  {{ $product->stock }} unit
                </span>
              </td>
              <td class="text-end">
                <div class="d-inline-flex gap-1">
                  <a href="{{ route('admin.products.index', ['edit' => $product->product_id]) }}" class="btn btn-sm btn-outline-primary">Edit</a>
                  <form action="{{ route('admin.products.destroy', $product->product_id) }}" method="POST" data-confirm="Hapus produk {{ $product->product_name }}?">
                    @csrf
                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada produk terdaftar.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </section>
@endsection
