<?php
// get_area.php
require 'config/database.php';

// Pastikan tidak ada output sebelum header JSON
header('Content-Type: application/json; charset=UTF-8');

// Ambil daftar area layanan
$query = "SELECT nama_area FROM area_layanan ORDER BY nama_area ASC";
$result = mysqli_query($koneksi, $query);

// Siapkan array area
$area_list = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $area_list[] = $row['nama_area'];
    }
}

// Output JSON dan hentikan skrip
echo json_encode(['area' => $area_list]);
exit;
