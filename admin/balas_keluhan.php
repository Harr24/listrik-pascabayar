<?php
session_start();
require '../config/database.php';

// Keamanan: Cek sesi admin dan ID keluhan
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin' || !isset($_GET['id'])) {
    header('Location: ../login.php');
    exit;
}

$id_keluhan = $_GET['id'];
$id_admin = $_SESSION['id_user'];

// --- LOGIKA BARU: Untuk menandai keluhan sebagai selesai ---
if (isset($_GET['aksi']) && $_GET['aksi'] == 'selesai') {
    $query_selesai = "UPDATE keluhan SET status = 'selesai' WHERE id_keluhan = ?";
    $stmt_selesai = mysqli_prepare($koneksi, $query_selesai);
    mysqli_stmt_bind_param($stmt_selesai, "i", $id_keluhan);
    mysqli_stmt_execute($stmt_selesai);

    header("Location: keluhan.php?status=selesai");
    exit;
}


// Logika saat admin mengirim balasan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['isi_pesan'])) {
    $isi_pesan = mysqli_real_escape_string($koneksi, $_POST['isi_pesan']);

    // 1. Simpan pesan balasan dari admin
    $query_insert = "INSERT INTO pesan_keluhan (id_keluhan, id_pengirim, isi_pesan) VALUES (?, ?, ?)";
    $stmt_insert = mysqli_prepare($koneksi, $query_insert);
    mysqli_stmt_bind_param($stmt_insert, "iis", $id_keluhan, $id_admin, $isi_pesan);
    mysqli_stmt_execute($stmt_insert);

    // 2. Update status keluhan menjadi 'ditanggapi'
    $query_update = "UPDATE keluhan SET status = 'ditanggapi' WHERE id_keluhan = ?";
    $stmt_update = mysqli_prepare($koneksi, $query_update);
    mysqli_stmt_bind_param($stmt_update, "i", $id_keluhan);
    mysqli_stmt_execute($stmt_update);

    // Refresh halaman untuk menampilkan pesan baru
    header("Location: balas_keluhan.php?id=" . $id_keluhan);
    exit;
}

// Ambil detail keluhan utama (subjek, nama pelanggan, dll)
$query_keluhan = "SELECT keluhan.*, users.nama_lengkap FROM keluhan 
                  JOIN pelanggan ON keluhan.id_pelanggan = pelanggan.id_pelanggan
                  JOIN users ON pelanggan.id_user = users.id_user
                  WHERE keluhan.id_keluhan = ?";
$stmt_keluhan = mysqli_prepare($koneksi, $query_keluhan);
mysqli_stmt_bind_param($stmt_keluhan, "i", $id_keluhan);
mysqli_stmt_execute($stmt_keluhan);
$keluhan = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_keluhan));

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

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top"></nav>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Detail Keluhan</h2>
        <a href="keluhan.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0">Subjek: <?= htmlspecialchars($keluhan['subjek']); ?></h5>
                <small>Dari: <?= htmlspecialchars($keluhan['nama_lengkap']); ?></small>
            </div>
            <?php if ($keluhan['status'] != 'selesai'): ?>
                <a href="balas_keluhan.php?id=<?= $id_keluhan; ?>&aksi=selesai" class="btn btn-success"
                    onclick="return confirm('Apakah Anda yakin masalah ini sudah selesai?')">
                    <i class="bi bi-check-lg"></i> Tandai Selesai
                </a>
            <?php endif; ?>
        </div>
        <div class="card-body" style="max-height: 400px; overflow-y: auto;">
            <?php while ($pesan = mysqli_fetch_assoc($riwayat_pesan)): ?>
                <?php if ($pesan['role'] == 'admin'): ?>
                    <div class="d-flex justify-content-end mb-3">
                        <div class="bg-primary text-white p-3 rounded" style="max-width: 70%;">
                            <p class="mb-1"><?= nl2br(htmlspecialchars($pesan['isi_pesan'])); ?></p>
                            <small
                                class="d-block text-light text-end"><?= date('d M Y, H:i', strtotime($pesan['tanggal_kirim'])); ?></small>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="d-flex justify-content-start mb-3">
                        <div class="bg-light p-3 rounded" style="max-width: 70%;">
                            <p class="mb-1"><?= nl2br(htmlspecialchars($pesan['isi_pesan'])); ?></p>
                            <?php if ($pesan['gambar_bukti']): ?>
                                <a href="../uploads/bukti_keluhan/<?= $pesan['gambar_bukti']; ?>" target="_blank">Lihat Bukti</a>
                            <?php endif; ?>
                            <small
                                class="d-block text-muted"><?= date('d M Y, H:i', strtotime($pesan['tanggal_kirim'])); ?></small>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endwhile; ?>
        </div>
        <?php if ($keluhan['status'] != 'selesai'): ?>
            <div class="card-footer">
                <form action="balas_keluhan.php?id=<?= $id_keluhan; ?>" method="POST">
                    <div class="input-group">
                        <textarea name="isi_pesan" class="form-control" placeholder="Ketik balasan Anda..."
                            required></textarea>
                        <button class="btn btn-primary" type="submit"><i class="bi bi-send-fill"></i> Kirim</button>
                    </div>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require '../includes/footer.php'; ?>