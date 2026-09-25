// 1. Inisialisasi Data & Elemen HTML
const daftarBooking = JSON.parse(localStorage.getItem('pawData') || '[]');
const tabelBody = document.getElementById('tabelAdmin');
const modalDetailElement = new bootstrap.Modal(document.getElementById('modalDetail'));
const modalContent = document.getElementById('modalBody');

// Fungsi Format Tanggal Bahasa Indonesia
function formatTanggal(tanggalString) {
  if (!tanggalString) return '-';
  return new Date(tanggalString).toLocaleString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).replace(' pukul ', ', ');
}

// 2. Fungsi Menampilkan Data ke Tabel
function renderTabel() {
  if (daftarBooking.length === 0) {
    tabelBody.innerHTML = `
      <tr>
        <td colspan="6" class="text-center text-muted py-4">
          Belum ada permintaan booking masuk dari pelanggan.
        </td>
      </tr>`;
    return;
  }

  tabelBody.innerHTML = '';

  daftarBooking.forEach((item, index) => {
    const namaHewan = item.pn || '-';
    const jenisHewan = item.pt || 'Hewan';
    const jenisLayanan = item.st || '-';
    const tanggalWaktu = formatTanggal(item.bd);
    const isDisetujui = item.status === 'Disetujui';

    const statusBadge = isDisetujui
      ? '<span class="badge bg-success px-3 py-2">Disetujui</span>'
      : '<span class="badge bg-warning text-dark px-3 py-2">Menunggu Konfirmasi</span>';

    const tombolProses = isDisetujui
      ? '<button class="btn btn-sm btn-secondary" disabled>Selesai</button>'
      : `<button class="btn btn-sm btn-success" onclick="prosesBooking(${index})">Proses</button>`;

    const baris = document.createElement('tr');
    baris.innerHTML = `
      <td class="text-center fw-bold">${index + 1}</td>
      <td>
        <span class="fw-bold text-dark">${namaHewan}</span>
        <br><small class="text-muted">(${jenisHewan})</small>
      </td>
      <td>${jenisLayanan}</td>
      <td>${tanggalWaktu}</td>
      <td class="text-center">${statusBadge}</td>
      <td class="text-center">
        <button class="btn btn-sm btn-info text-white me-1" onclick="lihatDetail(${index})">Detail</button>
        ${tombolProses}
      </td>
    `;
    tabelBody.appendChild(baris);
  });
}

// 3. Fungsi Menampilkan Pop-up Detail
function lihatDetail(index) {
  const item = daftarBooking[index];
  const statusBadge = item.status === 'Disetujui' 
    ? '<span class="badge bg-success">Disetujui</span>' 
    : '<span class="badge bg-warning text-dark">Menunggu Konfirmasi</span>';

  modalContent.innerHTML = `
    <div class="mb-2"><strong>Nama Pemilik:</strong> ${item.on || '-'}</div>
    <div class="mb-2"><strong>Nama Hewan:</strong> ${item.pn || '-'}</div>
    <div class="mb-2"><strong>Jenis Hewan:</strong> ${item.pt || '-'}</div>
    <div class="mb-2"><strong>Layanan Booking:</strong> ${item.st || '-'}</div>
    <div class="mb-2"><strong>Jadwal:</strong> ${formatTanggal(item.bd)}</div>
    <div class="mb-2"><strong>Status saat ini:</strong> ${statusBadge}</div>
  `;
  modalDetailElement.show();
}

// 4. Fungsi Mengubah Status Booking
function prosesBooking(index) {
  daftarBooking[index].status = 'Disetujui';
  localStorage.setItem('pawData', JSON.stringify(daftarBooking));
  alert('Permintaan booking untuk ' + (daftarBooking[index].pn || 'hewan') + ' berhasil diproses!');
  renderTabel();
}

// Jalankan rendering tabel saat file di-load
document.addEventListener('DOMContentLoaded', renderTabel);