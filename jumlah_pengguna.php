<?php
// Panggil file koneksi database
require 'config/database.php';

// Inisialisasi respons default
$response = ['total' => 0];

// Jalankan query untuk menghitung total pelanggan
$query = "SELECT COUNT(id_pelanggan) AS total FROM pelanggan";
$result = mysqli_query($koneksi, $query);

if ($result && mysqli_num_rows($result) > 0) {
    $data = mysqli_fetch_assoc($result);
    $response['total'] = (int) $data['total'];
}

// Set header untuk mengembalikan data JSON
header('Content-Type: application/json');
echo json_encode($response);
?>