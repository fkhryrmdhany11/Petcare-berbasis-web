// ============================================================
// DATA DUMMY - nanti bakal diganti dengan data asli dari backend yh
// ============================================================

let adoptionRequests = [
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


// ============================================================
// Pengaturan
// ============================================================

// Data dianggap "selesai" kalau sudah diputuskan (disetujui / ditolak).
// Hanya data selesai yang boleh dihapus.
function isSelesai(status) {

  return status === "approved" || status === "rejected";

}

const STATUS_LABEL = {
  pending: "Menunggu",
  approved: "Disetujui",
  rejected: "Ditolak"
};

const ICON_EDIT = `
  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
       stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <path d="M12 20h9"/>
    <path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>
  </svg>
`;

const ICON_DELETE = `
  <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
       stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
    <polyline points="3 6 5 6 21 6"/>
    <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
    <path d="M10 11v6"/>
    <path d="M14 11v6"/>
    <path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
  </svg>
`;


// ============================================================
// Helper
// ============================================================

function escapeHtml(text) {

  return String(text)
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;");

}


function formatDate(isoDate) {

  return new Date(isoDate).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "short",
    year: "numeric"
  });

}


function countByStatus(status) {

  return adoptionRequests.filter(
    (request) => request.status === status
  ).length;

}


// ============================================================
// Render
// ============================================================

function renderStats() {

  document.getElementById("totalRequest").textContent =
    adoptionRequests.length;

  document.getElementById("pendingRequest").textContent =
    countByStatus("pending");

  document.getElementById("approvedRequest").textContent =
    countByStatus("approved");

  document.getElementById("rejectedRequest").textContent =
    countByStatus("rejected");

}


function renderTable() {

  const tableBody =
    document.getElementById("requestTable");

  if (adoptionRequests.length === 0) {

    tableBody.innerHTML = `
      <tr>
        <td colspan="11" class="aa-empty">
          Belum ada permintaan adopsi.
        </td>
      </tr>
    `;

    return;

  }

  tableBody.innerHTML = adoptionRequests.map((request, index) => {

    const canDelete = isSelesai(request.status);

    return `
      <tr>

        <td>${index + 1}</td>

        <td class="aa-id">${escapeHtml(request.id)}</td>

        <td>${escapeHtml(request.name)}</td>

        <td>${escapeHtml(request.pet)}</td>

        <td>${escapeHtml(request.email)}</td>

        <td>${escapeHtml(request.phone)}</td>

        <td>
          <span class="aa-truncate" title="${escapeHtml(request.address)}">
            ${escapeHtml(request.address)}
          </span>
        </td>

        <td>
          <span class="aa-truncate" title="${escapeHtml(request.reason)}">
            ${escapeHtml(request.reason)}
          </span>
        </td>

        <td>
          <span class="aa-badge ${request.status}">
            ${STATUS_LABEL[request.status]}
          </span>
        </td>

        <td>${formatDate(request.date)}</td>

        <td>
          <div class="aa-actions">

            <a
              href="admin-adoption-detail.html?id=${encodeURIComponent(request.id)}"
              class="aa-btn edit"
              title="Edit / lihat detail"
              aria-label="Edit ${escapeHtml(request.id)}"
            >
              ${ICON_EDIT}
            </a>

            <button
              type="button"
              class="aa-btn delete"
              data-id="${escapeHtml(request.id)}"
              title="${canDelete ? "Hapus data" : "Hanya data selesai yang bisa dihapus"}"
              aria-label="Hapus ${escapeHtml(request.id)}"
              ${canDelete ? "" : "disabled"}
            >
              ${ICON_DELETE}
            </button>

          </div>
        </td>

      </tr>
    `;

  }).join("");

}


// Dipanggil oleh tombol Refresh di HTML (onclick="loadAdoptionData()")
function loadAdoptionData() {

  renderStats();

  renderTable();

}


// ============================================================
// Hapus data (dengan modal konfirmasi)
// ============================================================

const deleteModal =
  new bootstrap.Modal(document.getElementById("deleteModal"));

const deleteModalText =
  document.getElementById("deleteModalText");

const confirmDeleteButton =
  document.getElementById("confirmDeleteButton");

let pendingDeleteId = null;


document.getElementById("requestTable").addEventListener("click", (event) => {

  const deleteButton =
    event.target.closest(".aa-btn.delete");

  if (!deleteButton || deleteButton.disabled) {
    return;
  }

  pendingDeleteId = deleteButton.dataset.id;

  const request = adoptionRequests.find(
    (item) => item.id === pendingDeleteId
  );

  deleteModalText.textContent =
    `Data ${request.id} atas nama ${request.name} akan dihapus dan tidak bisa dikembalikan.`;

  deleteModal.show();

});


confirmDeleteButton.addEventListener("click", () => {

  adoptionRequests = adoptionRequests.filter(
    (request) => request.id !== pendingDeleteId
  );

  pendingDeleteId = null;

  deleteModal.hide();

  loadAdoptionData();

});


// ============================================================
// Render awal
// ============================================================

loadAdoptionData();