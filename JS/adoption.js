
const pets = [
  {
    id: 1,
    nama: "Siti",
    jenis: "kucing",
    ras: "Anggora",
    gender: "Betina",
    umur: "1 Tahun",
    status: "Tersedia",
    image: "../assets/img/anggora.jpg"
  },
  {
    id: 2,
    nama: "Jeno",
    jenis: "lainnya",
    ras: "African Pygmy",
    gender: "Jantan",
    umur: "8 Bulan",
    status: "Tersedia",
    image: "../assets/img/goat.jpg"
  },
  {
    id: 3,
    nama: "Jubariyah",
    jenis: "kucing",
    ras: "Bengal",
    gender: "Betina",
    umur: "13 Bulan",
    status: "Sudah Diadopsi",
    image: "../assets/img/bengal.jpg"
  },
  {
    id: 4,
    nama: "Noe",
    jenis: "kelinci",
    ras: "Rex",
    gender: "Jantan",
    umur: "10 Bulan",
    status: "Tersedia",
    image: "../assets/img/rabbit.jpg"
  }
];

const grid = document.getElementById("adoptionGrid");
const modal = document.getElementById("adoptionModal");
const closeModal = document.getElementById("closeModal");
const form = document.getElementById("adoptionForm");
const selectedPet = document.getElementById("selectedPet");
const successMessage = document.getElementById("successMessage");

function renderPets(category = "semua") {
  const filtered = category === "semua" ? pets : pets.filter(pet => pet.jenis === category);

  grid.innerHTML = filtered.map(pet => {
    const tersedia = pet.status === "Tersedia";

    return `
      <div class="pet-card">
        <img src="${pet.image}" alt="${pet.nama}" class="pet-image">
        <div class="pet-info">
          <h3>${pet.nama}</h3>
          <p>Ras: ${pet.ras}</p>
          <p>Gender: ${pet.gender}</p>
          <p>Umur: ${pet.umur}</p>
          <p class="pet-status ${tersedia ? "status-available" : "status-unavailable"}">
            Status: ${tersedia ? "Tersedia" : "Sudah Diadopsi / Tidak Tersedia"}
          </p>
          <button class="adoption-button" ${tersedia ? "" : "disabled"} data-id="${pet.id}">
            ${tersedia ? "Ajukan Adopsi" : "Tidak Tersedia"}
          </button>
        </div>
      </div>
    `;
  }).join("");

  document.querySelectorAll(".adoption-button:not(:disabled)").forEach(button => {
    button.addEventListener("click", () => openModal(Number(button.dataset.id)));
  });
}

function openModal(id) {
  const pet = pets.find(item => item.id === id);
  if (!pet) return;

  selectedPet.textContent = pet.nama;
  document.getElementById("petName").value = pet.nama;
  document.getElementById("petJenis").value = pet.jenis;
  document.getElementById("petRas").value = pet.ras;
  document.getElementById("petGender").value = pet.gender;
  document.getElementById("petUmur").value = pet.umur;
  document.getElementById("petImage").value = pet.image;

  modal.classList.add("show");
  document.body.style.overflow = "hidden";
}

function closeAdoptionModal() {
  modal.classList.remove("show");
  document.body.style.overflow = "";
}

document.querySelectorAll(".category-btn").forEach(button => {
  button.addEventListener("click", () => {
    document.querySelectorAll(".category-btn").forEach(btn => btn.classList.remove("active"));
    button.classList.add("active");
    renderPets(button.dataset.category);
  });
});

closeModal.addEventListener("click", closeAdoptionModal);

modal.addEventListener("click", event => {
  if (event.target === modal) closeAdoptionModal();
});

form.addEventListener("submit", event => {
  event.preventDefault();

  const request = {
    id: Date.now(),
    nama: document.getElementById("nama").value.trim(),
    email: document.getElementById("email").value.trim(),
    telepon: document.getElementById("telepon").value.trim(),
    alamat: document.getElementById("alamat").value.trim(),
    alasan: document.getElementById("alasan").value.trim(),
    hewan: document.getElementById("petName").value,
    jenis: document.getElementById("petJenis").value,
    ras: document.getElementById("petRas").value,
    gender: document.getElementById("petGender").value,
    umur: document.getElementById("petUmur").value,
    image: document.getElementById("petImage").value,
    tanggal: new Date().toISOString(),
    status: "Menunggu Verifikasi"
  };

  const requests = JSON.parse(localStorage.getItem("adoptionRequests") || "[]");
  const history = JSON.parse(localStorage.getItem("adoptionHistory") || "[]");

  requests.push(request);
  history.push(request);

  localStorage.setItem("adoptionRequests", JSON.stringify(requests));
  localStorage.setItem("adoptionHistory", JSON.stringify(history));

  form.reset();
  closeAdoptionModal();

  successMessage.classList.add("show");

  setTimeout(() => {
    successMessage.classList.remove("show");
  }, 1800);
});

renderPets();

