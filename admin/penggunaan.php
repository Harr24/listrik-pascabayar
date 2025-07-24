<?php
session_start();
require '../config/database.php';

// Keamanan: Cek sesi admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Ambil daftar semua pelanggan untuk dropdown
$query_pelanggan = "SELECT pelanggan.*, users.nama_lengkap 
                    FROM pelanggan 
                    JOIN users ON pelanggan.id_user = users.id_user 
                    ORDER BY users.nama_lengkap ASC";
$pelanggan_list = mysqli_query($koneksi, $query_pelanggan);

// Logika saat form disubmit (tetap sama)
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id_pelanggan = $_POST['id_pelanggan'];
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    $meter_awal = $_POST['meter_awal'];
    $meter_akhir = $_POST['meter_akhir'];

    // Simpan data penggunaan ke tabel `penggunaan`
    $query_insert_penggunaan = "INSERT INTO penggunaan (id_pelanggan, bulan, tahun, meter_awal, meter_akhir) 
                                VALUES ('$id_pelanggan', '$bulan', '$tahun', '$meter_awal', '$meter_akhir')";

    if (mysqli_query($koneksi, $query_insert_penggunaan)) {
        $id_penggunaan_baru = mysqli_insert_id($koneksi);
        $q_tarif = mysqli_query($koneksi, "SELECT tarif.* FROM pelanggan JOIN tarif ON pelanggan.id_tarif = tarif.id_tarif WHERE id_pelanggan = '$id_pelanggan'");
        $data_tarif = mysqli_fetch_assoc($q_tarif);
        $tarif_per_kwh = $data_tarif['tarif_per_kwh'];
        $jumlah_meter = $meter_akhir - $meter_awal;
        $total_bayar = $jumlah_meter * $tarif_per_kwh;

        $query_insert_tagihan = "INSERT INTO tagihan (id_penggunaan, jumlah_meter, total_bayar, status)
                                 VALUES ('$id_penggunaan_baru', '$jumlah_meter', '$total_bayar', 'belum_lunas')";

        if (mysqli_query($koneksi, $query_insert_tagihan)) {
            header('Location: penggunaan.php?status=sukses');
            exit;
        } else {
            $error = "Gagal membuat tagihan.";
        }
    } else {
        $error = "Gagal menyimpan data penggunaan.";
    }
}


require '../includes/header.php';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">⚡ ADMIN PANEL</a>
    </div>
</nav>

<div class="container mt-4">
    <h2>Input Penggunaan & Generate Tagihan</h2>
    <hr>

    <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
        <div class="alert alert-success">Tagihan berhasil dibuat!</div>
    <?php endif; ?>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>

    <form action="penggunaan.php" method="POST">
        <div class="mb-3">
            <label for="id_pelanggan" class="form-label">Pilih Pelanggan</label>
            <select class="form-select" id="id_pelanggan" name="id_pelanggan" onchange="getMeterAwal()" required>
                <option value="" disabled selected>-- Pilih salah satu --</option>
                <?php while ($p = mysqli_fetch_assoc($pelanggan_list)): ?>
                    <option value="<?= $p['id_pelanggan']; ?>">
                        <?= htmlspecialchars($p['nama_lengkap']); ?> (No. Meter:
                        <?= htmlspecialchars($p['nomor_meter']); ?>)
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="bulan" class="form-label">Bulan</label>
                <select name="bulan" id="bulan" class="form-select" required>
                    <?php for ($i = 1; $i <= 12; $i++): ?>
                        <option value="<?= $i; ?>" <?= date('n') == $i ? 'selected' : '' ?>>
                            <?= date("F", mktime(0, 0, 0, $i, 10)); ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="tahun" class="form-label">Tahun</label>
                <input type="number" name="tahun" id="tahun" class="form-control" value="<?= date('Y'); ?>" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="meter_awal" class="form-label">Meter Awal (kWh)</label>
                <input type="number" step="0.01" name="meter_awal" id="meter_awal" class="form-control" required
                    readonly>
            </div>
            <div class="col-md-6 mb-3">
                <label for="meter_akhir" class="form-label">Meter Akhir (kWh)</label>
                <input type="number" step="0.01" name="meter_akhir" id="meter_akhir" class="form-control" required>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Simpan & Buat Tagihan</button>
        <a href="index.php" class="btn btn-secondary">Kembali ke Dashboard</a>
    </form>
</div>

<script>
    function getMeterAwal() {
        // Ambil ID pelanggan yang dipilih
        let idPelanggan = document.getElementById('id_pelanggan').value;

        // Jika tidak ada pelanggan yang dipilih, jangan lakukan apa-apa
        if (!idPelanggan) return;

        // Kirim permintaan ke server
        fetch('ambil_meter_terakhir.php?id_pelanggan=' + idPelanggan)
            .then(response => response.json()) // Ubah respons menjadi format JSON
            .then(data => {
                // Masukkan data meter_awal ke dalam input form
                document.getElementById('meter_awal').value = data.meter_awal;
            })
            .catch(error => console.error('Error:', error));
    }
</script>

<?php require '../includes/footer.php'; ?>