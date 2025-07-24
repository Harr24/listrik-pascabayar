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

// Jika tagihan tidak ditemukan atau bukan milik user ini, tendang keluar
if (!$tagihan || $tagihan['status'] !== 'belum_lunas') {
    header('Location: index.php?status=tagihan_tidak_valid');
    exit;
}


// Logika saat form pembayaran disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $biaya_admin = 2500; // Contoh biaya admin
    $total_akhir = $tagihan['total_bayar'] + $biaya_admin;

    // Proses upload file
    $bukti_bayar = $_FILES['bukti_bayar'];
    $nama_bukti = uniqid() . '-' . $bukti_bayar['name'];
    $path_tujuan = '../uploads/bukti_pembayaran/' . $nama_bukti;

    if (move_uploaded_file($bukti_bayar['tmp_name'], $path_tujuan)) {
        // 1. Insert ke tabel pembayaran
        $tanggal_bayar = date('Y-m-d H:i:s');
        $query_pembayaran = "INSERT INTO pembayaran (id_tagihan, tanggal_bayar, biaya_admin, total_akhir, bukti_bayar)
                             VALUES ('$id_tagihan', '$tanggal_bayar', '$biaya_admin', '$total_akhir', '$nama_bukti')";

        if (mysqli_query($koneksi, $query_pembayaran)) {
            // 2. Update status di tabel tagihan
            $query_update = "UPDATE tagihan SET status = 'diproses' WHERE id_tagihan = '$id_tagihan'";
            mysqli_query($koneksi, $query_update);

            header('Location: index.php?status=sukses_bayar');
            exit;
        } else {
            $error = "Gagal menyimpan data pembayaran.";
        }
    } else {
        $error = "Gagal mengupload bukti pembayaran.";
    }
}


require '../includes/header.php';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-info">
    <div class="container">
        <a class="navbar-brand" href="index.php">⚡ Bayar Tagihan</a>
    </div>
</nav>

<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h3>Detail Tagihan Periode <?= date("F Y", mktime(0, 0, 0, $tagihan['bulan'], 1, $tagihan['tahun'])); ?>
            </h3>
        </div>
        <div class="card-body">
            <p><strong>Total Tagihan:</strong> Rp <?= number_format($tagihan['total_bayar'], 2, ',', '.'); ?></p>
            <p><strong>Biaya Admin:</strong> Rp 2.500,00</p>
            <hr>
            <h4><strong>Total yang Harus Dibayar: Rp
                    <?= number_format($tagihan['total_bayar'] + 2500, 2, ',', '.'); ?></strong></h4>
            <hr>
            <h5>Informasi Pembayaran</h5>
            <p>Silakan lakukan transfer ke salah satu rekening berikut:</p>
            <ul>
                <li><strong>Bank BCA:</strong> 1234567890 a.n. PT Listrik Cemerlang</li>
                <li><strong>Bank Mandiri:</strong> 0987654321 a.n. PT Listrik Cemerlang</li>
            </ul>
            <p>Setelah melakukan pembayaran, silakan unggah bukti transfer Anda di bawah ini.</p>

            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?= $error; ?></div>
            <?php endif; ?>

            <form action="bayar.php?id=<?= $id_tagihan; ?>" method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="bukti_bayar" class="form-label">Upload Bukti Pembayaran (JPG, PNG)</label>
                    <input class="form-control" type="file" id="bukti_bayar" name="bukti_bayar" required>
                </div>
                <button type="submit" class="btn btn-primary">Konfirmasi Pembayaran</button>
                <a href="index.php" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>