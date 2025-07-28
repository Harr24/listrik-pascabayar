<?php
session_start();
require '../config/database.php';

// Keamanan: Cek sesi admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

// Ambil data tarif dan area untuk dropdown
$tariffs_query = mysqli_query($koneksi, "SELECT * FROM tarif ORDER BY daya ASC");
$areas_query = mysqli_query($koneksi, "SELECT * FROM area_layanan ORDER BY nama_area ASC");

// Logika saat form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = $_POST['password'];
    $nomor_meter = mysqli_real_escape_string($koneksi, $_POST['nomor_meter']);
    $id_tarif = mysqli_real_escape_string($koneksi, $_POST['id_tarif']);
    $id_area = mysqli_real_escape_string($koneksi, $_POST['id_area']); // Ambil id_area
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $check_user = mysqli_query($koneksi, "SELECT username FROM users WHERE username = '$username'");
    if (mysqli_num_rows($check_user) > 0) {
        $error = "Username sudah ada, silakan gunakan yang lain.";
    } else {
        $query_user = "INSERT INTO users (username, password, nama_lengkap, role) VALUES ('$username', '$hashed_password', '$nama_lengkap', 'pelanggan')";
        if (mysqli_query($koneksi, $query_user)) {
            $id_user_baru = mysqli_insert_id($koneksi);

            // Tambahkan id_area ke query insert
            $query_pelanggan = "INSERT INTO pelanggan (id_user, nomor_meter, id_tarif, id_area, alamat) VALUES ('$id_user_baru', '$nomor_meter', '$id_tarif', '$id_area', '$alamat')";
            if (mysqli_query($koneksi, $query_pelanggan)) {
                header("Location: pelanggan.php?status=sukses_tambah");
                exit;
            } else {
                $error = "Gagal menyimpan data pelanggan.";
            }
        } else {
            $error = "Gagal membuat akun pengguna.";
        }
    }
}

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
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i>Tambah Pelanggan Baru</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <form action="tambah_pelanggan.php" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3">Informasi Akun</h5>
                                <div class="mb-3">
                                    <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username" required>
                                    <div class="form-text">Digunakan oleh pelanggan untuk login.</div>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <div class="form-text">Password default untuk pelanggan.</div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="mb-3">Informasi Pelanggan</h5>
                                <div class="mb-3">
                                    <label for="nomor_meter" class="form-label">Nomor Meter</label>
                                    <input type="text" class="form-control" id="nomor_meter" name="nomor_meter"
                                        required>
                                </div>
                                <div class="mb-3">
                                    <label for="id_tarif" class="form-label">Golongan Tarif</label>
                                    <select class="form-select" id="id_tarif" name="id_tarif" required>
                                        <option value="" disabled selected>-- Pilih Golongan Tarif --</option>
                                        <?php while ($tarif = mysqli_fetch_assoc($tariffs_query)): ?>
                                            <option value="<?= $tarif['id_tarif']; ?>">
                                                <?= htmlspecialchars($tarif['golongan_tarif']); ?> -
                                                <?= htmlspecialchars($tarif['daya']); ?> VA
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="id_area" class="form-label">Area Layanan</label>
                                    <select class="form-select" id="id_area" name="id_area" required>
                                        <option value="" disabled selected>-- Pilih Area Layanan --</option>
                                        <?php while ($area = mysqli_fetch_assoc($areas_query)): ?>
                                            <option value="<?= $area['id_area']; ?>">
                                                <?= htmlspecialchars($area['nama_area']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat Lengkap</label>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="1"
                                        required></textarea>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save-fill"></i> Simpan
                                Pelanggan</button>
                            <a href="pelanggan.php" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>