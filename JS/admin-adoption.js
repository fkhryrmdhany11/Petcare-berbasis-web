const table = document.getElementById("adoptionTable");

function getRequests() {
  return JSON.parse(localStorage.getItem("adoptionRequests") || "[]");
}

function saveRequests(data) {
  localStorage.setItem("adoptionRequests", JSON.stringify(data));
}

function syncHistory(id, status, updatedData = null) {
  const history = JSON.parse(localStorage.getItem("adoptionHistory") || "[]");
  const index = history.findIndex(item => item.id === id);

  if (index !== -1) {
    history[index] = updatedData
      ? { ...history[index], ...updatedData, status }
      : { ...history[index], status };

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

function statusBadge(status) {
  if (status === "Disetujui") {
    return `<span class="status-badge status-approved">Disetujui</span>`;
  }

  if (status === "Ditolak") {
    return `<span class="status-badge status-rejected">Ditolak</span>`;
  }

  return `<span class="status-badge status-waiting">Menunggu Verifikasi</span>`;
}

function updateStats(data) {
  document.getElementById("totalData").textContent = data.length;
  document.getElementById("waitingData").textContent = data.filter(item => item.status === "Menunggu Verifikasi").length;
  document.getElementById("approvedData").textContent = data.filter(item => item.status === "Disetujui").length;
  document.getElementById("rejectedData").textContent = data.filter(item => item.status === "Ditolak").length;
}

function renderTable() {
  const data = getRequests();

  updateStats(data);

  if (data.length === 0) {
    table.innerHTML = `
      <tr>
        <td colspan="7" class="empty-admin">
          Belum ada permintaan adopsi.
        </td>
      </tr>
    `;
    return;
  }

  table.innerHTML = data.map((item, index) => {
    const pending = item.status === "Menunggu Verifikasi";

    return `
      <tr>
        <td>${index + 1}</td>
        <td><strong>${item.nama || "-"}</strong></td>
        <td>${item.hewan || "-"}</td>
        <td>${item.jenis || "-"}</td>
        <td>${formatDate(item.tanggal)}</td>
        <td>${statusBadge(item.status)}</td>
        <td>
          <div class="action-group">
            <a href="admin-adoption-detail.html?id=${item.id}" class="action-btn btn-detail">Detail</a>
            <button class="action-btn btn-edit" onclick="editData(${item.id})">Edit</button>
            <button class="action-btn btn-delete" onclick="hapusData(${item.id})">Hapus</button>
            ${pending ? `
              <button class="action-btn btn-approve" onclick="ubahStatus(${item.id}, 'Disetujui')">Acc</button>
              <button class="action-btn btn-reject" onclick="ubahStatus(${item.id}, 'Ditolak')">Tolak</button>
            ` : ""}
          </div>
        </td>
      </tr>
    `;
  }).join("");
}

function ubahStatus(id, status) {
  const data = getRequests();
  const index = data.findIndex(item => item.id === id);

  if (index === -1) return;

  data[index].status = status;
  saveRequests(data);
  syncHistory(id, status);

  alert(`Pengajuan ${data[index].hewan} berhasil ${status === "Disetujui" ? "disetujui" : "ditolak"}.`);
  renderTable();
}

function hapusData(id) {
  const data = getRequests();
  const item = data.find(item => item.id === id);

  if (!item) return;

  if (!confirm(`Hapus pengajuan adopsi ${item.hewan}?`)) return;

  const newData = data.filter(item => item.id !== id);
  saveRequests(newData);

  const history = JSON.parse(localStorage.getItem("adoptionHistory") || "[]");
  localStorage.setItem(
    "adoptionHistory",
    JSON.stringify(history.filter(item => item.id !== id))
  );

  renderTable();
}

function editData(id) {
  const data = getRequests();
  const item = data.find(item => item.id === id);

  if (!item) return;

  const nama = prompt("Nama lengkap:", item.nama);
  if (nama === null) return;

  const email = prompt("Email:", item.email);
  if (email === null) return;

  const telepon = prompt("Nomor telepon:", item.telepon);
  if (telepon === null) return;

  const alamat = prompt("Alamat:", item.alamat);
  if (alamat === null) return;

  const alasan = prompt("Alasan adopsi:", item.alasan);
  if (alasan === null) return;

  Object.assign(item, {
    nama: nama.trim(),
    email: email.trim(),
    telepon: telepon.trim(),
    alamat: alamat.trim(),
    alasan: alasan.trim()
  });

  saveRequests(data);

  syncHistory(id, item.status, {
    nama: item.nama,
    email: item.email,
    telepon: item.telepon,
    alamat: item.alamat,
    alasan: item.alasan
  });

  renderTable();
}

renderTable();