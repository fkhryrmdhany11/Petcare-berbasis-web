const historyList = document.getElementById("historyList");

function getHistory() {
  const requests = JSON.parse(localStorage.getItem("adoptionRequests") || "[]");
  const oldHistory = JSON.parse(localStorage.getItem("adoptionHistory") || "[]");

  if (requests.length > 0) {
    return requests;
  }

  return oldHistory;
}

function getStatus(status) {
  if (status === "Disetujui" || status === "Berhasil") {
    return {
      text: "Berhasil",
      className: "status-success"
    };
  }

  if (status === "Ditolak" || status === "Gagal") {
    return {
      text: "Gagal",
      className: "status-failed"
    };
  }

  return {
    text: "Menunggu Verifikasi",
    className: "status-waiting"
  };
}

function formatDate(date) {
  if (!date) return "-";

  return new Date(date).toLocaleDateString("id-ID", {
    day: "numeric",
    month: "long",
    year: "numeric"
  });
}

function renderHistory() {
  const history = getHistory();

  if (history.length === 0) {
    historyList.innerHTML = `
      <div class="empty-history">
        Belum ada riwayat pengajuan adopsi.
      </div>
    `;
    return;
  }

  historyList.innerHTML = history.slice().reverse().map(item => {
    const status = getStatus(item.status);

    return `
      <div class="history-card">
        <img src="${item.image}" alt="${item.hewan}" class="history-image">
        <div class="history-info">
          <h3>${item.hewan}</h3>
          <p>Ras: ${item.ras}</p>
          <p>Gender: ${item.gender}</p>
          <p>Umur: ${item.umur}</p>
          <p class="history-date">Tanggal Pengajuan: ${formatDate(item.tanggal)}</p>
        </div>
        <div class="history-status ${status.className}">
          ${status.text}
        </div>
      </div>
    `;
  }).join("");
}

renderHistory();