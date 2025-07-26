<?php
session_start();
require '../config/database.php';
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nama = mysqli_real_escape_string($koneksi, $_POST['nama_teknisi']);
    $no_wa = mysqli_real_escape_string($koneksi, $_POST['no_wa']);
    $area = mysqli_real_escape_string($koneksi, $_POST['area_bertugas']);

    $query = "INSERT INTO teknisi (nama_teknisi, no_wa, area_bertugas) VALUES ('$nama', '$no_wa', '$area')";
    if (mysqli_query($koneksi, $query)) {
        header('Location: teknisi.php?status=sukses');
        exit;
    } else {
        $error = "Gagal menambah data.";
    }
}
require '../includes/header.php';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top"></nav>
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h4 class="mb-0"><i class="bi bi-person-plus-fill me-2"></i>Tambah Teknisi Baru</h4>
                </div>
                <div class="card-body">
                    <form action="tambah_teknisi.php" method="POST">
                        <div class="mb-3"><label for="nama_teknisi">Nama Teknisi</label><input type="text"
                                name="nama_teknisi" class="form-control" required></div>
                        <div class="mb-3"><label for="no_wa">No. WhatsApp</label><input type="text" name="no_wa"
                                class="form-control" placeholder="Contoh: 62813..." required></div>
                        <div class="mb-3"><label for="area_bertugas">Area Bertugas</label><input type="text"
                                name="area_bertugas" class="form-control" placeholder="Contoh: Jakarta Selatan"
                                required></div>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                        <a href="teknisi.php" class="btn btn-secondary">Batal</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php require '../includes/footer.php'; ?>