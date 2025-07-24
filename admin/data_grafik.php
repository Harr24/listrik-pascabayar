<?php
// admin/data_grafik.php
session_start();
require '../config/database.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Akses ditolak']);
    exit;
}

$query = "SELECT 
            YEAR(p.tanggal_bayar) as tahun, 
            MONTH(p.tanggal_bayar) as bulan, 
            tr.daya,
            SUM(p.total_akhir) as total_pendapatan
          FROM pembayaran p
          JOIN tagihan t ON p.id_tagihan = t.id_tagihan
          JOIN penggunaan pg ON t.id_penggunaan = pg.id_penggunaan
          JOIN pelanggan pl ON pg.id_pelanggan = pl.id_pelanggan
          JOIN tarif tr ON pl.id_tarif = tr.id_tarif
          WHERE t.status = 'lunas' 
            AND p.tanggal_bayar >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
          GROUP BY tahun, bulan, tr.daya
          ORDER BY tahun ASC, bulan ASC, tr.daya ASC";

$result = mysqli_query($koneksi, $query);

$data_mentah = [];
$labels_bulan = [];
$labels_daya = [];

while ($row = mysqli_fetch_assoc($result)) {
    $bulan_tahun = date("F Y", mktime(0, 0, 0, $row['bulan'], 1, $row['tahun']));
    $daya = $row['daya'] . " VA";

    // Simpan data mentah
    $data_mentah[$bulan_tahun][$daya] = $row['total_pendapatan'];

    // Kumpulkan semua label bulan dan daya yang unik
    if (!in_array($bulan_tahun, $labels_bulan))
        $labels_bulan[] = $bulan_tahun;
    if (!in_array($daya, $labels_daya))
        $labels_daya[] = $daya;
}

sort($labels_daya); // Urutkan label daya

$datasets = [];
$warna = ['rgba(54, 162, 235, 0.6)', 'rgba(255, 99, 132, 0.6)', 'rgba(75, 192, 192, 0.6)', 'rgba(255, 206, 86, 0.6)'];
$i = 0;

foreach ($labels_daya as $daya) {
    $data_per_daya = [];
    foreach ($labels_bulan as $bulan) {
        // Jika ada data pendapatan untuk bulan & daya ini, masukkan. Jika tidak, masukkan 0.
        $data_per_daya[] = isset($data_mentah[$bulan][$daya]) ? $data_mentah[$bulan][$daya] : 0;
    }

    $datasets[] = [
        'label' => $daya,
        'data' => $data_per_daya,
        'backgroundColor' => $warna[$i % count($warna)] // Ambil warna secara bergiliran
    ];
    $i++;
}

header('Content-Type: application/json');
echo json_encode([
    'labels' => $labels_bulan,
    'datasets' => $datasets
]);

?>