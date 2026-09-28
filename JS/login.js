document.addEventListener("DOMContentLoaded", () => {
      
  const DASHBOARD_URL = "index.html";

  const authFlip =
    document.getElementById("authFlip");

  const switchButtons =
    document.querySelectorAll(".auth-switch-button");


  switchButtons.forEach((button) => {

    button.addEventListener("click", () => {

      const target =
        button.dataset.target;

      authFlip.classList.toggle(
        "flipped",
        target === "signup"
      );

    });

  });


  // ---------- Mascot eyes follow cursor ----------

  const mascot =
    document.getElementById("mascot");

  const pupilLeft =
    document.getElementById("pupilLeft");

  const pupilRight =
    document.getElementById("pupilRight");

  const maxPupilOffset = 5;


  document.addEventListener("mousemove", (event) => {

    const mascotRect =
      mascot.getBoundingClientRect();

    const mascotCenterX =
      mascotRect.left + mascotRect.width / 2;

    const mascotCenterY =
      mascotRect.top + mascotRect.height / 2;

    const angle =
      Math.atan2(
        event.clientY - mascotCenterY,
        event.clientX - mascotCenterX
      );

    const offsetX =
      Math.cos(angle) * maxPupilOffset;

    const offsetY =
      Math.sin(angle) * maxPupilOffset;

    const pupilTransform =
      `translate(${offsetX}px, ${offsetY}px)`;

    pupilLeft.style.transform = pupilTransform;
    pupilRight.style.transform = pupilTransform;

  });


  // ---------- Paw print confetti ----------

  const confettiLayer =
    document.getElementById("confettiLayer");


  function launchPawConfetti() {

    const pawCount = 18;

    for (let i = 0; i < pawCount; i++) {

      const paw =
        document.createElement("span");

      paw.className = "confetti-paw";
      paw.textContent = "🐾";

      const startLeft =
        Math.random() * 100;

      const duration =
        1.6 + Math.random() * 1.2;

      const delay =
        Math.random() * 0.3;

      paw.style.left = `${startLeft}vw`;
      paw.style.animationDuration = `${duration}s`;
      paw.style.animationDelay = `${delay}s`;

      confettiLayer.appendChild(paw);

      setTimeout(() => {

        paw.remove();

      }, (duration + delay) * 1000);

    }

  }


  // ---------- Shared validation helper ----------

  function validateForm(form, messageElement) {

    let isValid = true;

    const inputs =
      form.querySelectorAll("input[required]");

    inputs.forEach((input) => {

      const formGroup =
        input.closest(".form-group");

      if (!input.value.trim()) {

        isValid = false;

        formGroup.classList.add("shake");

        setTimeout(() => {

          formGroup.classList.remove("shake");

        }, 400);

      }

    });

    if (!isValid) {

      messageElement.textContent =
        "Lengkapi semua data terlebih dahulu ya.";

      messageElement.className =
        "auth-message error";

    }

    return isValid;

  }


  // ---------- Login form ----------

    const loginForm =
    document.getElementById("loginForm");

  const loginMessage =
    document.getElementById("loginMessage");

  const loginSuccessModalElement =
    document.getElementById("loginSuccessModal");

  const loginSuccessModal =
    new bootstrap.Modal(loginSuccessModalElement);

  const loginSuccessOkButton =
    document.getElementById("loginSuccessOkButton");


  loginForm.addEventListener("submit", (event) => {

    event.preventDefault();

    if (!validateForm(loginForm, loginMessage)) {

      return;

    }

    loginMessage.textContent = "";
    loginMessage.className = "auth-message";

    launchPawConfetti();

    loginForm.reset();

    loginSuccessModal.show();

  });


  loginSuccessOkButton.addEventListener("click", () => {

    loginSuccessModal.hide();

    window.location.href = DASHBOARD_URL;

  });


  // ---------- Signup form ----------

  const signupForm =
    document.getElementById("signupForm");

  const signupMessage =
    document.getElementById("signupMessage");


  signupForm.addEventListener("submit", (event) => {

    event.preventDefault();

    if (!validateForm(signupForm, signupMessage)) {

      return;

    }

    const password =
      document.getElementById("signupPassword").value;

    const confirmPassword =
      document.getElementById("signupConfirm").value;

    if (password !== confirmPassword) {

      signupMessage.textContent =
        "Kata sandi dan konfirmasi tidak sama.";

      signupMessage.className =
        "auth-message error";

      const confirmGroup =
        document.getElementById("signupConfirm").closest(".form-group");

      confirmGroup.classList.add("shake");

      setTimeout(() => {

        confirmGroup.classList.remove("shake");

      }, 400);

      return;

    }

    signupMessage.textContent =
      "Akun berhasil dibuat! Yuk mulai adopsi 🐰";

    signupMessage.className =
      "auth-message success";

    launchPawConfetti();

    signupForm.reset();

  });

});