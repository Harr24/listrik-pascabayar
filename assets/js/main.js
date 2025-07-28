// main.js

document.addEventListener('DOMContentLoaded', function() {

    // Fungsi untuk animasi hitung naik (count up)
    function animateCountUp(elementId, target) {
        const el = document.getElementById(elementId);
        if (!el) return; // Hentikan jika elemen tidak ditemukan
        let current = 0;
        const step = Math.ceil(target / 50) || 1; // Pastikan step minimal 1
        const interval = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(interval);
            }
            el.textContent = new Intl.NumberFormat('id-ID').format(current);
        }, 30);
    }

    // Ambil dan tampilkan jumlah pengguna
    function updateJumlahPengguna() {
        fetch('jumlah_pengguna.php')
            .then(response => response.json())
            .then(data => {
                animateCountUp('jumlah-pengguna', parseInt(data.total));
            })
            .catch(error => console.error('Error fetching user count:', error));
    }

    // Ambil dan tampilkan jumlah teknisi
    function updateJumlahTeknisi() {
        fetch('jumlah_teknisi.php')
            .then(response => response.json())
            .then(data => {
                animateCountUp('jumlah-teknisi', parseInt(data.total));
            })
            .catch(error => console.error('Error fetching technician count:', error));
    }

    // Ambil dan tampilkan daftar layanan daya
    function updateLayananTersedia() {
        fetch('get_layanan.php')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('layanan-list');
                if (!container) return;
                container.innerHTML = '';
                data.layanan.forEach(daya => {
                    const span = document.createElement('span');
                    span.className = 'badge bg-dark fs-6';
                    span.textContent = new Intl.NumberFormat('id-ID').format(daya) + ' VA';
                    container.appendChild(span);
                });
            })
            .catch(error => console.error('Error fetching services:', error));
    }

    // Ambil dan tampilkan daftar harga di dropdown navbar
    function updateDaftarHarga() {
        fetch('get_tarif_list.php')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('tarif-dropdown-list');
                if (!container) return;
                container.innerHTML = '';
                if (data.tarif.length > 0) {
                    data.tarif.forEach(tarif => {
                        const listItem = document.createElement('li');
                        const link = document.createElement('a');
                        link.className = 'dropdown-item';
                        link.href = '#';
                        const harga = new Intl.NumberFormat('id-ID').format(tarif.tarif_per_kwh);
                        const daya = new Intl.NumberFormat('id-ID').format(tarif.daya);
                        link.innerHTML = `<div class="d-flex justify-content-between"><span><strong>${daya} VA</strong> (${tarif.golongan_tarif})</span> <span class="text-primary fw-bold">Rp ${harga}/kWh</span></div>`;
                        listItem.appendChild(link);
                        container.appendChild(listItem);
                    });
                } else {
                    container.innerHTML = '<li><a class="dropdown-item" href="#">Data tidak tersedia</a></li>';
                }
            })
            .catch(error => console.error('Error fetching tariff list:', error));
    }

    // Fungsi BARU untuk menampilkan area layanan
    function updateAreaLayanan() {
        fetch('get_area.php')
            .then(response => response.json())
            .then(data => {
                const container = document.getElementById('area-list');
                if (!container) return;
                container.innerHTML = ''; // Kosongkan dulu
                data.area.forEach(nama_area => {
                    const span = document.createElement('span');
                    span.className = 'badge bg-info text-dark fs-6';
                    span.textContent = nama_area;
                    container.appendChild(span);
                });
            })
            .catch(error => console.error('Error fetching area list:', error));
    }

    // Panggil semua fungsi inisialisasi
    updateJumlahPengguna();
    updateJumlahTeknisi();
    updateLayananTersedia();
    updateDaftarHarga();
    updateAreaLayanan(); // Panggil fungsi baru

    // Logika untuk background slider
    const slides = document.querySelectorAll('.hero-slide');
    let currentSlide = 0;
    if (slides.length > 0) {
        function nextSlide() {
            slides[currentSlide].classList.remove('active');
            currentSlide = (currentSlide + 1) % slides.length;
            slides[currentSlide].classList.add('active');
        }
        setInterval(nextSlide, 5000);
    }

    // Inisialisasi AOS (Animate On Scroll)
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 1000,
            once: true,
            offset: 100,
        });
    }
});