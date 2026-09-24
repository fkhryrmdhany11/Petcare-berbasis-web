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


      // Nama hewan otomatis masuk

      petNameInput.value =
        petName;

      adoptionMessage.innerHTML =
        "";

      adoptionModal.show();

    });

  });


  adoptionForm.addEventListener(
    "submit",
    async (event) => {

      event.preventDefault();


      const submitButton =
        adoptionForm.querySelector(
          'button[type="submit"]'
        );


      const formData = {

        pet_name:
          document.getElementById(
            "petName"
          ).value.trim(),

        full_name:
          document.getElementById(
            "fullName"
          ).value.trim(),

        email:
          document.getElementById(
            "email"
          ).value.trim(),

        phone:
          document.getElementById(
            "phone"
          ).value.trim(),

        address:
          document.getElementById(
            "address"
          ).value.trim(),

        reason:
          document.getElementById(
            "reason"
          ).value.trim()

      };

      if (
        !formData.pet_name ||
        !formData.full_name ||
        !formData.email ||
        !formData.phone ||
        !formData.address ||
        !formData.reason
      ) {

        adoptionMessage.innerHTML = `
          <div class="alert alert-danger">
            Semua data harus diisi.
          </div>
        `;

        return;

      }


      submitButton.disabled = true;

      submitButton.textContent =
        "Mengirim...";


      try {

        const response =
          await fetch(
            "../api/adoption.php",
            {

              method: "POST",

              headers: {
                "Content-Type":
                  "application/json"
              },

              body:
                JSON.stringify(
                  formData
                )

            }
          );


        const result =
          await response.json();

        if (result.success) {

          adoptionMessage.innerHTML = `
            <div class="alert alert-success">
              Pengajuan adopsi berhasil dikirim.
            </div>
          `;


          setTimeout(() => {

            adoptionModal.hide();

            adoptionForm.reset();

            adoptionMessage.innerHTML = "";

          }, 1500);


        } else {

          adoptionMessage.innerHTML = `
            <div class="alert alert-danger">
              ${
                result.message ||
                "Pengajuan gagal dikirim."
              }
            </div>
          `;

        }


      } catch (error) {

        console.error(error);


        adoptionMessage.innerHTML = `
          <div class="alert alert-danger">
            Terjadi kesalahan saat mengirim data.
          </div>
        `;

      }


      

      submitButton.disabled = false;

      submitButton.textContent =
        "Ajukan Adopsi";

    }
  );

});