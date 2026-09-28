(() => {
  "use strict";

  const BOOKING_KEY = "pawData";

  function escapeHtml(value) {
    return String(value ?? "-")
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }

  function readBookings() {
    try {
      const data = JSON.parse(localStorage.getItem(BOOKING_KEY) || "[]");
      return Array.isArray(data) ? data : [];
    } catch {
      return [];
    }
  }

  function formatTanggal(value) {
    if (!value) return "-";
    const date = new Date(value);
    if (Number.isNaN(date.getTime())) return "-";
    return date.toLocaleString("id-ID", {
      day: "2-digit",
      month: "long",
      year: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    }).replace(" pukul ", ", ");
  }

  function bookingStatusBadge(status) {
    return status === "Disetujui"
      ? '<span class="badge bg-success px-3 py-2">Disetujui</span>'
      : '<span class="badge bg-warning text-dark px-3 py-2">Menunggu Konfirmasi</span>';
  }

  let bookings = readBookings();
  const tableBookings = document.getElementById("tabelAdmin");
  const bookingModalEl = document.getElementById("modalDetail");
  const bookingModal = bookingModalEl ? new bootstrap.Modal(bookingModalEl) : null;

  function renderBookings() {
    tableBookings.replaceChildren();

    if (bookings.length === 0) {
      const row = document.createElement("tr");
      row.innerHTML = '<td colspan="6" class="text-center text-muted py-5">Belum ada permintaan booking masuk dari pelanggan.</td>';
      tableBookings.append(row);
      return;
    }

    bookings.forEach((item, index) => {
      const approved = item.status === "Disetujui";
      const row = document.createElement("tr");
      row.innerHTML = `
        <td class="text-center fw-bold">${index + 1}</td>
        <td><span class="fw-bold text-dark">${escapeHtml(item.pn || "-")}</span><br><small class="text-muted">(${escapeHtml(item.pt || "Hewan")})</small></td>
        <td>${escapeHtml(item.st || "-")}</td>
        <td>${escapeHtml(formatTanggal(item.bd))}</td>
        <td class="text-center">${bookingStatusBadge(item.status)}</td>
        <td class="text-center text-nowrap">
          <button class="btn btn-sm btn-info text-white me-1" type="button" data-booking-detail="${index}">Detail</button>
          <button class="btn btn-sm ${approved ? "btn-secondary" : "btn-success"}" type="button" data-booking-process="${index}" ${approved ? "disabled" : ""}>${approved ? "Selesai" : "Proses"}</button>
        </td>`;
      tableBookings.append(row);
    });
  }

  function showBookingDetail(index) {
    const item = bookings[index];
    const body = document.getElementById("modalBody");
    if (!item || !body || !bookingModal) return;

    body.innerHTML = `
      <div class="mb-2"><strong>Nama Pemilik:</strong> ${escapeHtml(item.on || "-")}</div>
      <div class="mb-2"><strong>Nama Hewan:</strong> ${escapeHtml(item.pn || "-")}</div>
      <div class="mb-2"><strong>Jenis Hewan:</strong> ${escapeHtml(item.pt || "-")}</div>
      <div class="mb-2"><strong>Layanan Booking:</strong> ${escapeHtml(item.st || "-")}</div>
      <div class="mb-2"><strong>Jadwal:</strong> ${escapeHtml(formatTanggal(item.bd))}</div>
      <div class="mb-2"><strong>Status saat ini:</strong> ${bookingStatusBadge(item.status)}</div>`;

    bookingModal.show();
  }

  function processBooking(index) {
    if (!bookings[index]) return;
    bookings[index].status = "Disetujui";
    localStorage.setItem(BOOKING_KEY, JSON.stringify(bookings));
    renderBookings();
  }

  document.addEventListener("click", (event) => {
    const bookingDetail = event.target.closest("[data-booking-detail]");
    if (bookingDetail) {
      showBookingDetail(Number(bookingDetail.dataset.bookingDetail));
      return;
    }

    const bookingProcess = event.target.closest("[data-booking-process]");
    if (bookingProcess) processBooking(Number(bookingProcess.dataset.bookingProcess));
  });

  renderBookings();
})();
