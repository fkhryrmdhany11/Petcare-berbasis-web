/**
 * PAWPAW PET CARE — CONSOLIDATED ADMIN PANEL SCRIPT (admin.js)
 * Menggabungkan interaksi UI Admin:
 * 1. Toggle Collapse Sidebar Desktop (Klik Logo) & Drawer Mobile
 * 2. Tab Aksi Cepat (Quick Action) Dashboard
 * 3. Filter Pencarian Tabel Real-time
 * 4. Konfirmasi Hapus & Modal Penolakan Booking
 */
document.addEventListener("DOMContentLoaded", () => {
  "use strict";

  const sbLayout = document.getElementById("sbLayout");
  const adminSidebarToggle = document.getElementById("adminSidebarToggle");
  const sidebar = document.getElementById("sbSidebar");
  const overlay = document.getElementById("sbOverlay");
  const menuButton = document.getElementById("sbMenuButton");

  // 1a. Collapse / Expand Sidebar Desktop saat klik Logo Admin
  if (sbLayout && adminSidebarToggle) {
    if (localStorage.getItem("pawpaw_admin_sb_collapsed") === "1") {
      sbLayout.classList.add("sb-collapsed");
    }
    adminSidebarToggle.addEventListener("click", () => {
      const collapsed = sbLayout.classList.toggle("sb-collapsed");
      localStorage.setItem("pawpaw_admin_sb_collapsed", collapsed ? "1" : "0");
    });
  }

  // 1b. Drawer Sidebar Mobile/Tablet
  if (sidebar && overlay && menuButton) {
    const closeSidebar = () => {
      sidebar.classList.remove("open");
      overlay.classList.remove("show");
    };

    menuButton.addEventListener("click", () => {
      sidebar.classList.toggle("open");
      overlay.classList.toggle("show");
    });

    overlay.addEventListener("click", closeSidebar);
  }

  // 2. Quick Action Tabs pada Dashboard Admin
  const quickTabs = document.querySelectorAll("[data-quick-tab]");
  const quickPanels = document.querySelectorAll("[data-quick-panel]");
  if (quickTabs.length > 0) {
    quickTabs.forEach((btn) => {
      btn.addEventListener("click", () => {
        const targetId = btn.getAttribute("data-quick-tab");
        quickTabs.forEach((b) => {
          b.classList.remove("btn-primary", "active");
          b.classList.add("btn-outline-primary");
        });
        btn.classList.remove("btn-outline-primary");
        btn.classList.add("btn-primary", "active");

        quickPanels.forEach((panel) => {
          panel.classList.toggle("d-none", panel.id !== targetId);
        });
      });
    });
  }

  // 3. Filter Pencarian Tabel Admin Real-Time
  document.querySelectorAll("[data-admin-filter]").forEach((input) => {
    const tableSelector = input.getAttribute("data-admin-filter");
    const table = tableSelector ? document.querySelector(tableSelector) : null;
    if (!table) return;

    input.addEventListener("input", () => {
      const q = input.value.trim().toLowerCase();
      table.querySelectorAll("tbody tr").forEach((row) => {
        const text = row.textContent.toLowerCase();
        row.style.display = !q || text.includes(q) ? "" : "none";
      });
    });
  });

  // 4. Konfirmasi Hapus Data
  document.querySelectorAll("form[data-confirm]").forEach((form) => {
    form.addEventListener("submit", (e) => {
      const msg = form.getAttribute("data-confirm") || "Yakin ingin menghapus data ini?";
      if (!window.confirm(msg)) {
        e.preventDefault();
      }
    });
  });
});
