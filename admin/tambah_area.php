<?php
session_start();
require '../config/database.php';

// Keamanan: Cek sesi admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Logika saat form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_area = mysqli_real_escape_string($koneksi, $_POST['nama_area']);

    // Simpan data baru ke tabel area_layanan
    $query = "INSERT INTO area_layanan (nama_area) VALUES (?)";
    $stmt = mysqli_prepare($koneksi, $query);
    mysqli_stmt_bind_param($stmt, "s", $nama_area);

    if (mysqli_stmt_execute($stmt)) {
        header('Location: area_layanan.php?status=sukses_tambah');
        exit;
    } else {
        $error = "Gagal menambahkan data area.";
    }
}

require '../includes/header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
</nav>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Area Layanan Baru</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <form action="tambah_area.php" method="POST">
                        <div class="mb-3">
                            <label for="nama_area" class="form-label">Nama Area Layanan</label>
                            <input type="text" name="nama_area" id="nama_area" class="form-control"
                                placeholder="Contoh: Perumahan Bukit Indah" required>
                            <div class="form-text">Masukkan nama perumahan, kelurahan, atau area spesifik yang
                                dijangkau.</div>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save-fill"></i> Simpan</button>
                        <a href="area_layanan.php" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>