<?php
session_start();
require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password_input = $_POST['password'];

    $query = "SELECT * FROM users WHERE username = '$username'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        if (password_verify($password_input, $user['password'])) {
            $_SESSION['id_user'] = $user['id_user'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
            $_SESSION['role'] = $user['role'];

            header("Location: " . ($user['role'] === 'admin' ? 'admin/index.php' : 'pelanggan/index.php'));
            exit();
        } else {
            $error = "Password salah!";
        }
    } else {
        $error = "Username tidak ditemukan!";
    }
}

require 'includes/header.php';
?>

<style>
    body {
        background: url('assets/img/login-bg.png') no-repeat center center fixed;
        background-size: cover;
        font-family: 'Segoe UI', sans-serif;
        color: #fff;
    }

    .login-wrapper {
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        backdrop-filter: blur(6px);
    }

    .login-card {
        background-color: rgba(0, 0, 0, 0.75);
        border-radius: 15px;
        padding: 40px;
        width: 400px;
        box-shadow: 0 0 15px rgba(255, 255, 255, 0.1);
    }

    .login-card h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #ff9d00;
        font-weight: 600;
    }

    .input-group-text {
        background-color: rgba(255, 255, 255, 0.08);
        border: none;
    }

    .input-group-text img {
        width: 20px;
        height: 20px;
    }

    .form-control {
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
        border: none;
    }

    .form-control::placeholder {
        color: #ccc;
    }

    .form-control:focus {
        background-color: rgba(255, 255, 255, 0.15);
        color: #fff;
    }

    .btn-primary {
        background: linear-gradient(to right, #ff512f, #dd2476);
        border: none;
    }

    .btn-primary:hover {
        background: linear-gradient(to right, #dd2476, #ff512f);
    }

    .card-footer {
        /* Menggunakan flexbox untuk menata link */
        display: flex;
        justify-content: space-between;
        margin-top: 20px;
        font-size: 0.9em;
    }

    .card-footer a {
        color: #ccc;
        text-decoration: none;
    }

    .card-footer a:hover {
        text-decoration: underline;
    }
</style>

<div class="login-wrapper">
    <div class="login-card">
        <h2>⚡ Listrik Pascabayar</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses_reset'): ?>
            <div class="alert alert-success">Password Anda berhasil diubah! Silakan login.</div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="mb-3 input-group">
                <span class="input-group-text">
                    <img src="assets/img/icon-user.svg" alt="User Icon">
                </span>
                <input type="text" name="username" id="username" class="form-control" placeholder="Masukkan username..."
                    required>
            </div>

            <div class="mb-3 input-group">
                <span class="input-group-text">
                    <img src="assets/img/icon-lock.svg" alt="Lock Icon">
                </span>
                <input type="password" name="password" id="password" class="form-control"
                    placeholder="Masukkan password..." required>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Login 🔐</button>
            </div>
        </form>

        <div class="card-footer">
            <a href="register.php">Belum punya akun? Daftar</a>
            <a href="lupa_sandi.php">Lupa Sandi?</a>
        </div>
    </div>
</div>

<?php require 'includes/footer.php'; ?>