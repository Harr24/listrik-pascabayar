<?php
session_start();
require '../config/database.php';

// Keamanan: Cek sesi admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// -- LOGIKA FILTER --
$filter_daya = isset($_GET['daya']) ? $_GET['daya'] : '';
$where_clause = "";

if (!empty($filter_daya)) {
    // Pastikan filter adalah angka untuk keamanan
    if (is_numeric($filter_daya)) {
        $where_clause = "WHERE tarif.daya = " . intval($filter_daya);
    }
}
// -- AKHIR LOGIKA FILTER --


// Query untuk mengambil semua data tagihan dari semua pelanggan
$query = "SELECT 
            tagihan.id_tagihan,
            tagihan.total_bayar,
            tagihan.status,
            penggunaan.bulan,
            penggunaan.tahun,
            users.nama_lengkap,
            tarif.daya
          FROM tagihan
          JOIN penggunaan ON tagihan.id_penggunaan = penggunaan.id_penggunaan
          JOIN pelanggan ON penggunaan.id_pelanggan = pelanggan.id_pelanggan
          JOIN users ON pelanggan.id_user = users.id_user
          JOIN tarif ON pelanggan.id_tarif = tarif.id_tarif
          $where_clause
          ORDER BY penggunaan.tahun DESC, penggunaan.bulan DESC, users.nama_lengkap ASC";

$result = mysqli_query($koneksi, $query);

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
        <h2 class="mb-0">Manajemen Tagihan</h2>
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali ke Dashboard</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Daftar Tagihan Pelanggan</h5>
        </div>
        <div class="card-body">
            <form action="tagihan.php" method="GET" class="row g-3 align-items-center mb-4">
                <div class="col-md-4">
                    <label for="daya" class="form-label">Filter Berdasarkan Daya</label>
                    <select name="daya" id="daya" class="form-select">
                        <option value="">-- Tampilkan Semua --</option>
                        <?php while ($row_daya = mysqli_fetch_assoc($result_daya)): ?>
                            <option value="<?= $row_daya['daya']; ?>" <?= ($filter_daya == $row_daya['daya']) ? 'selected' : ''; ?>>
                                <?= number_format($row_daya['daya']); ?> VA
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-funnel-fill"></i> Filter</button>
                    <a href="tagihan.php" class="btn btn-outline-secondary ms-2">Reset</a>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pelanggan</th>
                            <th>Periode</th>
                            <th>Daya</th>
                            <th>Total Tagihan</th>
                            <th class="text-center">Status</th>
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
                                    <td>
                                        <a href="detail_tagihan.php?id=<?= $row['id_tagihan']; ?>"
                                            class="btn btn-sm btn-info"><i class="bi bi-info-circle"></i> Detail</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center">Data tagihan tidak ditemukan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>