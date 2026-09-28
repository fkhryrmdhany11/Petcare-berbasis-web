document.addEventListener("DOMContentLoaded", () => {

  // ============================================================
  // DATA DUMMY - ganti dengan data asli dari backend nanti
  // ============================================================

  const dashboardData = {

    ordersToday: 8,

    revenueThisMonth: 12450000,

    actions: [
      {
        id: 1,
        type: "adopsi",
        text: "Budi mengajukan adopsi Siti",
        meta: "Kucing Anggora • 10 menit lalu",
        buttons: ["Setujui", "Tolak"]
      },
      {
        id: 2,
        type: "adopsi",
        text: "Rani mengajukan adopsi Noe",
        meta: "Kelinci Rex • 1 jam lalu",
        buttons: ["Setujui", "Tolak"]
      },
      {
        id: 3,
        type: "adopsi",
        text: "Dimas mengajukan adopsi Jeno",
        meta: "African Pygmy • 2 jam lalu",
        buttons: ["Setujui", "Tolak"]
      },
      {
        id: 4,
        type: "pesanan",
        text: "Pesanan #102 belum diproses",
        meta: "Royal Canin 2kg + Mainan bola • Rp 285.000",
        buttons: ["Proses"]
      },
      {
        id: 5,
        type: "pesanan",
        text: "Pesanan #103 belum diproses",
        meta: "Shampo hewan • Rp 65.000",
        buttons: ["Proses"]
      },
      {
        id: 6,
        type: "booking",
        text: "Booking vaksin Mochi belum dikonfirmasi",
        meta: "Besok, 09.00 • Dokter Ayu",
        buttons: ["Konfirmasi"]
      }
    ],

    activities: [
      { text: "Pesanan #101 sudah dibayar", time: "5 menit lalu" },
      { text: "Booking grooming untuk Kimi masuk", time: "25 menit lalu" },
      { text: "Budi mengajukan adopsi Siti", time: "1 jam lalu" },
      { text: "Pesanan #100 selesai dikirim", time: "2 jam lalu" },
      { text: "Rani mengajukan adopsi Noe", time: "3 jam lalu" }
    ],

    bookings: [
      { time: "09.00", title: "Vaksin - Mochi", detail: "Dokter Ayu" },
      { time: "10.30", title: "Grooming - Kimi", detail: "Groomer Sari" },
      { time: "13.00", title: "Cek kesehatan - Luna", detail: "Dokter Bima" },
      { time: "15.30", title: "Vaksin - Oreo", detail: "Dokter Ayu" }
    ],

    shipments: [
      { id: "#098", customer: "Sinta", items: "Pasir kucing 5kg", deadline: "12.00" },
      { id: "#099", customer: "Andi", items: "Royal Canin 2kg", deadline: "14.00" },
      { id: "#101", customer: "Wulan", items: "Kandang kelinci", deadline: "16.00" }
    ]

  };


  // ============================================================
  // Helper
  // ============================================================

  const statGrid = document.getElementById("statGrid");
  const actionList = document.getElementById("actionList");
  const actionCount = document.getElementById("actionCount");
  const activityList = document.getElementById("activityList");
  const bookingList = document.getElementById("bookingList");
  const shipmentList = document.getElementById("shipmentList");


  function formatRupiah(number) {

    return "Rp " + number.toLocaleString("id-ID");

  }


  function countActionsByType(type) {

    return dashboardData.actions.filter(
      (action) => action.type === type
    ).length;

  }


  // ============================================================
  // Tanggal hari ini
  // ============================================================

  document.getElementById("todayLabel").textContent =
    new Date().toLocaleDateString("id-ID", {
      weekday: "long",
      day: "numeric",
      month: "long",
      year: "numeric"
    });


  // ============================================================
  // 1. Kartu statistik
  // ============================================================

  function renderStats() {

    const stats = [
      {
        icon: "🛍️",
        value: dashboardData.ordersToday,
        label: "Pesanan baru hari ini",
        href: "pages/admin_shop.html"
      },
      {
        icon: "🐾",
        value: countActionsByType("adopsi"),
        label: "Adopsi menunggu review",
        href: "pages/admin-adoption.html"
      },
      {
        icon: "📅",
        value: dashboardData.bookings.length,
        label: "Booking hari ini",
        href: "pages/admin.html"
      },
      {
        icon: "💰",
        value: formatRupiah(dashboardData.revenueThisMonth),
        label: "Pendapatan bulan ini",
        href: "pages/admin_shop.html"
      }
    ];

    statGrid.innerHTML = stats.map((stat) => `
      <a href="${stat.href}" class="stat-card">
        <div class="stat-icon">${stat.icon}</div>
        <div class="stat-value">${stat.value}</div>
        <div class="stat-label">${stat.label}</div>
      </a>
    `).join("");

  }


  // ============================================================
  // 2. Perlu tindakan
  // ============================================================

  function renderActions() {

    actionCount.textContent = dashboardData.actions.length;

    if (dashboardData.actions.length === 0) {

      actionList.innerHTML = `
        <li class="empty-state">
          Semua beres! Tidak ada yang perlu ditindak 🎉
        </li>
      `;

      return;

    }

    actionList.innerHTML = dashboardData.actions.map((action) => `
      <li class="action-item" data-id="${action.id}">

        <span class="type-badge ${action.type}">
          ${action.type}
        </span>

        <div class="action-text">
          ${action.text}
          <small>${action.meta}</small>
        </div>

        <div class="action-buttons">
          ${action.buttons.map((label, index) => `
            <button
              type="button"
              class="btn-action ${index > 0 ? "ghost" : ""}"
              data-id="${action.id}"
              data-label="${label}"
            >
              ${label}
            </button>
          `).join("")}
        </div>

      </li>
    `).join("");

  }


  actionList.addEventListener("click", (event) => {

    const button = event.target.closest(".btn-action");

    if (!button) {
      return;
    }

    const actionId = Number(button.dataset.id);
    const label = button.dataset.label;

    const action = dashboardData.actions.find(
      (item) => item.id === actionId
    );

    const item = button.closest(".action-item");

    item.classList.add("removing");

    setTimeout(() => {

      dashboardData.actions = dashboardData.actions.filter(
        (current) => current.id !== actionId
      );

      dashboardData.activities.unshift({
        text: `${label}: ${action.text}`,
        time: "Baru saja"
      });

      dashboardData.activities = dashboardData.activities.slice(0, 6);

      renderStats();
      renderActions();
      renderActivities();

    }, 250);

  });


  // ============================================================
  // 4. Aktivitas terbaru
  // ============================================================

  function renderActivities() {

    activityList.innerHTML = dashboardData.activities.map((activity) => `
      <li class="activity-item">
        <span class="activity-dot"></span>
        <div>
          ${activity.text}
          <small>${activity.time}</small>
        </div>
      </li>
    `).join("");

  }


  // ============================================================
  // 5a. Jadwal booking hari ini
  // ============================================================

  function renderBookings() {

    if (dashboardData.bookings.length === 0) {

      bookingList.innerHTML = `
        <li class="empty-state">Tidak ada booking hari ini.</li>
      `;

      return;

    }

    bookingList.innerHTML = dashboardData.bookings.map((booking) => `
      <li class="schedule-item">
        <span class="schedule-time">${booking.time}</span>
        <div class="schedule-info">
          ${booking.title}
          <small>${booking.detail}</small>
        </div>
      </li>
    `).join("");

  }


  // ============================================================
  // 5b. Pesanan yang harus dikirim hari ini
  // ============================================================

  function renderShipments() {

    if (dashboardData.shipments.length === 0) {

      shipmentList.innerHTML = `
        <li class="empty-state">Tidak ada pengiriman hari ini.</li>
      `;

      return;

    }

    shipmentList.innerHTML = dashboardData.shipments.map((shipment) => `
      <li class="schedule-item" data-id="${shipment.id}">

        <span class="schedule-time">${shipment.deadline}</span>

        <div class="schedule-info">
          ${shipment.id} - ${shipment.customer}
          <small>${shipment.items}</small>
        </div>

        <label class="ship-check">
          <input type="checkbox">
          <span>Terkirim</span>
        </label>

      </li>
    `).join("");

  }


  shipmentList.addEventListener("change", (event) => {

    const checkbox = event.target.closest("input[type='checkbox']");

    if (!checkbox) {
      return;
    }

    checkbox
      .closest(".schedule-item")
      .classList.toggle("done", checkbox.checked);

  });


  // ============================================================
  // Render awal
  // ============================================================

  renderStats();
  renderActions();
  renderActivities();
  renderBookings();
  renderShipments();

});