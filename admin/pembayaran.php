<?php
session_start();
require '../config/database.php';

// Keamanan: Cek sesi admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Logika untuk konfirmasi pembayaran
if (isset($_GET['konfirmasi'])) {
    $id_pembayaran = $_GET['konfirmasi'];
    $id_tagihan = $_GET['id_tagihan'];
    $id_admin = $_SESSION['id_user'];

    // 1. Update id_admin di tabel pembayaran
    mysqli_query($koneksi, "UPDATE pembayaran SET id_admin = '$id_admin' WHERE id_pembayaran = '$id_pembayaran'");

    // 2. Update status di tabel tagihan menjadi 'lunas'
    mysqli_query($koneksi, "UPDATE tagihan SET status = 'lunas' WHERE id_tagihan = '$id_tagihan'");

    header('Location: pembayaran.php?status=sukses_konfirmasi');
    exit;
}

// Query untuk mengambil data pembayaran yang status tagihannya 'diproses'
$query = "SELECT 
            pembayaran.*, 
            tagihan.id_tagihan,
            users.nama_lengkap
          FROM pembayaran
          JOIN tagihan ON pembayaran.id_tagihan = tagihan.id_tagihan
          JOIN penggunaan ON tagihan.id_penggunaan = penggunaan.id_penggunaan
          JOIN pelanggan ON penggunaan.id_pelanggan = pelanggan.id_pelanggan
          JOIN users ON pelanggan.id_user = users.id_user
          WHERE tagihan.status = 'diproses'
          ORDER BY pembayaran.tanggal_bayar ASC";

$result = mysqli_query($koneksi, $query);

require '../includes/header.php';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">⚡ ADMIN PANEL</a>
    </div>
</nav>

<div class="container mt-4">
    <h2>Konfirmasi Pembayaran Pelanggan</h2>
    <hr>
    <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses_konfirmasi'): ?>
        <div class="alert alert-success">Pembayaran berhasil dikonfirmasi!</div>
    <?php endif; ?>

    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>No</th>
                    <th>Nama Pelanggan</th>
                    <th>Tanggal Bayar</th>
                    <th>Total Bayar</th>
                    <th>Bukti</th>
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
                            <td><?= date('d F Y, H:i', strtotime($row['tanggal_bayar'])); ?></td>
                            <td>Rp <?= number_format($row['total_akhir'], 2, ',', '.'); ?></td>
                            <td>
                                <a href="../uploads/bukti_pembayaran/<?= $row['bukti_bayar']; ?>" target="_blank"
                                    class="btn btn-sm btn-info">Lihat Bukti</a>
                            </td>
                            <td>
                                <a href="pembayaran.php?konfirmasi=<?= $row['id_pembayaran']; ?>&id_tagihan=<?= $row['id_tagihan']; ?>"
                                    class="btn btn-sm btn-success"
                                    onclick="return confirm('Konfirmasi pembayaran ini?')">Konfirmasi</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Tidak ada pembayaran yang perlu dikonfirmasi.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <a href="index.php" class="btn btn-secondary mt-3">Kembali ke Dashboard</a>
</div>

<?php require '../includes/footer.php'; ?>