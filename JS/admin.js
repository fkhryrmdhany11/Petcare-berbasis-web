(() => {
  "use strict";

  const KUNCI_STORAGE = "pawData";

  // ==========================================
  // 1. DATA DUMMY AWAL (Jika LocalStorage kosong)
  // ==========================================
  const DATA_DUMMY = [
    {
      id: "BK-88210",
      namaPemilik: "bujas",
      namaHewan: "Milo",
      jenisHewan: "Kucing",
      jenisLayanan: "Jadwalkan Vaksin",
      harga: 150000,
      tanggalBooking: "2026-10-05T09:00",
      status: "Menunggu Konfirmasi",
      alasanTolak: ""
    },
    {
      id: "BK-44102",
      namaPemilik: "messi",
      namaHewan: "pawik",
      jenisHewan: "Anjing",
      jenisLayanan: "Panggil Dokter Ke Rumah",
      harga: 250000,
      tanggalBooking: "2026-09-20T14:00",
      status: "Selesai",
      alasanTolak: ""
    },
    {
      id: "BK-12093",
      namaPemilik: "Chocky sitohang",
      namaHewan: "alex",
      jenisHewan: "Kucing",
      jenisLayanan: "Booking Jadwal Periksa",
      harga: 100000,
      tanggalBooking: "2026-09-22T11:00",
      status: "Ditolak",
      alasanTolak: "Klinik tutup pada tanggal libur nasional."
    }
  ];

  // Helper untuk membaca data
  function ambilDataBooking() {
    try {
      const data = JSON.parse(localStorage.getItem(KUNCI_STORAGE));
      // Jika data belum ada di LocalStorage, gunakan DATA_DUMMY
      if (!data || data.length === 0) {
        localStorage.setItem(KUNCI_STORAGE, JSON.stringify(DATA_DUMMY));
        return DATA_DUMMY;
      }
      return Array.isArray(data) ? data : [];
    } catch (error) {
      return DATA_DUMMY;
    }
  }

  // Format angka ke format Rupiah
  function formatRupiah(nomor) {
    return new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0 }).format(nomor || 0);
  }

  // Format tanggal rapi
  function formatTanggal(stringTanggal) {
    if (!stringTanggal) return "-";
    const d = new Date(stringTanggal);
    if (isNaN(d.getTime())) return "-";
    return d.toLocaleString("id-ID", {
      day: "2-digit", month: "long", year: "numeric", hour: "2-digit", minute: "2-digit"
    }).replace(" pukul ", ", ");
  }

  // Badge Status Warna
  function dapatkanBadgeStatus(status) {
    if (status === "Disetujui") return '<span class="badge bg-success px-3 py-2">Disetujui</span>';
    if (status === "Selesai") return '<span class="badge bg-primary px-3 py-2">Selesai</span>';
    if (status === "Ditolak") return '<span class="badge bg-danger px-3 py-2">Ditolak</span>';
    return '<span class="badge bg-warning text-dark px-3 py-2">Menunggu Konfirmasi</span>';
  }

  // State Utama
  let daftarBooking = ambilDataBooking();
  let indeksDataAktif = null;

  // Elemen DOM
  const tabelBody = document.getElementById("tabelBodyBooking");
  const halamanTabel = document.getElementById("halamanTabelBooking");
  const halamanDetail = document.getElementById("halamanDetailBooking");
  const kontenDetail = document.getElementById("kontenDetailBooking");
  const tombolKembali = document.getElementById("tombolKembali");

  // Modal Tolak
  const elModalTolak = document.getElementById("modalAlasanTolak");
  const bsModalTolak = elModalTolak ? new bootstrap.Modal(elModalTolak) : null;

  // ==========================================
  // 2. RENDER TABEL BOOKING
  // ==========================================
  function tampilkanTabel() {
    tabelBody.innerHTML = "";

    if (daftarBooking.length === 0) {
      tabelBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted py-5">Belum ada data booking.</td></tr>';
      return;
    }

    daftarBooking.forEach((item, index) => {
      const baris = document.createElement("tr");

      // Tombol Edit (Selalu Ada)
      let tombolAksi = `
        <button class="btn btn-sm btn-info text-white me-1" title="Detail / Edit Booking" onclick="bukaDetailBooking(${index})">
          <i class="fa-solid fa-pen-to-square"></i>
        </button>
      `;

      // NOTE: Tombol HAPUS CUMA ADA jika statusnya "Selesai"
      if (item.status === "Selesai") {
        tombolAksi += `
          <button class="btn btn-sm btn-danger" title="Hapus Data" onclick="hapusBooking(${index})">
            <i class="fa-solid fa-trash"></i>
          </button>
        `;
      }

      baris.innerHTML = `
        <td class="text-center fw-bold">${index + 1}</td>
        <td>
          <span class="fw-bold text-dark">${item.namaHewan || "-"}</span><br>
          <small class="text-muted">(${item.jenisHewan || "Hewan"})</small>
        </td>
        <td>${item.jenisLayanan || "-"}</td>
        <td>${formatTanggal(item.tanggalBooking)}</td>
        <td class="text-center">${dapatkanBadgeStatus(item.status)}</td>
        <td class="text-center text-nowrap">${tombolAksi}</td>
      `;

      tabelBody.appendChild(baris);
    });
  }

  // ==========================================
  // 3. LOGIKA HALAMAN DETAIL BOOKING
  // ==========================================
  window.bukaDetailBooking = function(index) {
    indeksDataAktif = index;
    const item = daftarBooking[index];

    let htmlDetail = `
      <div class="row mb-4">
        <div class="col-md-6">
          <p class="text-muted mb-1 small fw-bold">ID BOOKING</p>
          <h4 class="fw-bold text-primary">${item.id || '-'}</h4>
        </div>
        <div class="col-md-6 text-md-end">
          <p class="text-muted mb-1 small fw-bold">STATUS SAAT INI</p>
          ${dapatkanBadgeStatus(item.status)}
        </div>
      </div>

      <div class="row g-3">
        <div class="col-md-6">
          <div class="p-3 border rounded bg-light">
            <small class="text-muted d-block">Nama Customer / Pemilik</small>
            <strong class="fs-6">${item.namaPemilik || "-"}</strong>
          </div>
        </div>
        <div class="col-md-6">
          <div class="p-3 border rounded bg-light">
            <small class="text-muted d-block">Nama & Jenis Hewan</small>
            <strong class="fs-6">${item.namaHewan} (${item.jenisHewan})</strong>
          </div>
        </div>
        <div class="col-md-6">
          <div class="p-3 border rounded bg-light">
            <small class="text-muted d-block">Jenis Layanan</small>
            <strong class="fs-6">${item.jenisLayanan}</strong>
          </div>
        </div>
        <div class="col-md-6">
          <div class="p-3 border rounded bg-light">
            <small class="text-muted d-block">Estimasi Biaya</small>
            <strong class="fs-6 text-success">${formatRupiah(item.harga)}</strong>
          </div>
        </div>
        <div class="col-12">
          <div class="p-3 border rounded bg-light">
            <small class="text-muted d-block">Jadwal Pertemuan</small>
            <strong class="fs-6">${formatTanggal(item.tanggalBooking)}</strong>
          </div>
        </div>
      </div>
    `;

    // Jika statusnya DITOLAK, tampilkan alasan penolakan
    if (item.status === "Ditolak" && item.alasanTolak) {
      htmlDetail += `
        <div class="alert alert-danger mt-3">
          <strong>Alasan Penolakan:</strong> ${item.alasanTolak}
        </div>
      `;
    }

    // Tombol ACC / Tolak jika status masih "Menunggu Konfirmasi"
    if (item.status === "Menunggu Konfirmasi") {
      htmlDetail += `
        <hr class="my-4">
        <div class="d-flex gap-2">
          <button class="btn btn-success px-4 fw-bold" onclick="prosesStatus('Disetujui')">
            <i class="fa-solid fa-check"></i> ACC / Setujui Booking
          </button>
          <button class="btn btn-outline-danger px-4 fw-bold" onclick="bukaModalTolak()">
            <i class="fa-solid fa-xmark"></i> Tolak Booking
          </button>
        </div>
      `;
    } else if (item.status === "Disetujui") {
      htmlDetail += `
        <hr class="my-4">
        <button class="btn btn-primary px-4 fw-bold" onclick="prosesStatus('Selesai')">
          <i class="fa-solid fa-flag-checkered"></i> Tandai Selesai
        </button>
      `;
    }

    kontenDetail.innerHTML = htmlDetail;

    // Pindah Tampilan (Tabel Sembunyi -> Detail Tampil)
    halamanTabel.classList.add("d-none");
    halamanDetail.classList.remove("d-none");
  };

  // Function Mengubah Status (Disetujui / Selesai)
  window.prosesStatus = function(statusBaru, alasan = "") {
    if (indeksDataAktif === null) return;

    daftarBooking[indeksDataAktif].status = statusBaru;
    if (alasan) {
      daftarBooking[indeksDataAktif].alasanTolak = alasan;
    }

    // Simpan ke LocalStorage
    localStorage.setItem(KUNCI_STORAGE, JSON.stringify(daftarBooking));

    // Refresh Tabel & Kembali
    tampilkanTabel();
    tombolKembali.click();
  };

  // Function Modal Penolakan
  window.bukaModalTolak = function() {
    document.getElementById("inputAlasan").value = "";
    bsModalTolak.show();
  };

  document.getElementById("tombolSimpanTolak").addEventListener("click", () => {
    const alasan = document.getElementById("inputAlasan").value.trim();
    if (!alasan) {
      alert("Harap isi alasan penolakan terlebih dahulu!");
      return;
    }
    bsModalTolak.hide();
    prosesStatus("Ditolak", alasan);
  });

  // ==========================================
  // 4. HAPUS DATA (Hanya untuk yang 'Selesai')
  // ==========================================
  window.hapusBooking = function(index) {
    if (confirm("Apakah Anda yakin ingin menghapus baris data booking ini?")) {
      daftarBooking.splice(index, 1);
      localStorage.setItem(KUNCI_STORAGE, JSON.stringify(daftarBooking));
      tampilkanTabel();
    }
  };

  // Tombol Kembali Ke Tabel
  tombolKembali.addEventListener("click", () => {
    halamanDetail.classList.add("d-none");
    halamanTabel.classList.remove("d-none");
    indeksDataAktif = null;
  });

  // Jalankan Awal
  tampilkanTabel();
})();