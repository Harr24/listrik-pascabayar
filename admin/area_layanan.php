<?php
session_start();
require '../config/database.php';
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$result = mysqli_query($koneksi, "SELECT * FROM area_layanan ORDER BY nama_area ASC");
require '../includes/header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../assets/css/admin_style.css">

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
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Manajemen Area Layanan</h2>
        <div>
            <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
            <a href="tambah_area.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Tambah Area Baru</a>
        </div>
    </div>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'gagal_hapus'): ?>
        <div class="alert alert-danger">Gagal menghapus area karena masih ada pelanggan yang terdaftar di area tersebut.
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-geo-alt-fill me-2"></i>Daftar Area Tercakup</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Area Layanan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0):
                            $no = 1; ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['nama_area']); ?></td>
                                    <td>
                                        <a href="hapus_area.php?id=<?= $row['id_area']; ?>" class="btn btn-sm btn-danger"
                                            onclick="return confirm('Yakin ingin menghapus area ini?')"><i
                                                class="bi bi-trash-fill"></i> Hapus</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="3" class="text-center">Belum ada data area layanan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php require '../includes/footer.php'; ?>