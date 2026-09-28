(() => {
    "use strict";

    const CART_KEY = "pawpaw-cart-v1";
    const ORDER_KEY = "pawpaw-orders-v1";
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
    const account = window.PawPawAccount;

    function getCart() {
        try {
            const raw = JSON.parse(localStorage.getItem(CART_KEY) || "{}");
            const clean = {};
            if (raw && typeof raw === "object" && !Array.isArray(raw)) {
                for (const [id, qty] of Object.entries(raw)) {
                    if (Object.hasOwn(products, id) && Number.isInteger(qty) && qty > 0) {
                        clean[id] = Math.min(qty, 99);
                    }
                }
            }
            return clean;
        } catch {
            return {};
        }
    }

    function saveCart(cart) {
        try {
            localStorage.setItem(CART_KEY, JSON.stringify(cart));
        } catch {
            // Browser private mode dapat memblokir localStorage.
        }
        render();
    }

    function getOrders() {
        try {
            const data = JSON.parse(localStorage.getItem(ORDER_KEY) || "[]");
            return Array.isArray(data) ? data : [];
        } catch {
            return [];
        }
    }

    function saveOrder(order) {
        const orders = getOrders();
        orders.unshift(order);
        localStorage.setItem(ORDER_KEY, JSON.stringify(orders));
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
        if (pay) {
            const checkoutNeedsAccount = Boolean($("#checkout-form"));
            const hasAccount = Boolean(account?.getUsername?.());
            pay.disabled = subtotal === 0 || (checkoutNeedsAccount && !hasAccount);
        }

        const address = $("#delivery-address");
        if (address && delivery) {
            const needed = delivery.value === "delivery";
            address.required = needed;
            const field = address.closest(".address-field");
            if (field) field.hidden = !needed;
        }
    }

    function render() {
        const cart = getCart();
        updateCount(cart);
        renderItems("#cart-items", cart);
        renderItems("#checkout-items", cart);
        renderSummary(cart);
    }

    let toastTimer;
    function showCartToast(message) {
        const toast = $("#cart-toast");
        const messageEl = $("#cart-toast-message");
        if (!toast || !messageEl) return;

        window.clearTimeout(toastTimer);
        messageEl.textContent = message;
        toast.classList.remove("show");
        // Memaksa restart animasi ketika pengguna menambah item berulang kali.
        void toast.offsetWidth;
        toast.classList.add("show");
        toastTimer = window.setTimeout(() => toast.classList.remove("show"), 2200);
    }

    function setupProductSearch() {
        const search = $("#product-search");
        const clear = $("#clear-search");
        const empty = $("#search-empty");
        const cards = [...document.querySelectorAll(".product-card")];
        const radios = [...document.querySelectorAll('input[name="filter"]')];
        if (!search || cards.length === 0) return;

        const apply = () => {
            const query = search.value.trim().toLocaleLowerCase("id-ID");
            const selected = radios.find((radio) => radio.checked);
            const category = selected ? selected.id.replace("filter-", "") : "semua";
            let visible = 0;

            cards.forEach((card) => {
                const categoryMatch = category === "semua" || card.classList.contains(category);
                const text = card.textContent.toLocaleLowerCase("id-ID");
                const searchMatch = !query || text.includes(query);
                const show = categoryMatch && searchMatch;
                card.hidden = !show;
                if (show) visible += 1;
            });

            if (clear) clear.hidden = search.value.length === 0;
            if (empty) empty.hidden = visible !== 0;
        };

        search.addEventListener("input", apply);
        radios.forEach((radio) => radio.addEventListener("change", apply));
        if (clear) {
            clear.addEventListener("click", () => {
                search.value = "";
                search.focus();
                apply();
            });
        }
        apply();
    }

    document.addEventListener("click", (event) => {
        const add = event.target.closest("[data-add-cart]");
        if (add) {
            const id = add.dataset.addCart;
            if (!Object.hasOwn(products, id)) return;
            const cart = getCart();
            cart[id] = Math.min((cart[id] || 0) + 1, 99);
            saveCart(cart);
            showCartToast(products[id].name + " telah dimasukkan ke keranjang");

            const old = add.innerHTML;
            add.textContent = "✓";
            window.setTimeout(() => {
                add.innerHTML = old;
            }, 900);
            return;
        }

        const action = event.target.closest("[data-cart-action]");
        if (!action) return;
        const id = action.dataset.id;
        const cart = getCart();
        if (!Object.hasOwn(products, id) || !cart[id]) return;

        if (action.dataset.cartAction === "increase") {
            cart[id] = Math.min(99, cart[id] + 1);
        }
        if (action.dataset.cartAction === "decrease") {
            cart[id] -= 1;
        }
        if (action.dataset.cartAction === "remove" || cart[id] < 1) {
            delete cart[id];
        }
        saveCart(cart);
    });

    const delivery = $("#delivery-method");
    if (delivery) delivery.addEventListener("change", render);

    const form = $("#checkout-form");
    if (form) {
        const usernameField = $("#customer-username");
        const accountWarning = $("#checkout-account-warning");
        const activeUsername = account?.getUsername?.() || "";

        if (usernameField) {
            usernameField.value = activeUsername;
            usernameField.placeholder = activeUsername ? "" : "Belum login";
        }
        if (accountWarning) accountWarning.hidden = Boolean(activeUsername);

        const placeOrderButton = $("#place-order");
        if (placeOrderButton && !activeUsername) placeOrderButton.disabled = true;

        form.addEventListener("submit", (event) => {
            event.preventDefault();
            if (!form.reportValidity()) return;

            const cart = getCart();
            const entries = Object.entries(cart);
            if (!entries.length) {
                render();
                return;
            }

            const username = account?.getUsername?.() || "";
            const name = $("#customer-name").value.trim();
            const phone = $("#customer-phone").value.trim();
            const deliveryMethod = delivery ? delivery.value : "pickup";
            const address = $("#delivery-address")?.value.trim() || "";

            if (!username) {
                if (accountWarning) accountWarning.hidden = false;
                alert("Silakan login terlebih dahulu. Username checkout harus mengikuti akun yang sedang aktif.");
                window.location.href = "../login.html";
                return;
            }
            if (!name) {
                $("#customer-name").focus();
                return;
            }

            const subtotal = cartTotal(cart);
            const shipping = deliveryMethod === "delivery" ? 15000 : 0;
            const total = subtotal + shipping;
            const now = new Date();
            const orderId =
                "ORD-" +
                now.getFullYear().toString().slice(-2) +
                String(now.getMonth() + 1).padStart(2, "0") +
                String(now.getDate()).padStart(2, "0") +
                "-" +
                String(Date.now()).slice(-6);

            const order = {
                id: orderId,
                username,
                customerName: name,
                phone,
                createdAt: now.toISOString(),
                delivery: deliveryMethod,
                address: deliveryMethod === "delivery" ? address : "-",
                items: entries.map(([id, qty]) => ({
                    id,
                    name: products[id].name,
                    price: products[id].price,
                    qty,
                    subtotal: products[id].price * qty,
                })),
                subtotal,
                shipping,
                total,
                status: "Menunggu",
            };

            try {
                saveOrder(order);
            } catch {
                alert("Pesanan tidak dapat disimpan di browser ini. Coba aktifkan localStorage lalu ulangi checkout.");
                return;
            }

            saveCart({});
            form.reset();

            const modal = $("#checkout-success-modal");
            const message = $("#checkout-success-message");
            if (message) {
                message.textContent =
                    "Pesanan " + orderId + " atas username " + username + " berhasil disimpan dengan total " + rupiah(total) + ".";
            }
            if (modal) {
                modal.classList.remove("closing");
                modal.hidden = false;

                // Tutup notifikasi secara otomatis, lalu kembali ke halaman Shop.
                window.setTimeout(() => modal.classList.add("closing"), 1700);
                window.setTimeout(() => {
                    modal.hidden = true;
                    modal.classList.remove("closing");
                }, 2050);
            }

            window.setTimeout(() => {
                window.location.href = "shop.html";
            }, 2250);
        });
    }

    setupProductSearch();
    render();
})();
