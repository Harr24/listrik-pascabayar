<?php
session_start();
require '../config/database.php';

// Keamanan: Cek sesi admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php?status=ilegal');
    exit;
}

// Logika saat form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $golongan_tarif = mysqli_real_escape_string($koneksi, $_POST['golongan_tarif']);
    $daya = mysqli_real_escape_string($koneksi, $_POST['daya']);
    $tarif_per_kwh = mysqli_real_escape_string($koneksi, $_POST['tarif_per_kwh']);

    $query = "INSERT INTO tarif (golongan_tarif, daya, tarif_per_kwh) VALUES ('$golongan_tarif', '$daya', '$tarif_per_kwh')";

    if (mysqli_query($koneksi, $query)) {
        header('Location: tarif.php?status=sukses_tambah');
        exit;
    } else {
        $error = "Gagal menambahkan data tarif.";
    }
}

require '../includes/header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">⚡ ADMIN PANEL</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> Halo, <?= htmlspecialchars($_SESSION['nama_lengkap']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="profil_admin.php">Ubah Profil</a></li>
                        <li><a class="dropdown-item" href="ubah_password.php">Ubah Password</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Golongan Tarif Baru</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <form action="tambah_tarif.php" method="POST">
                        <div class="mb-3">
                            <label for="golongan_tarif" class="form-label">Golongan Tarif</label>
                            <input type="text" class="form-control" id="golongan_tarif" name="golongan_tarif"
                                placeholder="Contoh: R-1/TR" required>
                            <div class="form-text">Kode resmi dari PLN untuk klasifikasi pelanggan.</div>
                        </div>
                        <div class="mb-3">
                            <label for="daya" class="form-label">Daya (VA)</label>
                            <input type="number" class="form-control" id="daya" name="daya" placeholder="Contoh: 900"
                                required>
                            <div class="form-text">Batas daya listrik yang terpasang, dalam satuan VA.</div>
                        </div>
                        <div class="mb-3">
                            <label for="tarif_per_kwh" class="form-label">Tarif per kWh (Rp)</label>
                            <input type="number" step="0.01" class="form-control" id="tarif_per_kwh"
                                name="tarif_per_kwh" placeholder="Contoh: 1352.00" required>
                            <div class="form-text">Harga listrik untuk setiap 1 kWh pemakaian. Gunakan titik (.) sebagai
                                pemisah desimal.</div>
                        </div>
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save-fill"></i>
                                Simpan</button>
                            <a href="tarif.php" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>