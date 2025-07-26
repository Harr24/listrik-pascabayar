<?php
// jumlah_teknisi.php
require 'config/database.php';

$query = "SELECT COUNT(id_teknisi) AS total FROM teknisi";
$result = mysqli_query($koneksi, $query);

$data = mysqli_fetch_assoc($result);
if (!$data) {
    $data = ['total' => 0];
}

header('Content-Type: application/json');
echo json_encode($data);
exit;
