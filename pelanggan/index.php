<?php
session_start();
require '../config/database.php';

// Autentikasi role pelanggan
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pelanggan') {
    header('Location: ../login.php?status=ilegal');
    exit;
}

$id_user = $_SESSION['id_user'];
$query_pelanggan = mysqli_query($koneksi, "SELECT id_pelanggan FROM pelanggan WHERE id_user = '$id_user'");
$data_pelanggan = mysqli_fetch_assoc($query_pelanggan);
$id_pelanggan = $data_pelanggan['id_pelanggan'];

// Ambil semua tagihan
$query_tagihan = "SELECT 
                    tagihan.*,
                    penggunaan.bulan,
                    penggunaan.tahun,
                    penggunaan.meter_awal,
                    penggunaan.meter_akhir
                  FROM tagihan
                  JOIN penggunaan ON tagihan.id_penggunaan = penggunaan.id_penggunaan
                  WHERE penggunaan.id_pelanggan = '$id_pelanggan'
                  ORDER BY penggunaan.tahun DESC, penggunaan.bulan DESC";
$result_tagihan = mysqli_query($koneksi, $query_tagihan);

// Ambil tagihan terbaru yang belum lunas
$query_terbaru = "SELECT * FROM ($query_tagihan) as semua_tagihan WHERE status = 'belum_lunas' LIMIT 1";
$result_terbaru = mysqli_query($koneksi, $query_terbaru);
$tagihan_terbaru = mysqli_fetch_assoc($result_terbaru);

require '../includes/header.php';
?>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../assets/css/pelanggan_style.css">

<nav class="navbar navbar-expand-lg navbar-dark bg-info shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="index.php">⚡ DASHBOARD</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavPelanggan">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavPelanggan">
            <div class="navbar-nav">
                <a class="nav-link text-white" href="keluhan.php">Buat Keluhan</a>
            </div>
            <div class="navbar-nav ms-auto d-flex align-items-center">
                <span class="nav-link text-white d-none d-lg-block">Halo,
                    <?= htmlspecialchars($_SESSION['nama_lengkap']); ?></span>
                <a class="nav-link text-white" href="../logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
            </div>
        </div>
    </div>
</nav>


<div class="container mt-4">
    <div class="alert alert-primary shadow-sm">
        Selamat datang kembali, <strong><?= htmlspecialchars($_SESSION['nama_lengkap']); ?></strong>! Berikut ringkasan
        tagihan Anda.
    </div>

    <?php if ($tagihan_terbaru): ?>
        <div class="card card-tagihan-utama shadow-sm mb-4">
            <div class="card-header border-0 bg-transparent">
                <h5 class="card-title"><i class="bi bi-receipt"></i> Tagihan Terbaru Anda</h5>
            </div>
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4 mb-3 mb-md-0">
                        <h6>Periode</h6>
                        <h3><?= date("F Y", mktime(0, 0, 0, $tagihan_terbaru['bulan'], 1, $tagihan_terbaru['tahun'])); ?>
                        </h3>
                    </div>
                    <div class="col-md-4 mb-3 mb-md-0">
                        <h6>Total Bayar</h6>
                        <h3 class="fw-bold">Rp <?= number_format($tagihan_terbaru['total_bayar'] + 2500, 0, ',', '.'); ?>
                        </h3>
                        <small class="text-muted">*Sudah termasuk biaya admin</small>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="bayar.php?id=<?= $tagihan_terbaru['id_tagihan']; ?>" class="btn btn-lg btn-success">
                            <i class="bi bi-credit-card"></i> Bayar Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <h4 class="mb-3 mt-5"><i class="bi bi-clock-history"></i> Riwayat Tagihan</h4>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Periode</th>
                    <th>Pemakaian (kWh)</th>
                    <th>Total Tagihan</th>
                    <th class="text-center">Status & Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (mysqli_num_rows($result_tagihan) > 0): ?>
                    <?php mysqli_data_seek($result_tagihan, 0); ?>
                    <?php while ($row = mysqli_fetch_assoc($result_tagihan)): ?>
                        <?php
                        $jumlah_meter = $row['meter_akhir'] - $row['meter_awal'];
                        ?>
                        <tr>
                            <td><strong><?= date("F Y", mktime(0, 0, 0, $row['bulan'], 10)) ?></strong></td>
                            <td><?= $jumlah_meter; ?> kWh</td>
                            <td>Rp <?= number_format($row['total_bayar'], 0, ',', '.'); ?></td>
                            <td class="text-center">
                                <?php
                                if ($row['status'] == 'belum_lunas') {
                                    echo '<span class="badge rounded-pill bg-danger badge-status">Belum Lunas</span><br>';
                                    echo '<a href="bayar.php?id=' . $row['id_tagihan'] . '" class="btn btn-sm btn-outline-success mt-1"><i class="bi bi-wallet2"></i> Bayar</a>';
                                } elseif ($row['status'] == 'diproses') {
                                    echo '<span class="badge rounded-pill bg-warning text-dark badge-status">Diproses</span>';
                                } else {
                                    echo '<span class="badge rounded-pill bg-success badge-status">Lunas</span>';
                                }
                                ?>
                                <br>
                                <a href="detail_tagihan.php?id=<?= $row['id_tagihan']; ?>"
                                    class="btn btn-sm btn-outline-primary mt-1" title="Lihat Detail">
                                    <i class="bi bi-file-earmark-text"></i> Detail
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="4" class="text-center">Belum ada riwayat tagihan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require '../includes/footer.php'; ?>