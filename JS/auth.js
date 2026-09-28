(() => {
  "use strict";

  const USERS_KEY = "pawpaw-users-v1";
  const account = window.PawPawAccount;
  if (!account) return;

  const $ = (selector) => document.querySelector(selector);
  const normalize = (value) => String(value || "").trim().toLocaleLowerCase("id-ID");

  function readUsers() {
    try {
      const data = JSON.parse(localStorage.getItem(USERS_KEY) || "[]");
      return Array.isArray(data) ? data : [];
    } catch {
      return [];
    }
  }

  function saveUsers(users) {
    localStorage.setItem(USERS_KEY, JSON.stringify(users));
  }

  function showMessage(message, type = "danger") {
    const box = $("#auth-message");
    if (!box) return;
    box.className = `alert alert-${type} py-2`;
    box.textContent = message;
    box.hidden = false;
  }

  const loginForm = $("#login-form");
  if (loginForm) {
    loginForm.addEventListener("submit", (event) => {
      event.preventDefault();
      if (!loginForm.reportValidity()) return;

      const username = $("#login-username").value.trim();
      const password = $("#login-password").value;
      const remember = Boolean($("#remember-account")?.checked);
      const user = readUsers().find((item) =>
        normalize(item.username) === normalize(username) && item.password === password
      );

      if (!user) {
        showMessage("Username atau password tidak sesuai.");
        return;
      }

      account.setUser({ username: user.username, name: user.name || "" }, remember);
      window.location.href = "index.html";
    });
  }

  const registerForm = $("#register-form");
  if (registerForm) {
    registerForm.addEventListener("submit", (event) => {
      event.preventDefault();
      if (!registerForm.reportValidity()) return;

      const name = $("#register-name").value.trim();
      const username = $("#register-username").value.trim();
      const password = $("#register-password").value;
      const confirm = $("#register-confirm").value;

      if (password !== confirm) {
        showMessage("Konfirmasi password tidak sama.");
        return;
      }

      const users = readUsers();
      if (users.some((item) => normalize(item.username) === normalize(username))) {
        showMessage("Username sudah digunakan. Pilih username lain.");
        return;
      }

      users.push({ username, name, password });
      saveUsers(users);
      account.setUser({ username, name }, false);
      window.location.href = "index.html";
    });
  }
})();
