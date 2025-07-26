<?php
session_start();
require '../config/database.php';
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') { header('Location: ../login.php'); exit; }

$result = mysqli_query($koneksi, "SELECT * FROM teknisi ORDER BY nama_teknisi ASC");
require '../includes/header.php';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top"></nav>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Manajemen Teknisi</h2>
        <div>
            <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
            <a href="tambah_teknisi.php" class="btn btn-success"><i class="bi bi-plus-circle"></i> Tambah Teknisi</a>
        </div>
    </div>
    <div class="card shadow-sm">
        <div class="card-header"><h5 class="mb-0"><i class="bi bi-person-badge-fill me-2"></i>Daftar Teknisi</h5></div>
        <div class="card-body">
            <table class="table table-hover">
                <thead><tr><th>No</th><th>Nama Teknisi</th><th>No. WhatsApp</th><th>Area Bertugas</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php if (mysqli_num_rows($result) > 0): $no = 1; ?>
                        <?php while($row = mysqli_fetch_assoc($result)): ?>
                            <tr>
                                <td><?= $no++; ?></td>
                                <td><?= htmlspecialchars($row['nama_teknisi']); ?></td>
                                <td><?= htmlspecialchars($row['no_wa']); ?></td>
                                <td><?= htmlspecialchars($row['area_bertugas']); ?></td>
                                <td>
                                    <a href="hapus_teknisi.php?id=<?= $row['id_teknisi']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Yakin?')"><i class="bi bi-trash-fill"></i></a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center">Belum ada data teknisi.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php require '../includes/footer.php'; ?>