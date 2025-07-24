<?php
session_start();
require '../config/database.php';

// Keamanan: Cek sesi pelanggan dan ID tagihan
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pelanggan' || !isset($_GET['id'])) {
    header('Location: ../login.php');
    exit;
}

$id_tagihan = $_GET['id'];
$id_user = $_SESSION['id_user'];

// Query untuk memastikan tagihan ini milik user yang sedang login
$query = "SELECT tagihan.*, penggunaan.bulan, penggunaan.tahun, pelanggan.id_pelanggan
          FROM tagihan
          JOIN penggunaan ON tagihan.id_penggunaan = penggunaan.id_penggunaan
          JOIN pelanggan ON penggunaan.id_pelanggan = pelanggan.id_pelanggan
          WHERE tagihan.id_tagihan = '$id_tagihan' AND pelanggan.id_user = '$id_user'";
$result = mysqli_query($koneksi, $query);
$tagihan = mysqli_fetch_assoc($result);

if (!$tagihan || $tagihan['status'] !== 'belum_lunas') {
    header('Location: index.php?status=tagihan_tidak_valid');
    exit;
}

// Logika saat form pembayaran disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $biaya_admin = 2500;
    $total_akhir = $tagihan['total_bayar'] + $biaya_admin;

    if (isset($_FILES['bukti_bayar']) && $_FILES['bukti_bayar']['error'] == 0) {
        $bukti_bayar = $_FILES['bukti_bayar'];
        $nama_bukti = uniqid() . '-' . basename($bukti_bayar['name']);
        $path_tujuan = dirname(__DIR__) . '/uploads/bukti_pembayaran/' . $nama_bukti;

        if (move_uploaded_file($bukti_bayar['tmp_name'], $path_tujuan)) {
            $tanggal_bayar = date('Y-m-d H:i:s');
            $query_pembayaran = "INSERT INTO pembayaran (id_tagihan, tanggal_bayar, biaya_admin, total_akhir, bukti_bayar)
                                 VALUES ('$id_tagihan', '$tanggal_bayar', '$biaya_admin', '$total_akhir', '$nama_bukti')";

            if (mysqli_query($koneksi, $query_pembayaran)) {
                $query_update = "UPDATE tagihan SET status = 'diproses' WHERE id_tagihan = '$id_tagihan'";
                mysqli_query($koneksi, $query_update);
                header('Location: index.php?status=sukses_bayar');
                exit;
            } else {
                $error = "Gagal menyimpan data pembayaran ke database.";
            }
        } else {
            $error = "Gagal memindahkan file yang diupload. Cek izin folder 'uploads'.";
        }
    } else {
        $error = "Gagal mengupload file. Pastikan Anda memilih file dan tidak ada error.";
    }
}

require '../includes/header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../assets/css/pelanggan_style.css">

<nav class="navbar navbar-expand-lg navbar-dark bg-info shadow-sm">
    <div class="container">
        <a class="navbar-brand" href="index.php"><i class="bi bi-lightning-charge-fill"></i> Bayar Tagihan</a>
        <a class="nav-link text-white" href="../logout.php">Logout</a>
    </div>
</nav>

<div class="container mt-5">
    <div class="row">
        <div class="col-lg-7">
            <div class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <h4 class="card-title mb-4">Konfirmasi Pembayaran</h4>
                    <p class="text-muted">Tagihan untuk periode
                        <strong><?= date("F Y", mktime(0, 0, 0, $tagihan['bulan'], 1, $tagihan['tahun'])); ?></strong>
                    </p>

                    <ul class="list-group list-group-flush mb-4">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Total Tagihan
                            <span>Rp <?= number_format($tagihan['total_bayar'], 0, ',', '.'); ?></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            Biaya Admin
                            <span>Rp 2.500</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 fw-bold fs-5">
                            Total Pembayaran
                            <span class="text-primary">Rp
                                <?= number_format($tagihan['total_bayar'] + 2500, 0, ',', '.'); ?></span>
                        </li>
                    </ul>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= $error; ?></div>
                    <?php endif; ?>

                    <form action="bayar.php?id=<?= $id_tagihan; ?>" method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="bukti_bayar" class="form-label fw-semibold">Upload Bukti Pembayaran</label>
                            <p class="text-muted small">Unggah bukti transfer Anda di sini. Format file JPG, PNG.</p>
                            <input class="form-control" type="file" id="bukti_bayar" name="bukti_bayar" required>
                        </div>
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Konfirmasi Pembayaran</button>
                            <a href="index.php" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title mb-3"><i class="bi bi-credit-card-fill me-2"></i>Informasi Rekening</h5>
                    <p class="text-muted">Silakan lakukan transfer ke salah satu rekening tujuan berikut:</p>
                    <div class="list-group payment-info">
                        <div class="list-group-item">
                            <h6 class="mb-1 fw-bold">Bank BCA</h6>
                            <p class="mb-1">Nomor Rekening: <span class="fw-bold text-primary">081326740142</span></p>
                            <small class="text-muted">a.n. PT Harrindo Daya Tama</small>
                        </div>
                        <div class="list-group-item">
                            <h6 class="mb-1 fw-bold">Bank Mandiri</h6>
                            <p class="mb-1">Nomor Rekening: <span class="fw-bold text-primary">081326740142</span></p>
                            <small class="text-muted">a.n. PT Harrindo Daya Tama</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>