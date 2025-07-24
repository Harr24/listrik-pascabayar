<?php
session_start();
require '../config/database.php';

// Keamanan: Cek sesi admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Query untuk mengambil semua data tagihan dari semua pelanggan
$query = "SELECT 
            tagihan.id_tagihan,
            tagihan.total_bayar,
            tagihan.status,
            penggunaan.bulan,
            penggunaan.tahun,
            users.nama_lengkap
          FROM tagihan
          JOIN penggunaan ON tagihan.id_penggunaan = penggunaan.id_penggunaan
          JOIN pelanggan ON penggunaan.id_pelanggan = pelanggan.id_pelanggan
          JOIN users ON pelanggan.id_user = users.id_user
          ORDER BY penggunaan.tahun DESC, penggunaan.bulan DESC, users.nama_lengkap ASC";

$result = mysqli_query($koneksi, $query);

require '../includes/header.php';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">⚡ ADMIN PANEL</a>
    </div>
</nav>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center">
        <h2>Manajemen Tagihan (Semua Pelanggan)</h2>
        <a href="index.php" class="btn btn-secondary">Kembali ke Dashboard</a>
    </div>
    <hr>
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama Pelanggan</th>
                    <th>Periode</th>
                    <th>Total Tagihan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result) > 0): ?>
                    <?php $no = 1; ?>
                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?= $no++; ?></td>
                            <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                            <td><?= date("F Y", mktime(0, 0, 0, $row['bulan'], 1, $row['tahun'])); ?></td>
                            <td>Rp <?= number_format($row['total_bayar'], 2, ',', '.'); ?></td>
                            <td>
                                <?php
                                if ($row['status'] == 'belum_lunas') {
                                    echo '<span class="badge bg-danger">Belum Lunas</span>';
                                } elseif ($row['status'] == 'diproses') {
                                    echo '<span class="badge bg-warning">Menunggu Konfirmasi</span>';
                                } else {
                                    echo '<span class="badge bg-success">Lunas</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <!-- LINK DETAIL DITAMBAHKAN DI SINI -->
                                <a href="detail_tagihan.php?id=<?= $row['id_tagihan']; ?>"
                                    class="btn btn-sm btn-info">Detail</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Belum ada data tagihan sama sekali.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require '../includes/footer.php'; ?>