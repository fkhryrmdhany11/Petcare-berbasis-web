(() => {
  "use strict";

  const ORDER_KEY = "pawpaw-orders-v1";

  const rupiah = (value) => new Intl.NumberFormat("id-ID", {
    style: "currency",
    currency: "IDR",
    maximumFractionDigits: 0,
  }).format(Number(value) || 0);

  function escapeHtml(value) {
    return String(value ?? "-")
      .replaceAll("&", "&amp;")
      .replaceAll("<", "&lt;")
      .replaceAll(">", "&gt;")
      .replaceAll('"', "&quot;")
      .replaceAll("'", "&#039;");
  }

  function readOrders() {
    try {
      const data = JSON.parse(localStorage.getItem(ORDER_KEY) || "[]");
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
      month: "short",
      year: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    }).replace(" pukul ", ", ");
  }

  function orderStatusBadge(status) {
    if (status === "Selesai") return '<span class="badge bg-success">Selesai</span>';
    if (status === "Diproses") return '<span class="badge bg-primary">Diproses</span>';
    return '<span class="badge bg-warning text-dark">Menunggu</span>';
  }

  function deliveryLabel(value) {
    return value === "delivery" ? "Pengantaran" : "Ambil di toko";
  }

  let orders = readOrders();
  const tableOrders = document.getElementById("tabelPesanan");
  const orderModalEl = document.getElementById("modalPesanan");
  const orderModal = orderModalEl ? new bootstrap.Modal(orderModalEl) : null;

  function updateSummary() {
    document.getElementById("totalPesanan").textContent = String(orders.length);
    document.getElementById("pesananMenunggu").textContent = String(
      orders.filter((order) => (order.status || "Menunggu") === "Menunggu").length
    );
    document.getElementById("pesananDiproses").textContent = String(
      orders.filter((order) => order.status === "Diproses").length
    );
    document.getElementById("pesananSelesai").textContent = String(
      orders.filter((order) => order.status === "Selesai").length
    );
  }

  function renderOrders() {
    tableOrders.replaceChildren();

    if (orders.length === 0) {
      const row = document.createElement("tr");
      row.innerHTML = '<td colspan="10" class="text-center text-muted py-5">Belum ada pesanan toko. Pesanan dari checkout akan muncul di sini.</td>';
      tableOrders.append(row);
      updateSummary();
      return;
    }

    orders.forEach((order, index) => {
      const items = Array.isArray(order.items) ? order.items : [];
      const totalQty = items.reduce((sum, item) => sum + (Number(item.qty) || 0), 0);
      const itemText = items.length
        ? `${escapeHtml(items[0].name)}${items.length > 1 ? ` +${items.length - 1} produk lain` : ""} (${totalQty} item)`
        : "-";
      const status = order.status || "Menunggu";
      const actionText = status === "Menunggu" ? "Proses" : status === "Diproses" ? "Selesaikan" : "Selesai";
      const actionClass = status === "Menunggu" ? "btn-primary" : status === "Diproses" ? "btn-success" : "btn-secondary";
      const disabled = status === "Selesai" ? "disabled" : "";

      const row = document.createElement("tr");
      row.innerHTML = `
        <td class="fw-semibold">${index + 1}</td>
        <td><span class="order-id">${escapeHtml(order.id)}</span></td>
        <td class="fw-semibold">${escapeHtml(order.username)}</td>
        <td>${escapeHtml(order.customerName)}</td>
        <td>${escapeHtml(formatTanggal(order.createdAt))}</td>
        <td><div class="order-items-summary">${itemText}</div></td>
        <td class="fw-bold">${escapeHtml(rupiah(order.total))}</td>
        <td>${escapeHtml(deliveryLabel(order.delivery))}</td>
        <td>${orderStatusBadge(status)}</td>
        <td class="text-center text-nowrap">
          <button class="btn btn-sm btn-outline-info me-1" type="button" data-order-detail="${index}">Detail</button>
          <button class="btn btn-sm ${actionClass}" type="button" data-order-process="${index}" ${disabled}>${actionText}</button>
        </td>`;
      tableOrders.append(row);
    });

    updateSummary();
  }

  function showOrderDetail(index) {
    const order = orders[index];
    const body = document.getElementById("modalPesananBody");
    if (!order || !body || !orderModal) return;

    const items = Array.isArray(order.items) ? order.items : [];
    const itemRows = items.length
      ? items.map((item) => `
          <div class="detail-order-item">
            <div><strong>${escapeHtml(item.name)}</strong><br><small class="text-muted">${Number(item.qty) || 0} × ${escapeHtml(rupiah(item.price))}</small></div>
            <strong>${escapeHtml(rupiah(item.subtotal))}</strong>
          </div>`).join("")
      : '<div class="p-3 text-muted">Tidak ada detail item.</div>';

    body.innerHTML = `
      <div class="detail-grid">
        <div class="detail-box"><small>ID Pesanan</small><strong>${escapeHtml(order.id)}</strong></div>
        <div class="detail-box"><small>Status</small>${orderStatusBadge(order.status || "Menunggu")}</div>
        <div class="detail-box"><small>Username</small><strong>${escapeHtml(order.username)}</strong></div>
        <div class="detail-box"><small>Nama Pemesan</small><strong>${escapeHtml(order.customerName)}</strong></div>
        <div class="detail-box"><small>No. Telepon</small><strong>${escapeHtml(order.phone)}</strong></div>
        <div class="detail-box"><small>Tanggal Pesanan</small><strong>${escapeHtml(formatTanggal(order.createdAt))}</strong></div>
        <div class="detail-box"><small>Metode</small><strong>${escapeHtml(deliveryLabel(order.delivery))}</strong></div>
        <div class="detail-box"><small>Alamat</small><strong>${escapeHtml(order.address || "-")}</strong></div>
      </div>
      <h6 class="fw-bold mb-2">Item Pesanan</h6>
      <div class="detail-order-items mb-3">${itemRows}</div>
      <div class="d-flex justify-content-between mb-1"><span>Subtotal</span><strong>${escapeHtml(rupiah(order.subtotal))}</strong></div>
      <div class="d-flex justify-content-between mb-2"><span>Pengantaran</span><strong>${escapeHtml(rupiah(order.shipping))}</strong></div>
      <div class="d-flex justify-content-between border-top pt-2 fs-5"><span>Total</span><strong>${escapeHtml(rupiah(order.total))}</strong></div>`;

    orderModal.show();
  }

  function processOrder(index) {
    if (!orders[index]) return;
    const current = orders[index].status || "Menunggu";

    if (current === "Menunggu") orders[index].status = "Diproses";
    else if (current === "Diproses") orders[index].status = "Selesai";
    else return;

    localStorage.setItem(ORDER_KEY, JSON.stringify(orders));
    renderOrders();
  }

  document.addEventListener("click", (event) => {
    const orderDetail = event.target.closest("[data-order-detail]");
    if (orderDetail) {
      showOrderDetail(Number(orderDetail.dataset.orderDetail));
      return;
    }

    const orderProcess = event.target.closest("[data-order-process]");
    if (orderProcess) processOrder(Number(orderProcess.dataset.orderProcess));
  });

  renderOrders();
})();
