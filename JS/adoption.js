document.addEventListener("DOMContentLoaded", () => {

  const categoryButtons =
    document.querySelectorAll(".category-card");

  const petCards =
    document.querySelectorAll(".pet-card");


  categoryButtons.forEach((button) => {

    button.addEventListener("click", () => {

      const selectedCategory =
        button.dataset.filter;


      categoryButtons.forEach((categoryButton) => {

        categoryButton.classList.remove("active");

      });


      button.classList.add("active");


      petCards.forEach((petCard) => {

        const petCategory =
          petCard.dataset.category;


        const isVisible =
          selectedCategory === "all" ||
          petCategory === selectedCategory;


        petCard.classList.toggle(
          "hidden",
          !isVisible
        );

      });

    });

  });


  const adoptionButtons =
    document.querySelectorAll(
      ".adoption-button:not([disabled])"
    );


  const adoptionModalElement =
    document.getElementById(
      "adoptionModal"
    );


  const adoptionModal =
    new bootstrap.Modal(
      adoptionModalElement
    );


  const adoptionForm =
    document.getElementById(
      "adoptionForm"
    );


  const petNameInput =
    document.getElementById(
      "petName"
    );


  const adoptionMessage =
    document.getElementById(
      "adoptionMessage"
    );


  adoptionButtons.forEach((button) => {

    button.addEventListener("click", () => {

      const petName =
        button.dataset.pet;


      adoptionForm.reset();


      petNameInput.value =
        petName;


      adoptionMessage.innerHTML =
        "";


      adoptionModal.show();

    });

  });


  adoptionForm.addEventListener(
    "submit",
    (event) => {

      event.preventDefault();


      const fullName =
        document.getElementById(
          "fullName"
        ).value.trim();


      const email =
        document.getElementById(
          "email"
        ).value.trim();


      const phone =
        document.getElementById(
          "phone"
        ).value.trim();


      const address =
        document.getElementById(
          "address"
        ).value.trim();


      const reason =
        document.getElementById(
          "reason"
        ).value.trim();


      if (
        !fullName ||
        !email ||
        !phone ||
        !address ||
        !reason
      ) {

        adoptionMessage.innerHTML = `
          <div class="alert alert-danger">
            Semua data harus diisi.
          </div>
        `;

        return;

      }


      adoptionMessage.innerHTML = `
        <div class="alert alert-success">
          Pengajuan adopsi berhasil dikirim!
        </div>
      `;


      setTimeout(() => {

        adoptionModal.hide();

        adoptionForm.reset();

        adoptionMessage.innerHTML =
          "";

      }, 1500);

    }
  );

});