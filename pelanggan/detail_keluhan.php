<?php
session_start();
require '../config/database.php';

// Keamanan: Cek sesi pelanggan dan ID keluhan
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pelanggan' || !isset($_GET['id'])) {
    header('Location: ../login.php');
    exit;
}

$id_keluhan = $_GET['id'];
$id_user = $_SESSION['id_user'];
$id_pelanggan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id_pelanggan FROM pelanggan WHERE id_user = '$id_user'"))['id_pelanggan'];

// Ambil detail keluhan utama dan pastikan milik pelanggan yang login
$query_keluhan = "SELECT * FROM keluhan WHERE id_keluhan = ? AND id_pelanggan = ?";
$stmt_keluhan = mysqli_prepare($koneksi, $query_keluhan);
mysqli_stmt_bind_param($stmt_keluhan, "ii", $id_keluhan, $id_pelanggan);
mysqli_stmt_execute($stmt_keluhan);
$keluhan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_keluhan));

if (!$keluhan) {
    header('Location: keluhan.php?status=notfound'); // Keluhan tidak ditemukan atau bukan milik Anda
    exit;
}

// Logika saat pelanggan mengirim balasan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['isi_pesan'])) {
    $isi_pesan = mysqli_real_escape_string($koneksi, $_POST['isi_pesan']);

    $query_insert = "INSERT INTO pesan_keluhan (id_keluhan, id_pengirim, isi_pesan) VALUES (?, ?, ?)";
    $stmt_insert = mysqli_prepare($koneksi, $query_insert);
    mysqli_stmt_bind_param($stmt_insert, "iis", $id_keluhan, $id_user, $isi_pesan);
    mysqli_stmt_execute($stmt_insert);

    header("Location: detail_keluhan.php?id=" . $id_keluhan);
    exit;
}


// Ambil semua riwayat pesan untuk keluhan ini
$query_pesan = "SELECT pesan_keluhan.*, users.nama_lengkap, users.role FROM pesan_keluhan
                JOIN users ON pesan_keluhan.id_pengirim = users.id_user
                WHERE id_keluhan = ? ORDER BY tanggal_kirim ASC";
$stmt_pesan = mysqli_prepare($koneksi, $query_pesan);
mysqli_stmt_bind_param($stmt_pesan, "i", $id_keluhan);
mysqli_stmt_execute($stmt_pesan);
$riwayat_pesan = mysqli_stmt_get_result($stmt_pesan);

require '../includes/header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../assets/css/admin_style.css">
<nav class="navbar navbar-expand-lg navbar-dark bg-info shadow-sm"></nav>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Detail Keluhan</h2>
        <a href="keluhan.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0">Subjek: <?= htmlspecialchars($keluhan['subjek']); ?></h5>
        </div>
        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
            <?php while ($pesan = mysqli_fetch_assoc($riwayat_pesan)): ?>
                <?php if ($pesan['role'] == 'pelanggan'): ?>
                    <div class="d-flex justify-content-end mb-3">
                        <div class="bg-primary text-white p-3 rounded" style="max-width: 70%;">
                            <p class="mb-1 fw-bold">Anda</p>
                            <p class="mb-1"><?= nl2br(htmlspecialchars($pesan['isi_pesan'])); ?></p>
                            <?php if ($pesan['gambar_bukti']): ?>
                                <a href="../uploads/bukti_keluhan/<?= $pesan['gambar_bukti']; ?>" target="_blank"
                                    class="text-white">Lihat Bukti</a>
                            <?php endif; ?>
                            <small
                                class="d-block text-light text-end"><?= date('d M Y, H:i', strtotime($pesan['tanggal_kirim'])); ?></small>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="d-flex justify-content-start mb-3">
                        <div class="bg-light p-3 rounded" style="max-width: 70%;">
                            <p class="mb-1 fw-bold">Admin</p>
                            <p class="mb-1"><?= nl2br(htmlspecialchars($pesan['isi_pesan'])); ?></p>
                            <small
                                class="d-block text-muted"><?= date('d M Y, H:i', strtotime($pesan['tanggal_kirim'])); ?></small>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endwhile; ?>
        </div>
        <div class="card-footer">
            <form action="detail_keluhan.php?id=<?= $id_keluhan; ?>" method="POST">
                <div class="input-group">
                    <textarea name="isi_pesan" class="form-control" placeholder="Ketik balasan Anda..."
                        required></textarea>
                    <button class="btn btn-primary" type="submit"><i class="bi bi-send-fill"></i> Kirim</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>