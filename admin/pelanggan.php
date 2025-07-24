<?php
session_start();
require '../config/database.php';

// Keamanan: Cek apakah pengguna sudah login dan rolenya adalah admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php?status=ilegal');
    exit;
}

// Query untuk mengambil data pelanggan beserta data terkait dari tabel lain
$query = "SELECT 
            pelanggan.id_pelanggan, 
            pelanggan.nomor_meter, 
            pelanggan.alamat,
            users.nama_lengkap,
            users.username,
            tarif.golongan_tarif,
            tarif.daya
          FROM pelanggan
          JOIN users ON pelanggan.id_user = users.id_user
          JOIN tarif ON pelanggan.id_tarif = tarif.id_tarif
          ORDER BY users.nama_lengkap ASC";

$result = mysqli_query($koneksi, $query);

// Panggil header
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
    <?php if (isset($_GET['status'])): ?>
        <div class="mt-3">
            <?php if ($_GET['status'] == 'sukses'): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_GET['pesan']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php elseif ($_GET['status'] == 'error'): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?= htmlspecialchars($_GET['pesan']); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Manajemen Pelanggan</h2>
        <div>
            <a href="index.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Kembali ke Dashboard
            </a>
            <a href="tambah_pelanggan.php" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> Tambah Pelanggan Baru
            </a>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Daftar Pelanggan Terdaftar</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Lengkap & Alamat</th>
                            <th>Username</th>
                            <th>Nomor Meter</th>
                            <th>Golongan Tarif</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php $no = 1; ?>
                            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($row['nama_lengkap']); ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($row['alamat']); ?></div>
                                    </td>
                                    <td><?= htmlspecialchars($row['username']); ?></td>
                                    <td><?= htmlspecialchars($row['nomor_meter']); ?></td>
                                    <td><?= htmlspecialchars($row['golongan_tarif']); ?> /
                                        <?= htmlspecialchars($row['daya']); ?> VA
                                    </td>
                                    <td>
                                        <a href="edit_pelanggan.php?id=<?= $row['id_pelanggan']; ?>"
                                            class="btn btn-sm btn-warning">
                                            <i class="bi bi-pencil-fill"></i> Edit
                                        </a>
                                        <a href="hapus_pelanggan.php?id=<?= $row['id_pelanggan']; ?>"
                                            class="btn btn-sm btn-danger"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus pelanggan <?= htmlspecialchars($row['nama_lengkap']); ?>? Tindakan ini tidak dapat dibatalkan!');">
                                            <i class="bi bi-trash-fill"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center">Belum ada data pelanggan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
// Panggil footer
require '../includes/footer.php';
?>