document.addEventListener('DOMContentLoaded', () => {
  // 1. Suntik CSS Ringkas
  document.head.insertAdjacentHTML('beforeend', `<style>.booking-form{background:#f8fafc;border:2px solid var(--button,#BDE8F5);border-radius:20px;padding:25px;margin-top:20px}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:15px;margin-bottom:20px}.form-group{display:flex;flex-direction:column;gap:5px}.full-width{grid-column:span 2}.form-group label{font-size:14px;font-weight:600;color:var(--primary,#0F2854)}.form-group input,.form-group select{width:100%;padding:10px;border:1px solid #ccc;border-radius:10px}.btn-submit{width:100%;background:var(--primary,#0F2854);color:#fff;padding:12px;border:none;border-radius:10px;cursor:pointer}.badge-status{padding:4px 10px;border-radius:10px;font-size:12px;background:#fef08a;color:#854d0e}</style>`);

  // 2. Suntik HTML Form Ringkas
  const section = document.querySelector('.section');
  if (section) {
    section.insertAdjacentHTML('afterend', `
      <section class="section" id="booking-section">
        <h2 class="section-title">Form Booking</h2>
        <form id="bookingForm" class="booking-form">
          <div class="form-grid">
            <div class="form-group"><label>Nama Pemilik</label><input type="text" id="oName" required></div>
            <div class="form-group"><label>Nama Hewan</label><input type="text" id="pName" required></div>
            <div class="form-group"><label>Jenis Hewan</label><select id="pType"><option>Kucing</option><option>Anjing</option><option>Lainnya</option></select></div>
            <div class="form-group"><label>Layanan</label><select id="sType"><option>Booking Jadwal Periksa</option><option>Jadwalkan Vaksin</option><option>Panggil Dokter Ke Rumah</option></select></div>
            <div class="form-group full-width"><label>Waktu</label><input type="datetime-local" id="bDate" required></div>
          </div>
          <button type="submit" class="btn-submit">Kirim Booking 🐾</button>
        </form>
      </section>`);
  }

  // 3. Logika Form & LocalStorage
  const form = document.getElementById('bookingForm');
  const container = document.querySelector('.appointment-container');
  const data = JSON.parse(localStorage.getItem('pawData') || '[]');
  const icons = { 'Kucing': '🐈', 'Anjing': '🦮', 'Lainnya': '🐾' };

  // Format Kartu (Dinamis mengikuti status dari admin)
  const renderCard = (d) => {
    const isDisetujui = d.status === 'Disetujui';

    const badgeStatus = isDisetujui
      ? `<span class="badge-status" style="display:inline-block; width:fit-content; margin-top:6px; background-color: #bbf7d0; color: #166534;">Disetujui</span>`
      : `<span class="badge-status" style="display:inline-block; width:fit-content; margin-top:6px;">Menunggu Konfirmasi</span>`;

    const tombolAksi = isDisetujui
      ? `<button class="btn-proses" style="background-color: #e2e8f0; color: #64748b; cursor: not-allowed;" disabled>Selesai</button>`
      : `<button class="btn-proses">Proses</button>`;

    return `
        <div class="appointment-card">
          <div class="pet-icon-wrapper">
            <div class="card-icon">${icons[d.pt] || '🐾'}</div>
          </div>
          <div class="appt-info">
            <p><strong>Nama hewan:</strong> ${d.pn} (${d.pt})</p>
            <p><strong>Jenis Appoinment:</strong> ${d.st}</p>
            ${badgeStatus}
          </div>
          <div class="appt-time">
            <p class="time-label">Tanggal & waktu</p>
            <p class="time-value">${new Date(d.bd).toLocaleString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace(' pukul ', ', ')}</p>
          </div>
          ${tombolAksi}
        </div>`;
  };

  // Tampilkan data lama
  if (container) data.forEach(d => container.insertAdjacentHTML('afterbegin', renderCard(d)));

  // Klik card utama -> otomatis isi form
  document.querySelectorAll('.cards-grid .card').forEach(c => {
    c.onclick = () => {
      document.getElementById('sType').value = c.querySelector('h3').innerText;
      form?.scrollIntoView({ behavior: 'smooth' });
    }
  });

  // Saat form disubmit
  if (form) {
    form.onsubmit = (e) => {
      e.preventDefault();
      const newData = {
        pn: pName.value,
        pt: pType.value,
        st: sType.value,
        bd: bDate.value,
        status: 'Menunggu Konfirmasi'
      };

      data.unshift(newData);
      localStorage.setItem('pawData', JSON.stringify(data));
      container.insertAdjacentHTML('afterbegin', renderCard(newData));

      form.reset();
      alert('🎉 Booking Berhasil Dibuat!');
    };
  }
});