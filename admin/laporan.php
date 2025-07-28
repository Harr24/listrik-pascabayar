<?php
session_start();
require '../config/database.php';

// Keamanan: Cek sesi admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php?status=ilegal');
    exit;
}

// -- LOGIKA FILTER --
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('n');
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$filter_daya = isset($_GET['daya']) ? $_GET['daya'] : '';

$where_conditions = [];
$params = [];
$types = "";

$where_conditions[] = "penggunaan.bulan = ?";
$params[] = $filter_bulan;
$types .= "i";
$where_conditions[] = "penggunaan.tahun = ?";
$params[] = $filter_tahun;
$types .= "i";

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
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">⚡ ADMIN PANEL</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> Halo, <?= htmlspecialchars($_SESSION['nama_lengkap']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="profil_admin.php">Ubah Profil</a></li>
                        <li><a class="dropdown-item" href="ubah_password.php">Ubah Password</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Rekapitulasi Tagihan</h2>
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>
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
                        <?php for ($i = 1; $i <= 12; $i++): ?>
                            <option value="<?= $i; ?>" <?= ($filter_bulan == $i) ? 'selected' : ''; ?>>
                                <?= date("F", mktime(0, 0, 0, $i, 10)); ?></option>
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
                        <?php while ($row_daya = mysqli_fetch_assoc($result_daya)): ?>
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
            <a href="export_excel.php?bulan=<?= $filter_bulan; ?>&tahun=<?= $filter_tahun; ?>&daya=<?= $filter_daya; ?>"
                class="btn btn-success">
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
                        <?php
                        // 1. Inisialisasi variabel untuk rekapitulasi
                        $rekap_per_daya = [];
                        $total_pemasukan = 0;
                        $rows_data = []; // Tampung data untuk ditampilkan
                        
                        while ($row = mysqli_fetch_assoc($result)) {
                            $rows_data[] = $row; // Simpan data ke array
                            // 2. Lakukan kalkulasi HANYA untuk tagihan yang lunas
                            if ($row['status'] == 'lunas') {
                                $daya = $row['daya'];
                                if (!isset($rekap_per_daya[$daya])) {
                                    $rekap_per_daya[$daya] = 0;
                                }
                                $rekap_per_daya[$daya] += $row['total_bayar'];
                                $total_pemasukan += $row['total_bayar'];
                            }
                        }

                        if (!empty($rows_data)):
                            $no = 1;
                            foreach ($rows_data as $row):
                                ?>
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
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="5" class="text-center">Data tidak ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <hr class="mt-4">
            <div class="row justify-content-end">
                <div class="col-md-5">
                    <h4><i class="bi bi-calculator-fill"></i> Rekap Pemasukan</h4>
                    <ul class="list-group">
                        <?php ksort($rekap_per_daya); // Urutkan berdasarkan daya ?>
                        <?php foreach ($rekap_per_daya as $daya => $total): ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                Pemasukan <?= number_format($daya); ?> VA
                                <span>Rp <?= number_format($total, 0, ',', '.'); ?></span>
                            </li>
                        <?php endforeach; ?>

                        <li class="list-group-item d-flex justify-content-between align-items-center active">
                            <strong class="fs-5">TOTAL PEMASUKAN</strong>
                            <strong class="fs-5">Rp <?= number_format($total_pemasukan, 0, ',', '.'); ?></strong>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>