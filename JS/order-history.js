(() => {
  "use strict";

  const ORDER_KEY = "pawpaw-orders-v1";
  const list = document.getElementById("order-history-list");
  const info = document.getElementById("history-info");
  const currentUser = document.getElementById("history-current-user");
  const account = window.PawPawAccount;

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

  function getActiveUsername() {
    return account?.getUsername?.() || "";
  }

  function normalizeUsername(value) {
    return String(value || "").trim().toLocaleLowerCase("id-ID");
  }

  function formatDate(value) {
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

  function deliveryLabel(value) {
    return value === "delivery" ? "Pengantaran" : "Ambil di toko";
  }

  function statusInfo(status) {
    if (status === "Selesai") return { label: "Selesai", className: "done" };
    if (status === "Diproses") return { label: "Diproses", className: "processing" };
    return { label: "Menunggu", className: "waiting" };
  }

  function render() {
    if (!list) return;

    const username = getActiveUsername();
    list.replaceChildren();

    if (!username) {
      if (currentUser) currentUser.textContent = "Belum login";
      if (info) {
        info.textContent = "Silakan login untuk melihat riwayat pesanan akun Anda.";
        info.hidden = false;
      }

      list.innerHTML = `
        <div class="order-history-empty">
          <div class="display-6 mb-2">👤</div>
          <h3 class="h5">Login diperlukan</h3>
          <p class="mb-3">Riwayat pesanan tidak dapat ditampilkan tanpa akun customer yang aktif.</p>
          <a class="btn btn-pawpaw" href="../login.html">Login</a>
        </div>`;
      return;
    }

    if (currentUser) currentUser.textContent = `@${username}`;

    const normalized = normalizeUsername(username);
    const orders = readOrders().filter((order) =>
      normalizeUsername(order.username) === normalized
    );

    if (info) {
      info.textContent = `Menampilkan ${orders.length} pesanan milik akun @${username}. Pesanan akun lain tidak ditampilkan.`;
      info.hidden = false;
    }

    if (!orders.length) {
      list.innerHTML = `
        <div class="order-history-empty">
          <div class="display-6 mb-2">🧾</div>
          <h3 class="h5">Belum ada riwayat pesanan</h3>
          <p class="mb-3">Akun <strong>@${escapeHtml(username)}</strong> belum pernah melakukan checkout pada browser ini.</p>
          <a class="btn btn-pawpaw" href="shop.html">Mulai Belanja</a>
        </div>`;
      return;
    }

    orders.forEach((order) => {
      const status = statusInfo(order.status || "Menunggu");
      const items = Array.isArray(order.items) ? order.items : [];
      const itemsHtml = items.length
        ? items.map((item) => `
            <div class="order-history-item">
              <div>
                <strong>${escapeHtml(item.name)}</strong>
                <div class="small text-secondary">${Number(item.qty) || 0} × ${escapeHtml(rupiah(item.price))}</div>
              </div>
              <strong>${escapeHtml(rupiah(item.subtotal))}</strong>
            </div>`).join("")
        : '<p class="text-secondary mb-0">Detail item tidak tersedia.</p>';

      const card = document.createElement("article");
      card.className = "order-history-card";
      card.innerHTML = `
        <div class="order-history-head">
          <div>
            <div class="order-history-id">${escapeHtml(order.id)}</div>
            <div class="order-history-date">${escapeHtml(formatDate(order.createdAt))}</div>
          </div>
          <span class="order-status ${status.className}">${status.label}</span>
        </div>
        <div class="order-history-body">
          <div class="order-history-meta">
            <div><small>Username</small><strong>@${escapeHtml(username)}</strong></div>
            <div><small>Nama Pemesan</small><strong>${escapeHtml(order.customerName)}</strong></div>
            <div><small>Metode</small><strong>${escapeHtml(deliveryLabel(order.delivery))}</strong></div>
          </div>
          <div class="order-history-meta order-history-meta-single">
            <div><small>Alamat</small><strong>${escapeHtml(order.address || "-")}</strong></div>
          </div>
          <h3 class="h6 fw-bold mb-2">Item Pesanan</h3>
          <div>${itemsHtml}</div>
          <div class="order-history-total">
            <span>Total Pesanan</span>
            <strong>${escapeHtml(rupiah(order.total))}</strong>
          </div>
        </div>`;
      list.append(card);
    });
  }

  window.addEventListener("storage", (event) => {
    if (event.key === ORDER_KEY || event.key === account?.SESSION_KEY) render();
  });
  window.addEventListener("focus", render);

  render();
})();
