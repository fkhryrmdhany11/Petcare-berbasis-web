 // Toggle sidebar saat logo hamster diklik
  const app = document.getElementById('app');
  const toggle = document.getElementById('sidebarToggle');
  toggle.addEventListener('click', () => {
    app.classList.toggle('sidebar-collapsed');
  });