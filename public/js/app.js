/**
 * PAWPAW PET CARE — CONSOLIDATED CUSTOMER & AUTH SCRIPT (app.js)
 * Menggabungkan interaksi UI & Aksesibilitas (WCAG 2.2 AA):
 * 1. Sidebar Toggle (Desktop/Mobile) + aria-expanded + localStorage
 * 2. Marquee Pause/Play Toggle + localStorage
 * 3. Filter & Pencarian Katalog Produk / Hewan Adopsi + aria-pressed
 * 4. Modal Pengajuan Adopsi (Focus Management + Escape Key + aria-hidden)
 * 5. Kalkulasi Ongkir Checkout
 * 6. Animasi Kartu Flip 3D (inert/aria-hidden management) & Mata Maskot (respects prefers-reduced-motion)
 * 7. Sinkronisasi Slot Jam Praktik Layanan Kesehatan
 */
document.addEventListener("DOMContentLoaded", () => {
  "use strict";

  const prefersReducedMotion = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  // 1. Sidebar Toggle (dengan penyimpanan status di localStorage & aria-expanded)
  const app = document.getElementById("app");
  const sidebarToggle = document.getElementById("sidebarToggle");
  if (app && sidebarToggle) {
    const isCollapsed = localStorage.getItem("pawpaw_sidebar_collapsed") === "1";
    if (isCollapsed) {
      app.classList.add("sidebar-collapsed");
    }
    sidebarToggle.setAttribute("aria-expanded", isCollapsed ? "false" : "true");

    sidebarToggle.addEventListener("click", () => {
      const collapsed = app.classList.toggle("sidebar-collapsed");
      localStorage.setItem("pawpaw_sidebar_collapsed", collapsed ? "1" : "0");
      sidebarToggle.setAttribute("aria-expanded", collapsed ? "false" : "true");
    });
  }

  // 2. Pencarian & Filter Kategori Produk Toko (dengan aria-pressed)
  const productSearch = document.getElementById("product-search");
  const productCards = Array.from(document.querySelectorAll(".product-card[data-category]"));
  const filterBtns = Array.from(document.querySelectorAll("[data-filter-category]"));
  const emptySearchMsg = document.getElementById("search-empty");

  if (productCards.length > 0) {
    let activeCategory = "semua";

    const applyProductFilter = () => {
      const query = (productSearch?.value || "").trim().toLowerCase();
      let visibleCount = 0;

      productCards.forEach((card) => {
        const cat = (card.dataset.category || "").toLowerCase();
        const text = card.textContent.toLowerCase();
        const matchCat =
          activeCategory === "semua" ||
          cat === activeCategory ||
          (activeCategory === "perawatan" &&
            ["perawatan", "kebersihan", "suplemen", "aksesori", "perlengkapan"].includes(cat));
        const matchQuery = !query || text.includes(query);
        const show = matchCat && matchQuery;
        card.style.display = show ? "" : "none";
        if (show) visibleCount++;
      });

      if (emptySearchMsg) {
        emptySearchMsg.hidden = visibleCount > 0;
      }
    };

    filterBtns.forEach((btn) => {
      btn.addEventListener("click", () => {
        filterBtns.forEach((b) => {
          b.classList.remove("active");
          b.setAttribute("aria-pressed", "false");
        });
        btn.classList.add("active");
        btn.setAttribute("aria-pressed", "true");
        activeCategory = (btn.dataset.filterCategory || "semua").toLowerCase();
        applyProductFilter();
      });
    });

    if (productSearch) {
      productSearch.addEventListener("input", applyProductFilter);
    }
  }

  // 4. Filter Spesies & Modal Pengajuan Adopsi (Accessible Dialog + Escape + Focus Return)
  const petCards = Array.from(document.querySelectorAll(".pet-card[data-species]"));
  const speciesBtns = Array.from(document.querySelectorAll(".category-btn[data-category]"));
  const adoptionModal = document.getElementById("adoptionModal");
  const closeModalBtn = document.getElementById("closeModal");
  let lastFocusedAdoptTrigger = null;

  if (speciesBtns.length > 0 && petCards.length > 0) {
    speciesBtns.forEach((btn) => {
      btn.addEventListener("click", () => {
        speciesBtns.forEach((b) => {
          b.classList.remove("active");
          b.setAttribute("aria-pressed", "false");
        });
        btn.classList.add("active");
        btn.setAttribute("aria-pressed", "true");
        const category = (btn.dataset.category || "semua").toLowerCase();
        petCards.forEach((card) => {
          const sp = (card.dataset.species || "").toLowerCase();
          card.style.display = category === "semua" || sp === category ? "" : "none";
        });
      });
    });
  }

  const closeAdoptionModal = () => {
    if (!adoptionModal) return;
    adoptionModal.classList.remove("show");
    adoptionModal.setAttribute("aria-hidden", "true");
    document.body.style.overflow = "";
    if (lastFocusedAdoptTrigger && typeof lastFocusedAdoptTrigger.focus === "function") {
      lastFocusedAdoptTrigger.focus();
    }
  };

  document.querySelectorAll("[data-open-adopt]").forEach((btn) => {
    btn.addEventListener("click", () => {
      if (!adoptionModal) return;
      lastFocusedAdoptTrigger = btn;
      const animalIdInput = document.getElementById("modalAnimalId");
      const selectedPetSpan = document.getElementById("selectedPet");
      if (animalIdInput) animalIdInput.value = btn.dataset.animalId || "";
      if (selectedPetSpan) selectedPetSpan.textContent = btn.dataset.animalName || "";
      adoptionModal.classList.add("show");
      adoptionModal.setAttribute("aria-hidden", "false");
      document.body.style.overflow = "hidden";
      const firstInput = adoptionModal.querySelector("input:not([type='hidden']), textarea");
      if (firstInput) {
        setTimeout(() => firstInput.focus(), 50);
      }
    });
  });

  if (closeModalBtn && adoptionModal) {
    closeModalBtn.addEventListener("click", closeAdoptionModal);
    adoptionModal.addEventListener("click", (e) => {
      if (e.target === adoptionModal) {
        closeAdoptionModal();
      }
    });
    document.addEventListener("keydown", (e) => {
      if (e.key === "Escape" && adoptionModal.classList.contains("show")) {
        closeAdoptionModal();
      }
    });
  }

  // 5. Kalkulasi Ongkir Halaman Checkout
  const deliverySelect = document.getElementById("delivery-method");
  const addressWrapper = document.getElementById("address-field-wrapper");
  const addressInput = document.getElementById("delivery-address");
  const shippingEl = document.getElementById("checkout-shipping");
  const totalEl = document.getElementById("checkout-total");

  if (deliverySelect && totalEl) {
    const subtotal = Number(totalEl.dataset.subtotal || 0);
    const rupiah = (n) =>
      "Rp " + new Intl.NumberFormat("id-ID", { maximumFractionDigits: 0 }).format(n);

    const updateCheckoutSummary = () => {
      const isDelivery = deliverySelect.value === "delivery";
      const fee = isDelivery ? 15000 : 0;
      if (addressWrapper) addressWrapper.hidden = !isDelivery;
      if (addressInput) addressInput.required = isDelivery;
      if (shippingEl) shippingEl.textContent = rupiah(fee);
      totalEl.textContent = rupiah(subtotal + fee);
    };

    deliverySelect.addEventListener("change", updateCheckoutSummary);
    updateCheckoutSummary();
  }

  // 6. Halaman Login & Register (Flip Card + Inert/Focus Management + Mascot Eyes)
  const authFlip = document.getElementById("authFlip");
  const authCardFront = document.getElementById("authCardFront");
  const authCardBack = document.getElementById("authCardBack");
  const switchButtons = document.querySelectorAll(".auth-switch-button");

  const syncAuthCardsAccessibility = (isSignup) => {
    if (authCardFront) {
      authCardFront.toggleAttribute("inert", isSignup);
      authCardFront.setAttribute("aria-hidden", isSignup ? "true" : "false");
    }
    if (authCardBack) {
      authCardBack.toggleAttribute("inert", !isSignup);
      authCardBack.setAttribute("aria-hidden", !isSignup ? "true" : "false");
    }
  };

  switchButtons.forEach((button) => {
    button.addEventListener("click", () => {
      if (!authFlip) return;
      const isSignup = button.dataset.target === "signup";
      authFlip.classList.toggle("flipped", isSignup);
      syncAuthCardsAccessibility(isSignup);
      const targetCard = isSignup ? authCardBack : authCardFront;
      const firstField = targetCard?.querySelector("input:not([type='hidden'])");
      if (firstField) {
        setTimeout(() => firstField.focus(), 120);
      }
    });
  });

  const mascot = document.getElementById("mascot");
  const pupilLeft = document.getElementById("pupilLeft");
  const pupilRight = document.getElementById("pupilRight");
  if (mascot && pupilLeft && pupilRight && !prefersReducedMotion) {
    document.addEventListener("mousemove", (event) => {
      const rect = mascot.getBoundingClientRect();
      const cx = rect.left + rect.width / 2;
      const cy = rect.top + rect.height / 2;
      const angle = Math.atan2(event.clientY - cy, event.clientX - cx);
      const ox = Math.cos(angle) * 5;
      const oy = Math.sin(angle) * 5;
      const transform = `translate(${ox}px, ${oy}px)`;
      pupilLeft.style.transform = transform;
      pupilRight.style.transform = transform;
    });
  }

  // 7. Sinkronisasi Slot Jam Praktik saat memilih Layanan Kesehatan
  const healthServiceSelect = document.getElementById("healthServiceSelect");
  const healthTimeSlotSelect = document.getElementById("healthTimeSlotSelect");
  if (healthServiceSelect && healthTimeSlotSelect) {
    const syncTimeSlots = () => {
      const selectedOption = healthServiceSelect.options[healthServiceSelect.selectedIndex];
      const rawSlots = selectedOption?.getAttribute("data-slots") || "09:00,13:00,16:00";
      const slots = rawSlots
        .split(",")
        .map((s) => s.trim())
        .filter(Boolean);

      healthTimeSlotSelect.innerHTML = "";
      slots.forEach((slot) => {
        const opt = document.createElement("option");
        opt.value = slot;
        opt.textContent = `🕒 Sesi ${slot} WIB`;
        healthTimeSlotSelect.appendChild(opt);
      });
    };

    healthServiceSelect.addEventListener("change", syncTimeSlots);
  }
});
