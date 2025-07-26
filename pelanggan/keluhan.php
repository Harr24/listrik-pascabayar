<?php
session_start();
require '../config/database.php';

// Keamanan: Cek sesi pelanggan
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pelanggan') {
    header('Location: ../login.php');
    exit;
}

$id_user = $_SESSION['id_user'];

// Ambil ID Pelanggan
$query_pelanggan = mysqli_query($koneksi, "SELECT id_pelanggan FROM pelanggan WHERE id_user = '$id_user'");
$data_pelanggan = mysqli_fetch_assoc($query_pelanggan);
$id_pelanggan = $data_pelanggan['id_pelanggan'];

// Logika saat form keluhan baru disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['subjek'])) {
    $subjek = mysqli_real_escape_string($koneksi, $_POST['subjek']);
    $isi_pesan = mysqli_real_escape_string($koneksi, $_POST['isi_pesan']);

    // 1. Buat tiket keluhan baru
    $query_keluhan = "INSERT INTO keluhan (id_pelanggan, subjek) VALUES (?, ?)";
    $stmt_keluhan = mysqli_prepare($koneksi, $query_keluhan);
    mysqli_stmt_bind_param($stmt_keluhan, "is", $id_pelanggan, $subjek);
    mysqli_stmt_execute($stmt_keluhan);
    $id_keluhan_baru = mysqli_insert_id($koneksi);

    // 2. Simpan pesan pertama
    $gambar_bukti_nama = null;
    if (isset($_FILES['gambar_bukti']) && $_FILES['gambar_bukti']['error'] == 0) {
        $gambar = $_FILES['gambar_bukti'];
        $gambar_bukti_nama = uniqid() . '-' . basename($gambar['name']);
        // Pastikan folder ini ada: uploads/bukti_keluhan/
        $path_tujuan = dirname(__DIR__) . '/uploads/bukti_keluhan/' . $gambar_bukti_nama;
        move_uploaded_file($gambar['tmp_name'], $path_tujuan);
    }

    $query_pesan = "INSERT INTO pesan_keluhan (id_keluhan, id_pengirim, isi_pesan, gambar_bukti) VALUES (?, ?, ?, ?)";
    $stmt_pesan = mysqli_prepare($koneksi, $query_pesan);
    mysqli_stmt_bind_param($stmt_pesan, "iiss", $id_keluhan_baru, $id_user, $isi_pesan, $gambar_bukti_nama);
    mysqli_stmt_execute($stmt_pesan);

    header("Location: keluhan.php?status=sukses");
    exit;
}

// Ambil riwayat keluhan milik pelanggan ini
$query_riwayat = "SELECT * FROM keluhan WHERE id_pelanggan = ? ORDER BY tanggal_dibuat DESC";
$stmt_riwayat = mysqli_prepare($koneksi, $query_riwayat);
mysqli_stmt_bind_param($stmt_riwayat, "i", $id_pelanggan);
mysqli_stmt_execute($stmt_riwayat);
$riwayat_keluhan = mysqli_stmt_get_result($stmt_riwayat);

require '../includes/header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<nav class="navbar navbar-expand-lg navbar-dark bg-info shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">⚡ DASHBOARD</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavPelanggan">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavPelanggan">
            <div class="navbar-nav">
                <a class="nav-link text-white active" href="keluhan.php">Buat Keluhan</a>
            </div>
            <div class="navbar-nav ms-auto d-flex align-items-center">
                <span class="nav-link text-white d-none d-lg-block">Halo, <?= htmlspecialchars($_SESSION['nama_lengkap']); ?></span>
                <a class="nav-link text-white" href="../logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>
    </div>
</nav>


<div class="container mt-4">
    <div class="row">
        <div class="col-lg-4 mb-4">
            <div class="card shadow-sm">
                <div class="card-header"><h5 class="mb-0"><i class="bi bi-plus-circle-fill me-2"></i>Buat Keluhan Baru</h5></div>
                <div class="card-body">
                    <form action="keluhan.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="subjek" class="form-label">Subjek Keluhan</label>
                            <input type="text" class="form-control" name="subjek" id="subjek" required>
                        </div>
                        <div class="mb-3">
                            <label for="isi_pesan" class="form-label">Isi Keluhan</label>
                            <textarea class="form-control" name="isi_pesan" id="isi_pesan" rows="4" required></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="gambar_bukti" class="form-label">Lampirkan Bukti (Opsional)</label>
                            <input type="file" class="form-control" name="gambar_bukti" id="gambar_bukti">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Kirim Keluhan</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header"><h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Riwayat Keluhan Anda</h5></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Subjek</th>
                                    <th>Tanggal</th>
                                    <th class="text-center">Status</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (mysqli_num_rows($riwayat_keluhan) > 0): ?>
                                    <?php while($row = mysqli_fetch_assoc($riwayat_keluhan)): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($row['subjek']); ?></td>
                                            <td><?= date('d M Y', strtotime($row['tanggal_dibuat'])); ?></td>
                                            <td class="text-center">
                                                <?php 
                                                    if ($row['status'] == 'dikirim') {
                                                        echo '<span class="badge bg-danger">Terkirim</span>';
                                                    } elseif ($row['status'] == 'ditanggapi') {
                                                        echo '<span class="badge bg-warning text-dark">Ditanggapi</span>';
                                                    } else {
                                                        echo '<span class="badge bg-success">Selesai</span>';
                                                    }
                                                ?>
                                            </td>
                                            <td>
                                                <a href="detail_keluhan.php?id=<?= $row['id_keluhan']; ?>" class="btn btn-sm btn-info">Detail</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="4" class="text-center text-muted">Anda belum pernah mengirim keluhan.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>