<?php
require 'config/database.php';

// Cek apakah ID event ada di URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: index.php');
    exit;
}

$id_event = $_GET['id'];

// Ambil data event spesifik dari database
$query = "SELECT * FROM events WHERE id_event = ?";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id_event);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$event = mysqli_fetch_assoc($result);

// Jika event tidak ditemukan, kembali ke home
if (!$event) {
    header('Location: index.php');
    exit;
}

require 'includes/header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="assets/css/style.css">

<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">⚡ PT Harrindo Daya Tama</a>
        <div class="ms-auto">
            <a class="nav-link" href="login.php">Login</a>
        </div>
    </div>
</nav>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h1 class="fw-bold mb-3"><?= htmlspecialchars($event['judul']); ?></h1>
            <p class="text-muted"><i class="bi bi-calendar3"></i> Diposting pada
                <?= date('d F Y', strtotime($event['tanggal_posting'])); ?></p>
            <img src="uploads/events/<?= htmlspecialchars($event['gambar']); ?>"
                class="img-fluid rounded shadow-sm my-4" alt="<?= htmlspecialchars($event['judul']); ?>">
            <div class="fs-5">
                <?= nl2br(htmlspecialchars($event['keterangan'])); ?>
            </div>
            <hr class="my-4">
            <a href="index.php" class="btn btn-primary"><i class="bi bi-arrow-left"></i> Kembali ke Berita Lainnya</a>
        </div>
    </div>
</div>

<footer class="bg-dark text-white text-center p-3">
    <p class="mb-0">&copy; <?= date('Y'); ?> PT Harrindo Daya Tama. All Rights Reserved.</p>
</footer>

<?php require 'includes/footer.php'; ?>