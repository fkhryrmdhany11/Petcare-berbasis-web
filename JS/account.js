(() => {
  "use strict";

  const SESSION_KEY = "pawpaw-auth-user-v1";

  function normalizeUsername(value) {
    return String(value || "").trim();
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

    try {
      const raw = localStorage.getItem(SESSION_KEY) || sessionStorage.getItem(SESSION_KEY);
      if (raw) {
        const parsed = JSON.parse(raw);
        if (parsed && typeof parsed === "object") {
          return { ...parsed, username };
        }
      }
    } catch {
      // Jika session hanya berisi string, username tetap cukup untuk kebutuhan shop.
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
    if (!remember) {
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

  window.PawPawAccount = Object.freeze({
    SESSION_KEY,
    getUsername,
    getUser,
    setUser,
    clearUser,
  });
})();
