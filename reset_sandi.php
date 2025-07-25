<?php
session_start();
require 'config/database.php';

// Cek apakah token ada di URL
if (!isset($_GET['token']) || empty($_GET['token'])) {
    die("Token reset tidak ditemukan.");
}

$token = $_GET['token'];

// Cari user berdasarkan token DAN pastikan token belum kedaluwarsa
$query = "SELECT id_user, token_expiry FROM users WHERE reset_token = ? AND token_expiry > NOW()";
$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, "s", $token);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

if (!$user) {
    die("Token tidak valid atau sudah kedaluwarsa. Silakan ajukan permintaan reset baru.");
}

$id_user = $user['id_user'];

// Logika saat form password baru disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $password_baru = $_POST['password_baru'];
    $konfirmasi_password = $_POST['konfirmasi_password'];

    if ($password_baru === $konfirmasi_password) {
        // Hash password baru
        $hashed_password = password_hash($password_baru, PASSWORD_DEFAULT);

        // Update password baru dan hapus token dari database
        $query_update = "UPDATE users SET password = ?, reset_token = NULL, token_expiry = NULL WHERE id_user = ?";
        $stmt_update = mysqli_prepare($koneksi, $query_update);
        mysqli_stmt_bind_param($stmt_update, "si", $hashed_password, $id_user);

        if (mysqli_stmt_execute($stmt_update)) {
            // Arahkan ke halaman login dengan pesan sukses
            header('Location: login.php?status=sukses_reset');
            exit;
        } else {
            $error = "Gagal memperbarui password.";
        }
    } else {
        $error = "Konfirmasi password tidak cocok.";
    }
}


require 'includes/header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="assets/css/style.css">

<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="col-md-5">
        <div class="card shadow-lg">
            <div class="card-header bg-success text-white text-center">
                <h3><i class="bi bi-shield-lock-fill"></i> Atur Password Baru</h3>
            </div>
            <div class="card-body p-4">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form action="reset_sandi.php?token=<?= htmlspecialchars($token); ?>" method="POST">
                    <div class="mb-3">
                        <label for="password_baru" class="form-label">Password Baru</label>
                        <input type="password" class="form-control" id="password_baru" name="password_baru" required>
                    </div>
                    <div class="mb-3">
                        <label for="konfirmasi_password" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" id="konfirmasi_password" name="konfirmasi_password"
                            required>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php
require 'includes/footer.php';
?>