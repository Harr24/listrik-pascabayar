<?php
// get_layanan.php
require 'config/database.php';

// Query untuk mengambil semua jenis daya yang unik dari tabel tarif, diurutkan dari kecil ke besar
$query = "SELECT DISTINCT daya FROM tarif ORDER BY daya ASC";
$result = mysqli_query($koneksi, $query);

$layanan = [];
while ($row = mysqli_fetch_assoc($result)) {
    // Tambahkan setiap daya ke dalam array
    $layanan[] = $row['daya'];
}

// Mengembalikan data dalam format JSON
header('Content-Type: application/json');
echo json_encode(['layanan' => $layanan]);
?>