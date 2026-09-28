(() => {
  "use strict";

  const SESSION_KEY = "pawpaw-auth-user-v1";
  const PROFILE_KEY = "pawpaw-account-profiles-v1";

  function normalizeUsername(value) {
    return String(value || "").trim();
  }

  function normalizeEmail(value) {
    return String(value || "").trim().toLowerCase();
  }

  function usernameFromEmail(email) {
    const localPart = normalizeEmail(email).split("@")[0] || "";
    return localPart
      .replace(/[^a-z0-9._-]/gi, "")
      .slice(0, 40);
  }

  function extractUsername(value) {
    if (!value) return "";

    if (typeof value === "string") {
      const trimmed = value.trim();
      if (!trimmed) return "";

      try {
        const parsed = JSON.parse(trimmed);
        if (parsed && typeof parsed === "object") {
          return normalizeUsername(
            parsed.username ?? parsed.userName ?? parsed.user?.username ?? parsed.account?.username
          );
        }
      } catch {
        // Nilai biasa seperti "fakhry11" tetap dianggap username.
      }

      return trimmed;
    }

    if (typeof value === "object") {
      return normalizeUsername(
        value.username ?? value.userName ?? value.user?.username ?? value.account?.username
      );
    }

    return "";
  }

  function readStorage(storage, key) {
    try {
      return storage.getItem(key);
    } catch {
      return null;
    }
  }

  function getUsername() {
    const storages = [window.sessionStorage, window.localStorage];
    const keys = [
      SESSION_KEY,
      "pawpaw-current-user-v1",
      "pawpaw-current-user",
      "loggedInUser",
      "currentUser",
      "authUser",
      "user",
      "username",
    ];

    for (const storage of storages) {
      for (const key of keys) {
        const username = extractUsername(readStorage(storage, key));
        if (username) return username;
      }
    }

    return "";
  }

  function getUser() {
    const username = getUsername();
    if (!username) return null;

    for (const storage of [window.sessionStorage, window.localStorage]) {
      try {
        const raw = storage.getItem(SESSION_KEY);
        if (!raw) continue;
        const parsed = JSON.parse(raw);
        if (parsed && typeof parsed === "object") {
          return { ...parsed, username };
        }
      } catch {
        // Jika session hanya berisi string, username tetap cukup untuk kebutuhan shop.
      }
    }

    return { username };
  }

  function setUser(user, remember = false) {
    const username = extractUsername(user);
    if (!username) throw new Error("Username akun tidak valid.");

    const payload = typeof user === "object" && user !== null
      ? { ...user, username }
      : { username };

    const target = remember ? window.localStorage : window.sessionStorage;
    target.setItem(SESSION_KEY, JSON.stringify(payload));

    // Pastikan hanya satu lokasi session utama yang aktif agar akun tidak tertukar.
    if (remember) {
      try { sessionStorage.removeItem(SESSION_KEY); } catch {}
    } else {
      try { localStorage.removeItem(SESSION_KEY); } catch {}
    }

    return payload;
  }

  function clearUser() {
    const keys = [
      SESSION_KEY,
      "pawpaw-current-user-v1",
      "pawpaw-current-user",
      "loggedInUser",
      "currentUser",
      "authUser",
      "user",
      "username",
    ];
    for (const storage of [window.sessionStorage, window.localStorage]) {
      for (const key of keys) {
        try { storage.removeItem(key); } catch {}
      }
    }
  }

  function readProfiles() {
    try {
      const data = JSON.parse(localStorage.getItem(PROFILE_KEY) || "[]");
      return Array.isArray(data) ? data : [];
    } catch {
      return [];
    }
  }

  function saveProfile(profile) {
    if (!profile?.email || !profile?.username) return;

    try {
      const profiles = readProfiles();
      const email = normalizeEmail(profile.email);
      const index = profiles.findIndex((item) => normalizeEmail(item?.email) === email);
      const cleanProfile = {
        name: String(profile.name || "").trim(),
        email,
        username: normalizeUsername(profile.username),
      };

      if (index >= 0) profiles[index] = { ...profiles[index], ...cleanProfile };
      else profiles.push(cleanProfile);

      localStorage.setItem(PROFILE_KEY, JSON.stringify(profiles));
    } catch {
      // Penyimpanan profil opsional; session login tetap dapat berjalan tanpa ini.
    }
  }

  function profileForEmail(email) {
    const normalized = normalizeEmail(email);
    if (!normalized) return null;
    return readProfiles().find((item) => normalizeEmail(item?.email) === normalized) || null;
  }

  /*
   * Integrasi halaman login tanpa mengubah JS/login.js.
   * account.js cukup di-import oleh login.html. Listener capture membaca data
   * sebelum login.js mereset form, lalu menyimpan akun aktif untuk Shop/Checkout.
   */
  function connectLoginPage() {
    document.addEventListener("submit", (event) => {
      const form = event.target;
      if (!(form instanceof HTMLFormElement)) return;

      if (form.id === "loginForm") {
        const email = normalizeEmail(document.getElementById("loginEmail")?.value);
        const password = document.getElementById("loginPassword")?.value || "";
        if (!email || !password) return;

        const knownProfile = profileForEmail(email);
        const username = knownProfile?.username || usernameFromEmail(email);
        if (!username) return;

        const remember = Boolean(document.getElementById("rememberMe")?.checked);
        setUser({
          username,
          email,
          name: knownProfile?.name || "",
          role: email === "admin@pawpaw.com" ? "admin" : "customer",
        }, remember);
        return;
      }

      if (form.id === "signupForm") {
        const name = String(document.getElementById("signupName")?.value || "").trim();
        const email = normalizeEmail(document.getElementById("signupEmail")?.value);
        const password = document.getElementById("signupPassword")?.value || "";
        if (!name || !email || !password) return;

        // Karena halaman login kelompok belum memiliki field username, sementara
        // username Shop dibuat stabil dari bagian email sebelum tanda @.
        saveProfile({
          name,
          email,
          username: usernameFromEmail(email),
        });
      }
    }, true);
  }

  window.PawPawAccount = Object.freeze({
    SESSION_KEY,
    PROFILE_KEY,
    getUsername,
    getUser,
    setUser,
    clearUser,
  });

  connectLoginPage();
})();
