<?php
session_start();
require 'config/database.php';

// Ambil data tarif dan area untuk dropdown
$tariffs_query = mysqli_query($koneksi, "SELECT * FROM tarif");
$areas_query = mysqli_query($koneksi, "SELECT * FROM area_layanan ORDER BY nama_area ASC");


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $password = mysqli_real_escape_string($koneksi, $_POST['password']);
    $nomor_meter = mysqli_real_escape_string($koneksi, $_POST['nomor_meter']);
    $id_tarif = mysqli_real_escape_string($koneksi, $_POST['id_tarif']);
    $id_area = mysqli_real_escape_string($koneksi, $_POST['id_area']); // Ambil id_area
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);

    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $check_user = mysqli_query($koneksi, "SELECT username FROM users WHERE username = '$username'");
    if (mysqli_num_rows($check_user) > 0) {
        $error = "Username sudah digunakan, silakan pilih yang lain.";
    } else {
        $query_user = "INSERT INTO users (username, password, nama_lengkap, role) VALUES ('$username', '$hashed_password', '$nama_lengkap', 'pelanggan')";
        if (mysqli_query($koneksi, $query_user)) {
            $id_user_baru = mysqli_insert_id($koneksi);
            // Tambahkan id_area ke query insert
            $query_pelanggan = "INSERT INTO pelanggan (id_user, nomor_meter, id_tarif, id_area, alamat) VALUES ('$id_user_baru', '$nomor_meter', '$id_tarif', '$id_area', '$alamat')";
            if (mysqli_query($koneksi, $query_pelanggan)) {
                header("Location: login.php?status=sukses_registrasi");
                exit();
            } else {
                $error = "Gagal menyimpan data pelanggan.";
            }
        } else {
            $error = "Gagal membuat akun pengguna.";
        }
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

    .register-wrapper {
        min-height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 20px 0;
        backdrop-filter: blur(6px);
    }

    .register-card {
        background-color: rgba(0, 0, 0, 0.75);
        padding: 40px;
        border-radius: 15px;
        width: 500px;
        box-shadow: 0 0 20px rgba(255, 255, 255, 0.08);
    }

    .register-card h3 {
        color: #00ffaa;
        text-align: center;
        margin-bottom: 30px;
    }

    .input-group-text {
        background-color: #222;
        border: none;
    }

    .form-control,
    .form-select {
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
        border: 1px solid #555;
    }

    .form-select {
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23cccccc' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
    }

    .form-control::placeholder {
        color: #ccc;
    }

    .btn-success {
        background: linear-gradient(to right, #00c853, #00e676);
        border: none;
    }

    .btn-success:hover {
        background: linear-gradient(to right, #00e676, #00c853);
    }

    .text-muted a {
        color: #ccc;
        text-decoration: none;
    }

    .text-muted a:hover {
        text-decoration: underline;
    }

    img.icon {
        width: 20px;
        height: 20px;
    }
</style>

<div class="register-wrapper">
    <div class="register-card">
        <h3>📝 Pendaftaran Pelanggan</h3>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <div class="mb-3 input-group">
                <span class="input-group-text"><img src="assets/img/icon-user.svg" alt="user" class="icon"></span>
                <input type="text" class="form-control" name="nama_lengkap" placeholder="Nama Lengkap" required>
            </div>

            <div class="mb-3 input-group">
                <span class="input-group-text"><img src="assets/img/icon-user.svg" alt="username" class="icon"></span>
                <input type="text" class="form-control" name="username" placeholder="Username" required>
            </div>

            <div class="mb-3 input-group">
                <span class="input-group-text"><img src="assets/img/icon-lock.svg" alt="password" class="icon"></span>
                <input type="password" class="form-control" name="password" placeholder="Password" required>
            </div>

            <hr class="text-light">

            <div class="mb-3 input-group">
                <span class="input-group-text"><img src="assets/img/icon-meter.png" alt="meter" class="icon"></span>
                <input type="text" class="form-control" name="nomor_meter" placeholder="Nomor Meter" required>
            </div>

            <div class="mb-3">
                <select class="form-select form-control" name="id_tarif" required>
                    <option value="" disabled selected>-- Pilih Golongan Tarif --</option>
                    <?php while ($tarif = mysqli_fetch_assoc($tariffs_query)): ?>
                        <option value="<?= $tarif['id_tarif']; ?>">
                            <?= $tarif['golongan_tarif']; ?> - <?= $tarif['daya']; ?> VA (Rp
                            <?= number_format($tarif['tarif_per_kwh']); ?>/kWh)
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <select class="form-select form-control" name="id_area" required>
                    <option value="" disabled selected>-- Pilih Area Layanan --</option>
                    <?php while ($area = mysqli_fetch_assoc($areas_query)): ?>
                        <option value="<?= $area['id_area']; ?>">
                            <?= htmlspecialchars($area['nama_area']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <textarea class="form-control" name="alamat" placeholder="Alamat lengkap..." rows="2"
                    required></textarea>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-success">Daftar Sekarang</button>
            </div>

            <div class="text-center mt-3 text-muted">
                <a href="login.php">Sudah punya akun? Login di sini</a>
            </div>
        </form>
    </div>
</div>

<?php require 'includes/footer.php'; ?>