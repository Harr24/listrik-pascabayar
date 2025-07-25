<?php
session_start();
require '../config/database.php';

// Keamanan: Cek sesi admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$id_user = $_SESSION['id_user'];

// Ambil data admin saat ini
$result = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user = '$id_user'");
$admin = mysqli_fetch_assoc($result);

// Logika saat form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);

    // Cek apakah username baru sudah digunakan oleh user lain
    $check_username = mysqli_query($koneksi, "SELECT id_user FROM users WHERE username = '$username' AND id_user != '$id_user'");
    if (mysqli_num_rows($check_username) > 0) {
        $error = "Username sudah digunakan oleh akun lain.";
    } else {
        // Update data di database
        $query_update = "UPDATE users SET nama_lengkap = '$nama_lengkap', username = '$username' WHERE id_user = '$id_user'";
        if (mysqli_query($koneksi, $query_update)) {

            // <-- PENTING: Perbarui juga data di session! -->
            $_SESSION['nama_lengkap'] = $nama_lengkap;
            $_SESSION['username'] = $username;
            // <-- BATAS BAGIAN PENTING -->

            $sukses = "Profil berhasil diperbarui!";
            // Ambil ulang data terbaru untuk ditampilkan di form
            $result = mysqli_query($koneksi, "SELECT * FROM users WHERE id_user = '$id_user'");
            $admin = mysqli_fetch_assoc($result);
        } else {
            $error = "Gagal memperbarui profil.";
        }
    }
}

require '../includes/header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="index.php">⚡ ADMIN PANEL</a>
    </div>
</nav>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-person-fill-gear me-2"></i>Ubah Profil</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <?php if (isset($sukses)): ?>
                        <div class="alert alert-success"><?= htmlspecialchars($sukses); ?></div>
                    <?php endif; ?>

                    <form action="profil_admin.php" method="POST">
                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                                value="<?= htmlspecialchars($admin['nama_lengkap']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username"
                                value="<?= htmlspecialchars($admin['username']); ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save-fill"></i> Simpan
                            Perubahan</button>
                        <a href="index.php" class="btn btn-secondary">Kembali ke Dashboard</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>