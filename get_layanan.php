<?php
// Panggil koneksi database
require 'config/database.php';

// Inisialisasi array layanan
$layanan = [];

// Jalankan query: ambil semua nilai daya unik, urutkan naik
$query = "SELECT DISTINCT daya FROM tarif ORDER BY daya ASC";
$result = mysqli_query($koneksi, $query);

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $layanan[] = (int) $row['daya']; // casting ke integer agar rapi saat JSON
    }
}

// Set header & keluarkan dalam format JSON
header('Content-Type: application/json');
echo json_encode(['layanan' => $layanan]);
