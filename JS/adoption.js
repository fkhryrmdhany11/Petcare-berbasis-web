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
});