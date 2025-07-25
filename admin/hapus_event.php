<?php
// admin/hapus_event.php
session_start();
require '../config/database.php';
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin' || !isset($_GET['id'])) {
    header('Location: ../login.php');
    exit;
}

$id_event = $_GET['id'];
// Ambil nama file gambar untuk dihapus dari server
$q = mysqli_query($koneksi, "SELECT gambar FROM events WHERE id_event = '$id_event'");
$data = mysqli_fetch_assoc($q);
if ($data) {
    $path_gambar = dirname(__DIR__) . '/uploads/events/' . $data['gambar'];
    if (file_exists($path_gambar)) {
        unlink($path_gambar); // Hapus file gambar
    }
}
// Hapus data dari database
mysqli_query($koneksi, "DELETE FROM events WHERE id_event = '$id_event'");
header('Location: event.php?status=sukses_hapus');
exit;
?>