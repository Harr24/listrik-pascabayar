<?php
session_start();
require '../config/database.php'; // Sesuaikan path ini jika berbeda

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php?status=ilegal');
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: pelanggan.php?status=error&pesan=ID pelanggan tidak ditemukan.');
    exit;
}

$id_pelanggan = $_GET['id'];

if (!filter_var($id_pelanggan, FILTER_VALIDATE_INT)) {
    header('Location: pelanggan.php?status=error&pesan=ID pelanggan tidak valid.');
    exit;
}

// Gunakan Prepared Statement untuk menghapus data
$query_delete = "DELETE FROM pelanggan WHERE id_pelanggan = ?";
$stmt = mysqli_prepare($koneksi, $query_delete);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "i", $id_pelanggan); // 'i' untuk integer

    if (mysqli_stmt_execute($stmt)) {
        // Opsional: Hapus data terkait di tabel 'users' jika id_user hanya digunakan oleh pelanggan ini
        // Pertimbangkan juga Foreign Key Constraints ON DELETE CASCADE di database untuk otomatisasi ini

        header('Location: pelanggan.php?status=sukses&pesan=Pelanggan berhasil dihapus.');
    } else {
        header('Location: pelanggan.php?status=error&pesan=Gagal menghapus pelanggan: ' . mysqli_error($koneksi));
    }
    mysqli_stmt_close($stmt);
} else {
    header('Location: pelanggan.php?status=error&pesan=Kesalahan database saat menyiapkan query.');
}

mysqli_close($koneksi);
exit;
?>