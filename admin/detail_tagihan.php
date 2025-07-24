<?php
session_start();
require '../config/database.php';

// Keamanan: Cek sesi admin dan ID tagihan
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin' || !isset($_GET['id'])) {
    header('Location: login.php');
    exit;
}

$id_tagihan = $_GET['id'];

// Query untuk mengambil data tagihan yang sangat detail
$query = "SELECT 
            tagihan.*,
            penggunaan.*,
            pelanggan.nomor_meter,
            users.nama_lengkap,
            tarif.golongan_tarif,
            tarif.daya,
            tarif.tarif_per_kwh,
            pembayaran.tanggal_bayar,
            pembayaran.biaya_admin,
            pembayaran.total_akhir,
            pembayaran.bukti_bayar
          FROM tagihan
          JOIN penggunaan ON tagihan.id_penggunaan = penggunaan.id_penggunaan
          JOIN pelanggan ON penggunaan.id_pelanggan = pelanggan.id_pelanggan
          JOIN users ON pelanggan.id_user = users.id_user
          JOIN tarif ON pelanggan.id_tarif = tarif.id_tarif
          LEFT JOIN pembayaran ON tagihan.id_tagihan = pembayaran.id_tagihan
          WHERE tagihan.id_tagihan = ?";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "i", $id_tagihan);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$detail = mysqli_fetch_assoc($result);

if (!$detail) {
    header('Location: tagihan.php?status=notfound');
    exit;
}

require '../includes/header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<style>
    /* CSS khusus untuk menyembunyikan tombol saat print */
    @media print {
        .no-print {
            display: none !important;
        }
    }
</style>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top no-print">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">⚡ ADMIN PANEL</a>
    </div>
</nav>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3 no-print">
        <h2 class="mb-0">Detail Tagihan</h2>
        <div>
            <a href="tagihan.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
            <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer-fill"></i> Cetak</button>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-light">
            <div class="d-flex justify-content-between">
                <div>
                    <h5 class="mb-0">Tagihan #<?= htmlspecialchars($detail['id_tagihan']); ?></h5>
                    <small class="text-muted">Periode:
                        <?= date("F Y", mktime(0, 0, 0, $detail['bulan'], 1, $detail['tahun'])); ?></small>
                </div>
                <div>
                    <?php
                    if ($detail['status'] == 'belum_lunas') {
                        echo '<span class="badge bg-danger fs-6">Belum Lunas</span>';
                    } elseif ($detail['status'] == 'diproses') {
                        echo '<span class="badge bg-warning text-dark fs-6">Diproses</span>';
                    } else {
                        echo '<span class="badge bg-success fs-6">Lunas</span>';
                    }
                    ?>
                </div>
            </div>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-6">
                    <h6 class="text-muted">PELANGGAN</h6>
                    <ul class="list-unstyled">
                        <li><strong>Nama:</strong> <?= htmlspecialchars($detail['nama_lengkap']); ?></li>
                        <li><strong>Nomor Meter:</strong> <?= htmlspecialchars($detail['nomor_meter']); ?></li>
                        <li><strong>Golongan:</strong> <?= htmlspecialchars($detail['golongan_tarif']); ?> /
                            <?= number_format($detail['daya']); ?> VA</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">PENGGUNAAN</h6>
                    <ul class="list-unstyled">
                        <li><strong>Meter Awal:</strong> <?= htmlspecialchars($detail['meter_awal']); ?> kWh</li>
                        <li><strong>Meter Akhir:</strong> <?= htmlspecialchars($detail['meter_akhir']); ?> kWh</li>
                        <li><strong>Total Pemakaian:</strong> <?= htmlspecialchars($detail['jumlah_meter']); ?> kWh</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6 class="text-muted">RINCIAN TAGIHAN</h6>
                    <ul class="list-unstyled">
                        <li><strong>Tarif / kWh:</strong> Rp
                            <?= number_format($detail['tarif_per_kwh'], 2, ',', '.'); ?></li>
                        <li><strong>Total Tagihan:</strong> <span class="fw-bold fs-5">Rp
                                <?= number_format($detail['total_bayar'], 2, ',', '.'); ?></span></li>
                    </ul>
                </div>
                <?php if ($detail['status'] != 'belum_lunas' && $detail['bukti_bayar']): ?>
                    <div class="col-md-6">
                        <h6 class="text-muted">PEMBAYARAN</h6>
                        <ul class="list-unstyled">
                            <li><strong>Tanggal Bayar:</strong>
                                <?= date('d F Y, H:i', strtotime($detail['tanggal_bayar'])); ?></li>
                            <li><strong>Total Transfer:</strong> Rp
                                <?= number_format($detail['total_akhir'], 2, ',', '.'); ?></li>
                            <li><strong>Bukti:</strong> <a href="../uploads/bukti_pembayaran/<?= $detail['bukti_bayar']; ?>"
                                    target="_blank">Lihat Bukti Pembayaran</a></li>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>