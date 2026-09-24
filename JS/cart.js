(() => {
    "use strict";
    const KEY = "pawpaw-cart-v1";
    const products = Object.freeze({
        whiskas: { name: "Whiskas Adult", price: 75000, image: "whiskas.png" },
        pedigree: { name: "Pedigree Adult", price: 100000, image: "pedigree.png" },
        "shampoo-anjing": {
            name: "Shampoo Anjing",
            price: 90000,
            image: "shampoo-anjing.png",
        },
        "shampoo-kucing": {
            name: "Shampoo Kucing",
            price: 85000,
            image: "shampoo-kucing.png",
        },
        sisir: {
            name: "Sisir Grooming",
            price: 60000,
            image: "sisir-grooming.png",
        },
        tulang: {
            name: "Mainan Tulang Karet",
            price: 35000,
            image: "mainan-tulang.png",
        },
    });
    const rupiah = (value) =>
        new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            maximumFractionDigits: 0,
        }).format(value);
    const $ = (selector) => document.querySelector(selector);

    function getCart() {
        try {
            const raw = JSON.parse(localStorage.getItem(KEY) || "{}");
            const clean = {};
            if (raw && typeof raw === "object" && !Array.isArray(raw)) {
                for (const [id, qty] of Object.entries(raw)) {
                    if (Object.hasOwn(products, id) && Number.isInteger(qty) && qty > 0)
                        clean[id] = Math.min(qty, 99);
                }
            }
            return clean;
        } catch {
            return {};
        }
    }
    function saveCart(cart) {
        try {
            localStorage.setItem(KEY, JSON.stringify(cart));
        } catch {
            /* private mode can block storage */
        }
        render();
    }
    function cartTotal(cart) {
        return Object.entries(cart).reduce(
            (total, [id, qty]) => total + products[id].price * qty,
            0,
        );
    }
    function updateCount(cart) {
        const count = Object.values(cart).reduce((sum, qty) => sum + qty, 0);
        document.querySelectorAll("[data-cart-count]").forEach((el) => {
            el.textContent = String(count);
        });
    }
    function itemNode(id, qty) {
        const product = products[id];
        const row = document.createElement("div");
        row.className = "cart-item d-flex align-items-center gap-3 flex-wrap";
        const img = document.createElement("img");
        img.src = "../assets/img/shop/" + product.image;
        img.alt = "";
        img.className = "cart-item-image";
        const info = document.createElement("div");
        info.className = "cart-item-info";
        const name = document.createElement("strong");
        name.textContent = product.name;
        const unit = document.createElement("div");
        unit.className = "text-secondary small";
        unit.textContent = rupiah(product.price) + " / item";
        info.append(name, unit);
        const controls = document.createElement("div");
        controls.className = "d-flex align-items-center gap-2";
        const minus = document.createElement("button");
        minus.type = "button";
        minus.className = "btn btn-outline-secondary btn-sm";
        minus.dataset.cartAction = "decrease";
        minus.dataset.id = id;
        minus.setAttribute("aria-label", "Kurangi " + product.name);
        minus.textContent = "−";
        const quantity = document.createElement("span");
        quantity.className = "cart-quantity";
        quantity.textContent = String(qty);
        quantity.setAttribute("aria-label", "Jumlah " + qty);
        const plus = document.createElement("button");
        plus.type = "button";
        plus.className = "btn btn-outline-secondary btn-sm";
        plus.dataset.cartAction = "increase";
        plus.dataset.id = id;
        plus.disabled = qty >= 99;
        plus.setAttribute("aria-label", "Tambah " + product.name);
        plus.textContent = "+";
        const remove = document.createElement("button");
        remove.type = "button";
        remove.className = "btn btn-link text-danger btn-sm";
        remove.dataset.cartAction = "remove";
        remove.dataset.id = id;
        remove.textContent = "Hapus";
        controls.append(minus, quantity, plus, remove);
        const subtotal = document.createElement("strong");
        subtotal.className = "cart-item-subtotal";
        subtotal.textContent = rupiah(product.price * qty);
        row.append(img, info, controls, subtotal);
        return row;
    }
    function renderItems(selector, cart) {
        const list = $(selector);
        if (!list) return;
        list.replaceChildren();
        const entries = Object.entries(cart);
        if (!entries.length) {
            const empty = document.createElement("div");
            empty.className = "cart-empty text-center py-5";
            empty.innerHTML =
                '<span class="display-5" aria-hidden="true">🛒</span><h3 class="h5 mt-3">Keranjang masih kosong</h3><p>Tambahkan produk dari katalog terlebih dahulu.</p><a class="btn btn-pawpaw" href="shop.html">Lihat produk</a>';
            list.append(empty);
            return;
        }
        entries.forEach(([id, qty]) => list.append(itemNode(id, qty)));
    }
    function renderSummary(cart) {
        const subtotal = cartTotal(cart);
        const delivery = $("#delivery-method");
        const fee =
            delivery && delivery.value === "delivery" && subtotal > 0 ? 15000 : 0;
        document.querySelectorAll("[data-subtotal]").forEach((el) => {
            el.textContent = rupiah(subtotal);
        });
        document.querySelectorAll("[data-shipping]").forEach((el) => {
            el.textContent = rupiah(fee);
        });
        document.querySelectorAll("[data-total]").forEach((el) => {
            el.textContent = rupiah(subtotal + fee);
        });
        const go = $("#go-checkout");
        if (go) go.classList.toggle("disabled", subtotal === 0);
        const pay = $("#place-order");
        if (pay) pay.disabled = subtotal === 0;
        const address = $("#delivery-address");
        if (address) {
            const needed = delivery.value === "delivery";
            address.required = needed;
            address.closest(".address-field").hidden = !needed;
        }
    }
    function render() {
        const cart = getCart();
        updateCount(cart);
        renderItems("#cart-items", cart);
        renderItems("#checkout-items", cart);
        renderSummary(cart);
    }
    document.addEventListener("click", (event) => {
        const add = event.target.closest("[data-add-cart]");
        if (add) {
            const id = add.dataset.addCart;
            if (!Object.hasOwn(products, id)) return;
            const cart = getCart();
            cart[id] = Math.min((cart[id] || 0) + 1, 99);
            saveCart(cart);
            const status = $("#cart-status");
            if (status)
                status.textContent = products[id].name + " ditambahkan ke keranjang.";
            const old = add.innerHTML;
            add.textContent = "✓";
            window.setTimeout(() => {
                add.innerHTML = old;
            }, 900);
            return;
        }
        const action = event.target.closest("[data-cart-action]");
        if (!action) return;
        const id = action.dataset.id,
            cart = getCart();
        if (!Object.hasOwn(products, id) || !cart[id]) return;
        if (action.dataset.cartAction === "increase")
            cart[id] = Math.min(99, cart[id] + 1);
        if (action.dataset.cartAction === "decrease") cart[id] -= 1;
        if (action.dataset.cartAction === "remove" || cart[id] < 1) delete cart[id];
        saveCart(cart);
    });
    const delivery = $("#delivery-method");
    if (delivery) delivery.addEventListener("change", render);
    const form = $("#checkout-form");
    if (form)
        form.addEventListener("submit", (event) => {
            event.preventDefault();
            if (!form.reportValidity()) return;
            const cart = getCart();
            if (!Object.keys(cart).length) {
                render();
                return;
            }
            const total =
                cartTotal(cart) + (delivery.value === "delivery" ? 15000 : 0);
            const name = $("#customer-name").value.trim();
            if (!name) {
                $("#customer-name").focus();
                return;
            }
            const success = $("#order-success");
            success.replaceChildren();
            const heading = document.createElement("h3");
            heading.className = "h5";
            heading.textContent = "Simulasi pesanan berhasil";
            const detail = document.createElement("p");
            detail.className = "mb-1";
            detail.textContent =
                "Terima kasih, " + name + ". Total simulasi: " + rupiah(total) + ".";
            const note = document.createElement("p");
            note.className = "mb-0 small";
            note.textContent =
                "Tidak ada pesanan atau pembayaran sungguhan yang dikirim.";
            success.append(heading, detail, note);
            success.hidden = false;
            form.reset();
            saveCart({});
            success.scrollIntoView({ behavior: "smooth", block: "center" });
        });
    render();
})();
