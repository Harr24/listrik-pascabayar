<?php
session_start();
require '../config/database.php';

// Keamanan: Cek sesi admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Query untuk mengambil daftar keluhan dari pelanggan
$query = "SELECT 
            keluhan.id_keluhan,
            keluhan.subjek,
            keluhan.status,
            keluhan.tanggal_dibuat,
            users.nama_lengkap,
            pelanggan.alamat
          FROM keluhan
          JOIN pelanggan ON keluhan.id_pelanggan = pelanggan.id_pelanggan
          JOIN users ON pelanggan.id_user = users.id_user
          ORDER BY keluhan.tanggal_dibuat DESC";

$result = mysqli_query($koneksi, $query);

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
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> Halo, <?= htmlspecialchars($_SESSION['nama_lengkap']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="profil_admin.php">Ubah Profil</a></li>
                        <li><a class="dropdown-item" href="ubah_password.php">Ubah Password</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Daftar Keluhan Pelanggan</h2>
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-chat-left-text-fill me-2"></i>Tiket Masuk</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pelanggan</th>
                            <th>Subjek Keluhan</th>
                            <th>Tanggal Masuk</th>
                            <th class="text-center">Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): $no = 1; ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td>
                                        <div class="fw-bold"><?= htmlspecialchars($row['nama_lengkap']); ?></div>
                                        <div class="small text-muted"><?= htmlspecialchars($row['alamat']); ?></div>
                                    </td>
                                    <td><?= htmlspecialchars($row['subjek']); ?></td>
                                    <td><?= date('d F Y, H:i', strtotime($row['tanggal_dibuat'])); ?></td>
                                    <td class="text-center">
                                        <?php 
                                            if ($row['status'] == 'dikirim') {
                                                echo '<span class="badge bg-danger">Baru</span>';
                                            } elseif ($row['status'] == 'ditanggapi') {
                                                echo '<span class="badge bg-warning text-dark">Ditanggapi</span>';
                                            } else {
                                                echo '<span class="badge bg-success">Selesai</span>';
                                            }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="balas_keluhan.php?id=<?= $row['id_keluhan']; ?>" class="btn btn-sm btn-primary">
                                            <i class="bi bi-eye-fill"></i> Lihat & Balas
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-center">Tidak ada keluhan masuk.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>