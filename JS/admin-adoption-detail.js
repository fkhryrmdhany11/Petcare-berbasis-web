const adoptionRequests = [
  {
    id: "ADP-001",
    name: "Budi Santoso",
    pet: "Siti (Anggora)",
    email: "budi.santoso@email.com",
    phone: "0812-3456-7801",
    address: "Jl. Panam Raya No. 12, Tampan, Pekanbaru",
    reason: "Sudah lama ingin punya kucing, di rumah ada halaman luas dan keluarga sangat mendukung.",
    status: "pending",
    date: "2026-09-28"
  },
  {
    id: "ADP-002",
    name: "Rani Putri",
    pet: "Noe (Kelinci Rex)",
    email: "rani.putri@email.com",
    phone: "0813-2244-5502",
    address: "Jl. Hangtuah Ujung No. 5, Pekanbaru",
    reason: "Anak saya suka kelinci dan kami sudah menyiapkan kandang serta pakan yang sesuai.",
    status: "pending",
    date: "2026-09-27"
  },
  {
    id: "ADP-003",
    name: "Dimas Pratama",
    pet: "Jeno (African Pygmy)",
    email: "dimas.pratama@email.com",
    phone: "0852-1122-3303",
    address: "Jl. Garuda Sakti KM 3, Pekanbaru",
    reason: "Punya lahan kecil di belakang rumah dan berpengalaman merawat kambing.",
    status: "approved",
    date: "2026-09-25"
  },
  {
    id: "ADP-004",
    name: "Ayu Lestari",
    pet: "Mochi (Persia)",
    email: "ayu.lestari@email.com",
    phone: "0821-9988-7704",
    address: "Jl. Soekarno Hatta No. 88, Pekanbaru",
    reason: "Ingin teman untuk kucing saya yang sekarang, sudah rutin vaksin dan steril.",
    status: "approved",
    date: "2026-09-24"
  },
  {
    id: "ADP-005",
    name: "Fajar Nugroho",
    pet: "Jubariyah (Bengala)",
    email: "fajar.nugroho@email.com",
    phone: "0857-6655-4405",
    address: "Jl. Riau No. 21, Pekanbaru",
    reason: "Belum punya pengalaman tapi ingin belajar merawat kucing.",
    status: "rejected",
    date: "2026-09-23"
  },
  {
    id: "ADP-006",
    name: "Sinta Dewi",
    pet: "Luna (Golden Retriever)",
    email: "sinta.dewi@email.com",
    phone: "0811-7788-9906",
    address: "Jl. Arifin Ahmad No. 40, Pekanbaru",
    reason: "Tinggal di rumah dengan halaman besar dan punya waktu untuk mengajak anjing jalan setiap hari.",
    status: "approved",
    date: "2026-09-21"
  },
  {
    id: "ADP-007",
    name: "Rizky Ramadhan",
    pet: "Oreo (Kelinci Holland Lop)",
    email: "rizky.ramadhan@email.com",
    phone: "0878-3344-5507",
    address: "Jl. Tuanku Tambusai No. 9, Pekanbaru",
    reason: "Tinggal di kos dan pemilik kos belum mengizinkan hewan peliharaan.",
    status: "rejected",
    date: "2026-09-20"
  }
];

const STATUS_LABEL = {
  pending: "Menunggu",
  approved: "Disetujui",
  rejected: "Ditolak"
};

function escapeHtml(text) {
  return String(text ?? "-")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

function formatDate(date) {
  return new Date(date).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric"
  });
}

function getStatusBadge(status) {
  return `
    <span class="badge ${
      status === "pending"
        ? "bg-warning text-dark"
        : status === "approved"
        ? "bg-success"
        : "bg-danger"
    } px-3 py-2">
      ${STATUS_LABEL[status]}
    </span>
  `;
}

const params = new URLSearchParams(window.location.search);
const adoptionId = params.get("id");

const request = adoptionRequests.find(
  item => item.id === adoptionId
);

const detailContainer = document.getElementById("detailContainer");

function renderDetail() {
  if (!request) {
    detailContainer.innerHTML = `
      <div class="text-center text-muted py-5">
        Data pengajuan adopsi tidak ditemukan.
      </div>
    `;
    return;
  }

  detailContainer.innerHTML = `
    <div class="adoption-detail">

      <div class="adoption-pet">

        <div class="adoption-pet-placeholder">
          <i class="fa-solid fa-paw"></i>
          <span>${escapeHtml(request.pet)}</span>
        </div>

        <div class="adoption-pet-info">
          <h3>${escapeHtml(request.pet)}</h3>

          <p>
            <strong>ID Pengajuan:</strong>
            ${escapeHtml(request.id)}
          </p>

          <p>
            <strong>Tanggal:</strong>
            ${escapeHtml(formatDate(request.date))}
          </p>

          <p>
            <strong>Status:</strong>
            ${getStatusBadge(request.status)}
          </p>
        </div>

      </div>

      <div class="adoption-data">

        <h3>Data Pemohon</h3>

        <div class="adoption-row">
          <span class="adoption-label">Nama Lengkap</span>
          <span class="adoption-value">
            ${escapeHtml(request.name)}
          </span>
        </div>

        <div class="adoption-row">
          <span class="adoption-label">Email</span>
          <span class="adoption-value">
            ${escapeHtml(request.email)}
          </span>
        </div>

        <div class="adoption-row">
          <span class="adoption-label">Nomor Telepon</span>
          <span class="adoption-value">
            ${escapeHtml(request.phone)}
          </span>
        </div>

        <div class="adoption-row">
          <span class="adoption-label">Alamat</span>
          <span class="adoption-value">
            ${escapeHtml(request.address)}
          </span>
        </div>

        <div class="adoption-row">
          <span class="adoption-label">Hewan</span>
          <span class="adoption-value">
            ${escapeHtml(request.pet)}
          </span>
        </div>

        <div class="adoption-row">
          <span class="adoption-label">Alasan Adopsi</span>
          <span class="adoption-value">
            ${escapeHtml(request.reason)}
          </span>
        </div>

        <div class="adoption-row">
          <span class="adoption-label">Tanggal Pengajuan</span>
          <span class="adoption-value">
            ${escapeHtml(formatDate(request.date))}
          </span>
        </div>

        <div class="adoption-row">
          <span class="adoption-label">Status</span>
          <span class="adoption-value">
            ${getStatusBadge(request.status)}
          </span>
        </div>

        ${
          request.status === "pending"
            ? `
              <div class="adoption-actions">

                <button
                  type="button"
                  class="btn btn-success"
                  onclick="ubahStatus('approved')"
                >
                  <i class="fa-solid fa-check"></i>
                  Acc Pengajuan
                </button>

                <button
                  type="button"
                  class="btn btn-danger"
                  onclick="ubahStatus('rejected')"
                >
                  <i class="fa-solid fa-xmark"></i>
                  Tolak Pengajuan
                </button>

              </div>
            `
            : ""
        }

      </div>

    </div>
  `;
}

function ubahStatus(status) {
  request.status = status;

  alert(
    status === "approved"
      ? "Pengajuan berhasil disetujui."
      : "Pengajuan berhasil ditolak."
  );

  renderDetail();
}

window.ubahStatus = ubahStatus;

renderDetail();