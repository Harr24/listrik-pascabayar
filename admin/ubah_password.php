<?php
session_start();
require '../config/database.php';

// Keamanan: Cek sesi admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

$id_user = $_SESSION['id_user'];

// Logika saat form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password_lama = $_POST['password_lama'];
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    // Ambil password hash dari database
    $result = mysqli_query($koneksi, "SELECT password FROM users WHERE id_user = '$id_user'");
    $admin = mysqli_fetch_assoc($result);

    // Cek apakah password lama cocok
    if (password_verify($password_lama, $admin['password'])) {
        // Cek apakah password baru dan konfirmasinya sama
        if ($password_baru === $konfirmasi_password) {
            // Hash password baru
            $hashed_password_baru = password_hash($password_baru, PASSWORD_DEFAULT);
            // Update password di database
            $query_update = "UPDATE users SET password = '$hashed_password_baru' WHERE id_user = '$id_user'";
            if (mysqli_query($koneksi, $query_update)) {
                $sukses = "Password berhasil diubah!";
            } else {
                $error = "Gagal mengubah password.";
            }
        } else {
            $error = "Konfirmasi password baru tidak cocok.";
        }
    } else {
        $error = "Password lama yang Anda masukkan salah.";
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
    <h2>Ubah Password</h2>
    <hr>
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?= $error; ?></div>
    <?php endif; ?>
    <?php if (isset($sukses)): ?>
        <div class="alert alert-success"><?= $sukses; ?></div>
    <?php endif; ?>
    <form action="ubah_password.php" method="POST">
        <div class="mb-3">
            <label for="password_lama" class="form-label">Password Lama</label>
            <input type="password" class="form-control" id="password_lama" name="password_lama" required>
        </div>
        <div class="mb-3">
            <label for="password_baru" class="form-label">Password Baru</label>
            <input type="password" class="form-control" id="password_baru" name="password_baru" required>
        </div>
        <div class="mb-3">
            <label for="konfirmasi_password" class="form-label">Konfirmasi Password Baru</label>
            <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Ubah Password</button>
        <a href="index.php" class="btn btn-secondary">Kembali ke Dashboard</a>
    </form>
</div>

<?php require '../includes/footer.php'; ?>