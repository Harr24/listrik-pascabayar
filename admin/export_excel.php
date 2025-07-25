<?php
session_start();
require '../config/database.php';

// Keamanan: Cek sesi admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    exit('Akses ditolak');
}

// Ambil filter dari URL
$filter_bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('n');
$filter_tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');
$filter_daya = isset($_GET['daya']) ? $_GET['daya'] : '';

$where_conditions = [];
$params = [];
$types = "";

$where_conditions[] = "penggunaan.bulan = ?";
$params[] = $filter_bulan;
$types .= "i";

$where_conditions[] = "penggunaan.tahun = ?";
$params[] = $filter_tahun;
$types .= "i";

if (!empty($filter_daya)) {
    $where_conditions[] = "tarif.daya = ?";
    $params[] = $filter_daya;
    $types .= "i";
}

$where_clause = "WHERE " . implode(' AND ', $where_conditions);

// Query yang sama persis dengan halaman laporan
$query = "SELECT 
            users.nama_lengkap,
            penggunaan.bulan,
            penggunaan.tahun,
            tarif.daya,
            tagihan.total_bayar,
            tagihan.status
          FROM tagihan
          JOIN penggunaan ON tagihan.id_penggunaan = penggunaan.id_penggunaan
          JOIN pelanggan ON penggunaan.id_pelanggan = pelanggan.id_pelanggan
          JOIN users ON pelanggan.id_user = users.id_user
          JOIN tarif ON pelanggan.id_tarif = tarif.id_tarif
          $where_clause
          ORDER BY users.nama_lengkap ASC";

$stmt = mysqli_prepare($koneksi, $query);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Proses pembuatan file CSV
$nama_file = "laporan_tagihan_" . $filter_bulan . "_" . $filter_tahun . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $nama_file);
$output = fopen('php://output', 'w');

// Tulis header kolom
fputcsv($output, ['Nama Pelanggan', 'Periode', 'Daya (VA)', 'Total Tagihan (Rp)', 'Status']);

// Tulis data baris
while ($row = mysqli_fetch_assoc($result)) {
    $periode = date("F Y", mktime(0, 0, 0, $row['bulan'], 1, $row['tahun']));
    $baris = [
        $row['nama_lengkap'],
        $periode,
        $row['daya'],
        $row['total_bayar'],
        $row['status']
    ];
    fputcsv($output, $baris);
}

fclose($output);
exit;
?>