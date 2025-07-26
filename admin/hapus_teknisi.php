<?php
session_start();
require '../config/database.php';
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    mysqli_query($koneksi, "DELETE FROM teknisi WHERE id_teknisi = '$id'");
}
header('Location: teknisi.php?status=dihapus');
exit;
?>