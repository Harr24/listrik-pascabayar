<?php
session_start();
require '../config/database.php';

// Keamanan: Cek sesi admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin' || !isset($_GET['id'])) {
    header('Location: ../login.php');
    exit;
}

$id_area = $_GET['id'];

// PENTING: Periksa apakah ada pelanggan di area ini sebelum menghapus
$check_query = "SELECT id_pelanggan FROM pelanggan WHERE id_area = ?";
$stmt_check = mysqli_prepare($koneksi, $check_query);
mysqli_stmt_bind_param($stmt_check, "i", $id_area);
mysqli_stmt_execute($stmt_check);
$result_check = mysqli_stmt_get_result($stmt_check);

if (mysqli_num_rows($result_check) > 0) {
    // Jika ada pelanggan, jangan hapus. Kirim pesan error.
    header('Location: area_layanan.php?status=gagal_hapus');
    exit;
}

// Jika aman, lanjutkan proses hapus
$delete_query = "DELETE FROM area_layanan WHERE id_area = ?";
$stmt_delete = mysqli_prepare($koneksi, $delete_query);
mysqli_stmt_bind_param($stmt_delete, "i", $id_area);
mysqli_stmt_execute($stmt_delete);

header('Location: area_layanan.php?status=sukses_hapus');
exit;
?>