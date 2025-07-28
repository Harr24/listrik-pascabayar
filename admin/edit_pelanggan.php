<?php
session_start();
require '../config/database.php';

// Keamanan: Cek sesi admin dan keberadaan ID
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin' || !isset($_GET['id'])) {
    header('Location: ../login.php');
    exit;
}

$id_pelanggan = $_GET['id'];

// Logika saat form disubmit untuk update
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama_lengkap = mysqli_real_escape_string($koneksi, $_POST['nama_lengkap']);
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $nomor_meter = mysqli_real_escape_string($koneksi, $_POST['nomor_meter']);
    $id_tarif = mysqli_real_escape_string($koneksi, $_POST['id_tarif']);
    $id_area = mysqli_real_escape_string($koneksi, $_POST['id_area']); // Ambil id_area
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);

    // Ambil id_user dari database untuk memastikan tidak salah
    $id_user_query = mysqli_query($koneksi, "SELECT id_user FROM pelanggan WHERE id_pelanggan='$id_pelanggan'");
    $id_user = mysqli_fetch_assoc($id_user_query)['id_user'];

    // Cek duplikasi username
    $check_username = mysqli_query($koneksi, "SELECT id_user FROM users WHERE username = '$username' AND id_user != '$id_user'");
    if (mysqli_num_rows($check_username) > 0) {
        $error = "Username sudah digunakan oleh akun lain.";
    } else {
        // 1. Update tabel users
        $query_update_user = "UPDATE users SET nama_lengkap = '$nama_lengkap', username = '$username' WHERE id_user = '$id_user'";

        // 2. Update tabel pelanggan, tambahkan id_area
        $query_update_pelanggan = "UPDATE pelanggan SET nomor_meter = '$nomor_meter', id_tarif = '$id_tarif', id_area = '$id_area', alamat = '$alamat' WHERE id_pelanggan = '$id_pelanggan'";

        if (mysqli_query($koneksi, $query_update_user) && mysqli_query($koneksi, $query_update_pelanggan)) {
            header("Location: pelanggan.php?status=sukses_edit");
            exit;
        } else {
            $error = "Gagal memperbarui data pelanggan.";
        }
    }
}

// Ambil data pelanggan yang akan di-edit
$query = "SELECT pelanggan.*, users.nama_lengkap, users.username
          FROM pelanggan
          JOIN users ON pelanggan.id_user = users.id_user
          WHERE pelanggan.id_pelanggan = '$id_pelanggan'";
$result = mysqli_query($koneksi, $query);
$pelanggan = mysqli_fetch_assoc($result);

// Ambil semua data tarif dan area untuk dropdown
$tariffs_query = mysqli_query($koneksi, "SELECT * FROM tarif ORDER BY daya ASC");
$areas_query = mysqli_query($koneksi, "SELECT * FROM area_layanan ORDER BY nama_area ASC");


require '../includes/header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top">
</nav>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Edit Data Pelanggan</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <form action="edit_pelanggan.php?id=<?= $id_pelanggan; ?>" method="POST">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="mb-3">Informasi Akun</h5>
                                <div class="mb-3">
                                    <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                                    <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap"
                                        value="<?= htmlspecialchars($pelanggan['nama_lengkap']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" class="form-control" id="username" name="username"
                                        value="<?= htmlspecialchars($pelanggan['username']); ?>" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <h5 class="mb-3">Informasi Pelanggan</h5>
                                <div class="mb-3">
                                    <label for="nomor_meter" class="form-label">Nomor Meter</label>
                                    <input type="text" class="form-control" id="nomor_meter" name="nomor_meter"
                                        value="<?= htmlspecialchars($pelanggan['nomor_meter']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="id_tarif" class="form-label">Golongan Tarif</label>
                                    <select class="form-select" id="id_tarif" name="id_tarif" required>
                                        <option value="" disabled>-- Pilih Golongan Tarif --</option>
                                        <?php while ($tarif = mysqli_fetch_assoc($tariffs_query)): ?>
                                            <option value="<?= $tarif['id_tarif']; ?>"
                                                <?= ($tarif['id_tarif'] == $pelanggan['id_tarif']) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($tarif['golongan_tarif']); ?> -
                                                <?= htmlspecialchars($tarif['daya']); ?> VA
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="id_area" class="form-label">Area Layanan</label>
                                    <select class="form-select" id="id_area" name="id_area" required>
                                        <option value="" disabled>-- Pilih Area Layanan --</option>
                                        <?php while ($area = mysqli_fetch_assoc($areas_query)): ?>
                                            <option value="<?= $area['id_area']; ?>"
                                                <?= ($area['id_area'] == $pelanggan['id_area']) ? 'selected' : ''; ?>>
                                                <?= htmlspecialchars($area['nama_area']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="alamat" class="form-label">Alamat Lengkap</label>
                                    <textarea class="form-control" id="alamat" name="alamat" rows="1"
                                        required><?= htmlspecialchars($pelanggan['alamat']); ?></textarea>
                                </div>
                            </div>
                        </div>
                        <hr>
                        <div class="mt-3">
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save-fill"></i> Simpan
                                Perubahan</button>
                            <a href="pelanggan.php" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require '../includes/footer.php'; ?>