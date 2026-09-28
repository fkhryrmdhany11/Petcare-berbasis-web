document.addEventListener("DOMContentLoaded", () => {

  const sidebar =
    document.getElementById("sbSidebar");

  const overlay =
    document.getElementById("sbOverlay");

  const menuButton =
    document.getElementById("sbMenuButton");


  function toggleSidebar(forceOpen) {

    const shouldOpen =
      typeof forceOpen === "boolean"
        ? forceOpen
        : !sidebar.classList.contains("open");

    sidebar.classList.toggle("open", shouldOpen);
    overlay.classList.toggle("show", shouldOpen);

  }


  menuButton.addEventListener("click", () => toggleSidebar());

  overlay.addEventListener("click", () => toggleSidebar(false));

});