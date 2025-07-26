<?php
// jumlah_teknisi.php
require 'config/database.php';

// Query untuk menghitung total teknisi
$query = "SELECT COUNT(id_teknisi) as total FROM teknisi";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

// Mengembalikan data dalam format JSON
header('Content-Type: application/json');
echo json_encode($data);
?>