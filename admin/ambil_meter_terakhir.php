<?php
// admin/ambil_meter_terakhir.php
require '../config/database.php';

// Pastikan id_pelanggan dikirim
if (isset($_GET['id_pelanggan'])) {
    $id_pelanggan = $_GET['id_pelanggan'];

    // Query untuk mengambil meter_akhir terbaru dari pelanggan yang dipilih
    $query = "SELECT meter_akhir FROM penggunaan 
              WHERE id_pelanggan = '$id_pelanggan' 
              ORDER BY tahun DESC, bulan DESC LIMIT 1";

    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        $meter_awal = $data['meter_akhir'];
    } else {
        // Jika pelanggan belum pernah punya catatan, mulai dari 0
        $meter_awal = 0;
    }

    // Mengembalikan data dalam format JSON
    header('Content-Type: application/json');
    echo json_encode(['meter_awal' => $meter_awal]);
}
?>