<?php
session_start();
require '../config/database.php';

// Keamanan: Cek sesi admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// -- LOGIKA FILTER --
// Ambil nilai filter dari URL, jika tidak ada, gunakan nilai default
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('n'); // Bulan saat ini
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y'); // Tahun saat ini
$filter_daya = isset($_GET['daya']) ? $_GET['daya'] : '';

$where_conditions = [];
$params = [];
$types = "";

// Tambahkan filter bulan dan tahun (wajib ada)
$where_conditions[] = "penggunaan.bulan = ?";
$params[] = $filter_bulan;
$types .= "i";

$where_conditions[] = "penggunaan.tahun = ?";
$params[] = $filter_tahun;
$types .= "i";

// Tambahkan filter daya jika dipilih
if (!empty($filter_daya)) {
    $where_conditions[] = "tarif.daya = ?";
    $params[] = $filter_daya;
    $types .= "i";
}

$where_clause = "WHERE " . implode(' AND ', $where_conditions);

// Query utama dengan filter
$query = "SELECT 
            tagihan.id_tagihan, tagihan.total_bayar, tagihan.status,
            penggunaan.bulan, penggunaan.tahun,
            users.nama_lengkap, tarif.daya
          FROM tagihan
          JOIN penggunaan ON tagihan.id_penggunaan = penggunaan.id_penggunaan
          JOIN pelanggan ON penggunaan.id_pelanggan = pelanggan.id_pelanggan
          JOIN users ON pelanggan.id_user = users.id_user
          JOIN tarif ON pelanggan.id_tarif = tarif.id_tarif
          $where_clause
          ORDER BY users.nama_lengkap ASC";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Ambil semua jenis daya yang ada untuk opsi filter
$query_daya = "SELECT DISTINCT daya FROM tarif ORDER BY daya ASC";
$result_daya = mysqli_query($koneksi, $query_daya);

require '../includes/header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
    </nav>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Rekapitulasi Tagihan</h2>
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-filter"></i> Filter Laporan</h5>
        </div>
        <div class="card-body">
            <form action="laporan.php" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label for="bulan" class="form-label">Bulan</label>
                    <select name="bulan" id="bulan" class="form-select">
                        <?php for($i=1; $i<=12; $i++): ?>
                            <option value="<?= $i; ?>" <?= ($filter_bulan == $i) ? 'selected' : ''; ?>><?= date("F", mktime(0, 0, 0, $i, 10)); ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="tahun" class="form-label">Tahun</label>
                    <input type="number" class="form-control" name="tahun" value="<?= $filter_tahun; ?>">
                </div>
                <div class="col-md-3">
                    <label for="daya" class="form-label">Daya</label>
                    <select name="daya" id="daya" class="form-select">
                        <option value="">Semua Daya</option>
                        <?php while($row_daya = mysqli_fetch_assoc($result_daya)): ?>
                            <option value="<?= $row_daya['daya']; ?>" <?= ($filter_daya == $row_daya['daya']) ? 'selected' : ''; ?>><?= number_format($row_daya['daya']); ?> VA</option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100"><i class="bi bi-search"></i> Tampilkan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card shadow-sm mt-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="bi bi-table me-2"></i>Hasil Laporan</h5>
            <a href="export_excel.php?bulan=<?= $filter_bulan; ?>&tahun=<?= $filter_tahun; ?>&daya=<?= $filter_daya; ?>" class="btn btn-success">
                <i class="bi bi-file-earmark-excel-fill"></i> Export ke Excel
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pelanggan</th>
                            <th>Daya</th>
                            <th>Total Tagihan</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (mysqli_num_rows($result) > 0): ?>
                            <?php $no = 1; ?>
                            <?php while($row = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td><?= $no++; ?></td>
                                    <td><?= htmlspecialchars($row['nama_lengkap']); ?></td>
                                    <td><span class="badge bg-secondary"><?= number_format($row['daya']); ?> VA</span></td>
                                    <td>Rp <?= number_format($row['total_bayar'], 2, ',', '.'); ?></td>
                                    <td class="text-center">
                                       <?php 
                                            if ($row['status'] == 'belum_lunas') {
                                                echo '<span class="badge bg-danger">Belum Lunas</span>';
                                            } elseif ($row['status'] == 'diproses') {
                                                echo '<span class="badge bg-warning text-dark">Diproses</span>';
                                            } else {
                                                echo '<span class="badge bg-success">Lunas</span>';
                                            }
                                        ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center">Data tidak ditemukan.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>