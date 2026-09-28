document.addEventListener('DOMContentLoaded', () => {
    const areaLayanan = document.querySelector('.section');
    if (areaLayanan) {
        areaLayanan.insertAdjacentHTML('afterend', `
            <section class="section" id="booking-section">
                <h2 class="section-title">Form Booking Layanan</h2>
                <form id="formBooking" class="booking-form" style="background: #f8fafc; border: 2px solid #BDE8F5; border-radius: 20px; padding: 25px; margin-top: 20px;">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                        
                        <div style="display: flex; flex-direction: column; gap: 5px;">
                            <label style="font-size: 14px; font-weight: 600; color: #0F2854;">Nama Pemilik</label>
                            <input type="text" id="inputNamaPemilik" placeholder="Masukkan nama Anda" required style="padding: 10px; border: 1px solid #ccc; border-radius: 10px;">
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 5px;">
                            <label style="font-size: 14px; font-weight: 600; color: #0F2854;">Nama Hewan</label>
                            <input type="text" id="inputNamaHewan" placeholder="Nama peliharaan" required style="padding: 10px; border: 1px solid #ccc; border-radius: 10px;">
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 5px;">
                            <label style="font-size: 14px; font-weight: 600; color: #0F2854;">Jenis Hewan</label>
                            <select id="selectJenisHewan" style="padding: 10px; border: 1px solid #ccc; border-radius: 10px;">
                                <option value="Kucing">Kucing 🐈</option>
                                <option value="Anjing">Anjing 🦮</option>
                                <option value="Lainnya">Lainnya 🐾</option>
                            </select>
                        </div>

                        <div style="display: flex; flex-direction: column; gap: 5px;">
                            <label style="font-size: 14px; font-weight: 600; color: #0F2854;">Pilih Layanan</label>
                            <select id="selectJenisLayanan" style="padding: 10px; border: 1px solid #ccc; border-radius: 10px;">
                                <option value="Booking Jadwal Periksa" data-harga="100000">Booking Jadwal Periksa (Rp 100.000)</option>
                                <option value="Jadwalkan Vaksin" data-harga="150000">Jadwalkan Vaksin (Rp 150.000)</option>
                                <option value="Panggil Dokter Ke Rumah" data-harga="250000">Panggil Dokter Ke Rumah (Rp 250.000)</option>
                            </select>
                        </div>

                        <div style="grid-column: span 2; display: flex; flex-direction: column; gap: 5px;">
                            <label style="font-size: 14px; font-weight: 600; color: #0F2854;">Tanggal & Waktu</label>
                            <input type="datetime-local" id="inputTanggalBooking" required style="padding: 10px; border: 1px solid #ccc; border-radius: 10px;">
                        </div>

                    </div>
                    <button type="submit" style="width: 100%; background: #0F2854; color: #fff; padding: 12px; border: none; border-radius: 10px; font-weight: 600; cursor: pointer;">
                        Kirim Booking 🐾
                    </button>
                </form>
            </section>
        `);
    }

    const formBooking = document.getElementById('formBooking');
    const containerRiwayat = document.querySelector('.appointment-container');
    
    // Membaca data yang tersimpan
    let daftarBooking = JSON.parse(localStorage.getItem('pawData') || '[]');

    // Ikon hewan
    const ikonHewan = { 'Kucing': '🐈', 'Anjing': '🦮', 'Lainnya': '🐾' };

    // Function membuat tampilan kartu riwayat booking
    const buatKartuBooking = (data) => {
        let warnaBadge = 'background: #fef08a; color: #854d0e;'; // Kuning (Menunggu)
        if (data.status === 'Disetujui' || data.status === 'Selesai') {
            warnaBadge = 'background: #bbf7d0; color: #166534;'; // Hijau
        } else if (data.status === 'Ditolak') {
            warnaBadge = 'background: #fecaca; color: #991b1b;'; // Merah
        }

        // Format tanggal ke Bahasa Indonesia
        const tanggalFormat = data.tanggalBooking ? new Date(data.tanggalBooking).toLocaleString('id-ID', {
            day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit'
        }).replace(' pukul ', ', ') : '-';

        return `
        <div class="appointment-card">
            <div class="pet-icon-wrapper">
                <div class="card-icon">${ikonHewan[data.jenisHewan] || '🐾'}</div>
            </div>
            <div class="appt-info">
                <p><strong>Nama hewan:</strong> ${data.namaHewan} (${data.jenisHewan})</p>
                <p><strong>Jenis Appointment:</strong> ${data.jenisLayanan}</p>
                <span style="display:inline-block; width:fit-content; padding: 4px 10px; border-radius: 10px; font-size: 12px; font-weight: 600; ${warnaBadge}">
                    ${data.status}
                </span>
            </div>
            <div class="appt-time">
                <p class="time-label">Tanggal & waktu</p>
                <p class="time-value">${tanggalFormat}</p>
            </div>
            <button class="btn-proses" style="${data.status !== 'Menunggu Konfirmasi' ? 'background-color: #e2e8f0; color: #64748b; cursor: not-allowed;' : ''}" ${data.status !== 'Menunggu Konfirmasi' ? 'disabled' : ''}>
                ${data.status === 'Menunggu Konfirmasi' ? 'Proses' : data.status}
            </button>
        </div>`;
    };

    // Tampilkan data yang tersimpan ke layar
    if (containerRiwayat && daftarBooking.length > 0) {
        containerRiwayat.innerHTML = '';
        daftarBooking.forEach(item => containerRiwayat.insertAdjacentHTML('beforeend', buatKartuBooking(item)));
    }

    if (formBooking) {
        formBooking.onsubmit = (event) => {
            event.preventDefault();

            const selectLayanan = document.getElementById('selectJenisLayanan');
            const hargaSelected = selectLayanan.options[selectLayanan.selectedIndex].getAttribute('data-harga');

            // Data booking baru
            const dataBaru = {
                id: 'BK-' + Math.floor(10000 + Math.random() * 90000), // Contoh ID: BK-58291
                namaPemilik: document.getElementById('inputNamaPemilik').value,
                namaHewan: document.getElementById('inputNamaHewan').value,
                jenisHewan: document.getElementById('selectJenisHewan').value,
                jenisLayanan: selectLayanan.value,
                harga: parseInt(hargaSelected),
                tanggalBooking: document.getElementById('inputTanggalBooking').value,
                status: 'Menunggu Konfirmasi',
                alasanTolak: ''
            };

            // Simpan ke array & LocalStorage
            daftarBooking.unshift(dataBaru);
            localStorage.setItem('pawData', JSON.stringify(daftarBooking));

            // Update riwayat tampilan
            if (containerRiwayat) {
                containerRiwayat.insertAdjacentHTML('afterbegin', buatKartuBooking(dataBaru));
            }

            // Reset Form
            formBooking.reset();

            // TAMPILKAN POP-UP NOTIFIKASI DI TENGAH
            tampilkanPopUpSukses();
        };
    }

    // Function untuk memunculkan Modal Pop-Up Tengah
    function tampilkanPopUpSukses() {
        const modalPopUp = `
            <div id="modalNotifikasi" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 9999; font-family: 'Poppins', sans-serif;">
                <div style="background: #ffffff; border-radius: 20px; padding: 35px 25px; width: 90%; max-width: 420px; text-align: center; box-shadow: 0 10px 25px rgba(0,0,0,0.2); border-top: 8px solid #0F2854; animation: popIn 0.3s ease-out;">
                    <div style="font-size: 55px; margin-bottom: 10px;">🎉</div>
                    <h2 style="font-family: 'Fredoka', sans-serif; color: #0F2854; margin-bottom: 10px; font-size: 24px; font-weight: 700;">Booking Berhasil!</h2>
                    <p style="color: #64748b; font-size: 14px; line-height: 1.5; margin-bottom: 20px;">
                        Permintaan booking Anda telah berhasil dikirim. Tim PawPaw Pet Care akan segera mengkonfirmasi jadwal Anda.
                    </p>
                    <button onclick="document.getElementById('modalNotifikasi').remove()" style="background: #0F2854; color: #ffffff; border: none; padding: 12px 25px; border-radius: 25px; font-size: 15px; font-weight: 600; cursor: pointer; width: 100%; transition: 0.2s;">
                        Tutup & Lihat Jadwal
                    </button>
                </div>
            </div>
        `;
        document.body.insertAdjacentHTML('beforeend', modalPopUp);
    }
});