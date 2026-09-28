const detailContainer = document.getElementById("detailContainer");
const params = new URLSearchParams(window.location.search);
const id = Number(params.get("id"));

function getData() {
  return JSON.parse(localStorage.getItem("adoptionRequests") || "[]");
}

function saveData(data) {
  localStorage.setItem("adoptionRequests", JSON.stringify(data));
}

function syncHistory(id, status) {
  const history = JSON.parse(localStorage.getItem("adoptionHistory") || "[]");
  const index = history.findIndex(item => item.id === id);

  if (index !== -1) {
    history[index].status = status;
    localStorage.setItem("adoptionHistory", JSON.stringify(history));
  }
}

function formatDate(date) {
  if (!date) return "-";

  return new Date(date).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric"
  });
}

function getStatusClass(status) {
  if (status === "Disetujui") return "approved";
  if (status === "Ditolak") return "rejected";
  return "waiting";
}

function renderDetail() {
  const data = getData();
  const item = data.find(request => request.id === id);

  if (!item) {
    detailContainer.innerHTML = `
      <div class="empty-detail">
        Data pengajuan adopsi tidak ditemukan.
      </div>
    `;
    return;
  }

  const pending = item.status === "Menunggu Verifikasi";

  detailContainer.innerHTML = `
    <div class="detail-grid">
      <div class="detail-pet">
        <img src="${item.image}" alt="${item.hewan}">
        <div class="detail-pet-info">
          <h3>${item.hewan}</h3>
          <p>Ras: ${item.ras}</p>
          <p>Gender: ${item.gender}</p>
          <p>Umur: ${item.umur}</p>
        </div>
      </div>

      <div class="detail-box">
        <h3>Data Pemohon</h3>

        <div class="detail-row">
          <span class="detail-label">Nama Lengkap</span>
          <span class="detail-value">${item.nama || "-"}</span>
        </div>

        <div class="detail-row">
          <span class="detail-label">Email</span>
          <span class="detail-value">${item.email || "-"}</span>
        </div>

        <div class="detail-row">
          <span class="detail-label">Nomor Telepon</span>
          <span class="detail-value">${item.telepon || "-"}</span>
        </div>

        <div class="detail-row">
          <span class="detail-label">Alamat</span>
          <span class="detail-value">${item.alamat || "-"}</span>
        </div>

        <div class="detail-row">
          <span class="detail-label">Alasan Adopsi</span>
          <span class="detail-value">${item.alasan || "-"}</span>
        </div>

        <div class="detail-row">
          <span class="detail-label">Tanggal Pengajuan</span>
          <span class="detail-value">${formatDate(item.tanggal)}</span>
        </div>

        <div class="detail-row">
          <span class="detail-label">Status</span>
          <span class="detail-value">
            <span class="detail-status ${getStatusClass(item.status)}">
              ${item.status}
            </span>
          </span>
        </div>

        ${pending ? `
          <div class="detail-actions">
            <button class="detail-action approve-btn" onclick="ubahStatus('Disetujui')">Acc Pengajuan</button>
            <button class="detail-action reject-btn" onclick="ubahStatus('Ditolak')">Tolak Pengajuan</button>
          </div>
        ` : ""}
      </div>
    </div>
  `;
}

function ubahStatus(status) {
  const data = getData();
  const index = data.findIndex(item => item.id === id);

  if (index === -1) return;

  data[index].status = status;
  saveData(data);
  syncHistory(id, status);

  alert(`Pengajuan ${data[index].hewan} berhasil ${status === "Disetujui" ? "disetujui" : "ditolak"}.`);

  renderDetail();
}

renderDetail();