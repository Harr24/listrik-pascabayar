<?php require 'includes/header.php'; ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="assets/css/style.css">

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">⚡ PT Harrindo Daya Tama</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-primary text-white" href="register.php">Daftar Sekarang</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="hero-section">
    <div class="hero-slider">
        <div class="hero-slide active"></div>
        <div class="hero-slide"></div>
        <div class="hero-slide"></div>
    </div>
    <div class="hero-content text-center text-white">
        <div class="container">
            <h1 class="display-4 fw-bold">Solusi Listrik Pascabayar Modern</h1>
            <p class="lead my-4">Monitor pemakaian, bayar tagihan, dan kelola akun listrik Anda dengan mudah di satu
                tempat.</p>
            <a href="register.php" class="btn btn-lg btn-success">
                <i class="bi bi-person-plus-fill"></i> Bergabung Menjadi Pengguna
            </a>
        </div>
    </div>
</div>

<div class="container text-center py-4">
    <h4 class="text-muted">Telah Dipercaya oleh</h4>
    <h2 class="display-5 fw-bold" id="jumlah-pengguna">...</h2>
    <h4 class="text-muted">Pengguna Terdaftar</h4>
</div>

<div class="container text-center py-4">
    <h4 class="text-muted mb-3">Layanan Daya Tersedia</h4>
    <div id="layanan-list" class="d-flex justify-content-center gap-3">
    </div>
</div>


<div class="bg-light">
    <div class="container py-5">
        <div class="text-center mb-5">
            <h2>Layanan Kami</h2>
            <p class="lead text-muted">Fitur yang kami sediakan untuk kemudahan Anda.</p>
        </div>
        <div class="row">
            <div class="col-md-4 text-center">
                <div class="info-box">
                    <i class="bi bi-credit-card-2-front-fill display-3 text-primary"></i>
                    <h4 class="mt-3">Pembayaran Mudah</h4>
                    <p>Bayar tagihan Anda kapan saja dan di mana saja dengan berbagai metode pembayaran.</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="info-box">
                    <i class="bi bi-graph-up-arrow display-3 text-primary"></i>
                    <h4 class="mt-3">Monitoring Real-time</h4>
                    <p>Lacak riwayat pemakaian listrik Anda setiap bulan secara transparan.</p>
                </div>
            </div>
            <div class="col-md-4 text-center">
                <div class="info-box">
                    <i class="bi bi-bell-fill display-3 text-primary"></i>
                    <h4 class="mt-3">Notifikasi Tagihan</h4>
                    <p>Dapatkan pengingat otomatis saat tagihan baru Anda terbit.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="bg-dark text-white text-center p-3">
    <p class="mb-0">&copy; 2025 PT Harrindo Daya Tama. All Rights Reserved.</p>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // --- Fungsi untuk jumlah pengguna ---
        function updateJumlahPengguna() {
            fetch('jumlah_pengguna.php')
                .then(response => response.json())
                .then(data => {
                    const jumlahFormatted = new Intl.NumberFormat('id-ID').format(data.total);
                    document.getElementById('jumlah-pengguna').textContent = jumlahFormatted;
                })
                .catch(error => console.error('Error:', error));
        }

        // --- FUNGSI BARU: Untuk menampilkan layanan daya ---
        function updateLayananTersedia() {
            fetch('get_layanan.php')
                .then(response => response.json())
                .then(data => {
                    const container = document.getElementById('layanan-list');
                    container.innerHTML = ''; // Kosongkan container dulu
                    data.layanan.forEach(daya => {
                        // Buat elemen span untuk setiap daya
                        const span = document.createElement('span');
                        span.className = 'badge bg-secondary fs-6';
                        span.textContent = new Intl.NumberFormat('id-ID').format(daya) + ' VA';
                        container.appendChild(span);
                    });
                })
                .catch(error => console.error('Error:', error));
        }

        // --- Panggil semua fungsi saat halaman dimuat ---
        updateJumlahPengguna();
        updateLayananTersedia();

        // --- Blok untuk background slider ---
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
    });
</script>

<?php require 'includes/footer.php'; ?>