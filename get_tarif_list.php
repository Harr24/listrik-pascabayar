<?php
// get_tarif_list.php
require 'config/database.php';

// Query untuk mengambil semua data tarif
$query = "SELECT golongan_tarif, daya, tarif_per_kwh FROM tarif ORDER BY daya ASC";
$result = mysqli_query($koneksi, $query);

$tarif_list = [];
while ($row = mysqli_fetch_assoc($result)) {
    $tarif_list[] = $row;
}

// Mengembalikan data dalam format JSON
header('Content-Type: application/json');
echo json_encode(['tarif' => $tarif_list]);
?>