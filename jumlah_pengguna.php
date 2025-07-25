<?php
// panggil file koneksi database
require 'config/database.php';

// Query untuk menghitung total pelanggan dari tabel 'pelanggan'
$query = "SELECT COUNT(id_pelanggan) as total FROM pelanggan";
$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

// Mengembalikan data dalam format JSON
header('Content-Type: application/json');
echo json_encode($data);
?>