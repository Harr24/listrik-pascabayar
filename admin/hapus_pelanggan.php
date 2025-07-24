<?php
session_start();
require '../config/database.php';

// Keamanan: Cek sesi admin dan keberadaan ID
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin' || !isset($_GET['id'])) {
    header('Location: ../login.php');
    exit;
}

$id_pelanggan = $_GET['id'];

// 1. Ambil id_user terkait sebelum menghapus pelanggan
$query_get_user = "SELECT id_user FROM pelanggan WHERE id_pelanggan = ?";
$stmt_get_user = mysqli_prepare($koneksi, $query_get_user);
mysqli_stmt_bind_param($stmt_get_user, "i", $id_pelanggan);
mysqli_stmt_execute($stmt_get_user);
$result_user = mysqli_stmt_get_result($stmt_get_user);
$data_user = mysqli_fetch_assoc($result_user);
$id_user = $data_user['id_user'];

// 2. Dapatkan semua ID penggunaan & ID tagihan milik pelanggan ini
$query_ids = "SELECT p.id_penggunaan, t.id_tagihan 
              FROM penggunaan p
              LEFT JOIN tagihan t ON p.id_penggunaan = t.id_penggunaan
              WHERE p.id_pelanggan = ?";
$stmt_ids = mysqli_prepare($koneksi, $query_ids);
mysqli_stmt_bind_param($stmt_ids, "i", $id_pelanggan);
mysqli_stmt_execute($stmt_ids);
$result_ids = mysqli_stmt_get_result($stmt_ids);

$list_id_penggunaan = [];
$list_id_tagihan = [];
while ($row = mysqli_fetch_assoc($result_ids)) {
    $list_id_penggunaan[] = $row['id_penggunaan'];
    if ($row['id_tagihan']) {
        $list_id_tagihan[] = $row['id_tagihan'];
    }
}

// Hanya lanjut jika ada data 'anak' yang perlu dihapus
if (!empty($list_id_tagihan)) {
    $in_tagihan = str_repeat('?,', count($list_id_tagihan) - 1) . '?';
    // 3. Hapus semua pembayaran terkait
    $stmt_pembayaran = mysqli_prepare($koneksi, "DELETE FROM pembayaran WHERE id_tagihan IN ($in_tagihan)");
    mysqli_stmt_bind_param($stmt_pembayaran, str_repeat('i', count($list_id_tagihan)), ...$list_id_tagihan);
    mysqli_stmt_execute($stmt_pembayaran);
}

if (!empty($list_id_penggunaan)) {
    $in_penggunaan = str_repeat('?,', count($list_id_penggunaan) - 1) . '?';
    // 4. Hapus semua tagihan terkait
    $stmt_tagihan = mysqli_prepare($koneksi, "DELETE FROM tagihan WHERE id_penggunaan IN ($in_penggunaan)");
    mysqli_stmt_bind_param($stmt_tagihan, str_repeat('i', count($list_id_penggunaan)), ...$list_id_penggunaan);
    mysqli_stmt_execute($stmt_tagihan);
}

// 5. Hapus semua penggunaan
$stmt_penggunaan = mysqli_prepare($koneksi, "DELETE FROM penggunaan WHERE id_pelanggan = ?");
mysqli_stmt_bind_param($stmt_penggunaan, "i", $id_pelanggan);
mysqli_stmt_execute($stmt_penggunaan);

// 6. Hapus pelanggan
$stmt_pelanggan = mysqli_prepare($koneksi, "DELETE FROM pelanggan WHERE id_pelanggan = ?");
mysqli_stmt_bind_param($stmt_pelanggan, "i", $id_pelanggan);
mysqli_stmt_execute($stmt_pelanggan);

// 7. Hapus user
$stmt_user = mysqli_prepare($koneksi, "DELETE FROM users WHERE id_user = ?");
mysqli_stmt_bind_param($stmt_user, "i", $id_user);
mysqli_stmt_execute($stmt_user);

header("Location: pelanggan.php?status=sukses_hapus");
exit;
?>