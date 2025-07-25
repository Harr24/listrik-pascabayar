<?php
// admin/tambah_event.php
session_start();
require '../config/database.php';
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul = mysqli_real_escape_string($koneksi, $_POST['judul']);
    $keterangan = mysqli_real_escape_string($koneksi, $_POST['keterangan']);
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $gambar = $_FILES['gambar'];
        $nama_gambar = uniqid() . '-' . basename($gambar['name']);
        $path_tujuan = dirname(__DIR__) . '/uploads/events/' . $nama_gambar;
        if (move_uploaded_file($gambar['tmp_name'], $path_tujuan)) {
            $query = "INSERT INTO events (judul, keterangan, gambar) VALUES ('$judul', '$keterangan', '$nama_gambar')";
            if (mysqli_query($koneksi, $query)) {
                header('Location: event.php?status=sukses');
                exit;
            } else {
                $error = "Gagal menyimpan ke database.";
            }
        } else {
            $error = "Gagal upload gambar.";
        }
    } else {
        $error = "Silakan pilih gambar.";
    }
}
require '../includes/header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top"></nav>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-plus-circle-fill me-2"></i>Tambah Event / Berita Baru</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div><?php endif; ?>
                    <form action="tambah_event.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-3"><label for="judul" class="form-label">Judul</label><input type="text"
                                class="form-control" id="judul" name="judul" required></div>
                        <div class="mb-3"><label for="keterangan" class="form-label">Keterangan</label><textarea
                                class="form-control" id="keterangan" name="keterangan" rows="5" required></textarea>
                        </div>
                        <div class="mb-3"><label for="gambar" class="form-label">Gambar</label><input type="file"
                                class="form-control" id="gambar" name="gambar" required></div>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save-fill"></i> Simpan
                            Event</button>
                        <a href="event.php" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require '../includes/footer.php'; ?>