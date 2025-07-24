<?php
session_start();
require '../config/database.php';

// Keamanan: Cek sesi admin dan keberadaan ID
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin' || !isset($_GET['id'])) {
    header('Location: ../login.php');
    exit;
}

$id_tarif = $_GET['id'];

// Hapus data dari database
$query = "DELETE FROM tarif WHERE id_tarif = $id_tarif";

if (mysqli_query($koneksi, $query)) {
    header('Location: tarif.php?status=sukses_hapus');
} else {
    header('Location: tarif.php?status=gagal_hapus');
}
exit;
?>