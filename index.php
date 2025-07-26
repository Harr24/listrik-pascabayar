<?php
require 'config/database.php';
require 'includes/header.php';

// Ambil 4 event terbaru
$query_events = mysqli_query($koneksi, "SELECT * FROM events ORDER BY tanggal_posting DESC LIMIT 4");
?>

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">⚡ PT Harrindo Daya Tama</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto align-items-center">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownTarif" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        Daftar Harga/kWh
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownTarif"
                        id="tarif-dropdown-list">
                        <li><a class="dropdown-item" href="#">Memuat...</a></li>
                    </ul>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="login.php">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link btn btn-primary text-white ms-2" href="register.php">Daftar Sekarang</a>
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
    <div class="hero-content text-center text-white" data-aos="fade-up" data-aos-duration="1000">
        <div class="container">
            <h1 class="display-4 fw-bold">Solusi Listrik Pascabayar Modern</h1>
            <p class="lead my-4">Monitor pemakaian, bayar tagihan, dan kelola akun listrik Anda dengan mudah di satu
                tempat.</p>
            <a href="register.php" class="btn btn-lg btn-success rounded-pill px-4">
                <i class="bi bi-person-plus-fill"></i> Bergabung Menjadi Pengguna
            </a>
            <p class="mt-4 small">
                Atau hubungi admin kami di WhatsApp
                <a href="https://wa.me/6281326740142" target="_blank" class="text-white fw-bold">081326740142</a>
                untuk bergabung.
            </p>
        </div>
    </div>
</div>

<div class="container py-5">
    <div class="row text-center">
        <div class="col-md-6 mb-4 mb-md-0" data-aos="fade-up">
            <h4 class="text-muted">Telah Dipercaya oleh</h4>
            <h2 class="display-5 fw-bold" id="jumlah-pengguna">0</h2>
            <h4 class="text-muted">Pengguna Terdaftar</h4>
        </div>
        <div class="col-md-6" data-aos="fade-up" data-aos-delay="200">
            <h4 class="text-muted">Didukung oleh</h4>
            <h2 class="display-5 fw-bold" id="jumlah-teknisi">0</h2>
            <h4 class="text-muted">Teknisi Handal</h4>
        </div>
    </div>
</div>

<div class="container my-5" data-aos="fade-up">
    <div class="text-center mb-5">
        <h2 class="fw-bold">Event & Berita Terkini</h2>
        <p class="lead text-muted">Ikuti perkembangan dan informasi terbaru dari kami.</p>
    </div>
    <div class="row g-4">
        <?php if (isset($query_events) && mysqli_num_rows($query_events) > 0): ?>
            <?php $delay = 0; ?>
            <?php while ($event = mysqli_fetch_assoc($query_events)): ?>
                <div class="col-md-6 col-lg-3" data-aos="zoom-in" data-aos-delay="<?= $delay; ?>">
                    <div class="card h-100 shadow-sm card-event">
                        <img src="uploads/events/<?= htmlspecialchars($event['gambar']); ?>" class="card-img-top"
                            alt="<?= htmlspecialchars($event['judul']); ?>">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($event['judul']); ?></h5>
                            <p class="card-text text-muted small flex-grow-1">
                                <?= date('d F Y', strtotime($event['tanggal_posting'])); ?>
                            </p>
                            <a href="event_detail.php?id=<?= $event['id_event']; ?>" class="stretched-link"></a>
                        </div>
                        <div class="card-footer bg-transparent border-0">
                            <a href="event_detail.php?id=<?= $event['id_event']; ?>"
                                class="btn btn-outline-primary w-100">Selengkapnya</a>
                        </div>
                    </div>
                </div>
                <?php $delay += 100; ?>
            <?php endwhile; ?>
        <?php else: ?>
            <p class="text-center">Belum ada event atau berita untuk ditampilkan.</p>
        <?php endif; ?>
    </div>
</div>

<div class="bg-light">
    <div class="container text-center py-5" data-aos="fade-up">
        <h4 class="text-muted mb-3">Layanan Daya Tersedia</h4>
        <div id="layanan-list" class="d-flex flex-wrap justify-content-center gap-3"></div>
    </div>
</div>

<footer class="bg-dark text-white text-center p-4">
    <div class="container">
        <p class="mb-0">&copy; <?= date('Y'); ?> PT Harrindo Daya Tama. All Rights Reserved.</p>
    </div>
</footer>

<?php require 'includes/footer.php'; ?>