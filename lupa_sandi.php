<?php
session_start();
require 'includes/header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="assets/css/style.css">

<div class="container vh-100 d-flex justify-content-center align-items-center">
    <div class="col-md-5">
        <div class="card shadow-lg">
            <div class="card-header bg-warning text-dark text-center">
                <h3><i class="bi bi-key-fill"></i> Lupa Password</h3>
            </div>
            <div class="card-body p-4">

                <?php if (isset($_SESSION['pesan_reset'])): ?>
                    <div class="alert alert-info">
                        <?= $_SESSION['pesan_reset']; ?>
                    </div>
                    <?php
                    // Hapus pesan setelah ditampilkan
                    unset($_SESSION['pesan_reset']);
                    ?>
                <?php endif; ?>

                <p class="text-muted">Masukkan username Anda. Kami akan membuatkan link untuk mereset password Anda.</p>
                <form action="proses_lupa_sandi.php" method="POST">
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary">Kirim Link Reset</button>
                    </div>
                </form>
                <div class="text-center mt-3">
                    <a href="login.php">Kembali ke halaman Login</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require 'includes/footer.php';
?>