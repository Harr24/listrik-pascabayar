<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pelanggan') {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    echo "ID Tagihan tidak ditemukan.";
    exit;
}

$id_tagihan = intval($_GET['id']);
$id_user = $_SESSION['id_user'];

// Validasi: pastikan tagihan milik user yang login
$query = "SELECT 
            t.id_tagihan,
            t.total_bayar,
            t.status,
            t.tanggal_bayar,
            p.bulan,
            p.tahun,
            p.meter_awal,
            p.meter_akhir,
            u.nama_lengkap,
            pel.nomor_meter,
            pel.alamat
          FROM tagihan t
          JOIN penggunaan p ON t.id_penggunaan = p.id_penggunaan
          JOIN pelanggan pel ON p.id_pelanggan = pel.id_pelanggan
          JOIN users u ON pel.id_user = u.id_user
          WHERE t.id_tagihan = $id_tagihan AND u.id_user = $id_user
          LIMIT 1";

$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "Data tagihan tidak ditemukan atau bukan milik Anda.";
    exit;
}

$jumlah_meter = $data['meter_akhir'] - $data['meter_awal'];
require '../includes/header.php';
?>

<style>
    .invoice-box {
        max-width: 800px;
        margin: 30px auto;
        padding: 40px;
        background: #ffffff;
        border-radius: 12px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        font-family: 'Segoe UI', sans-serif;
        color: #333;
    }

    .invoice-box h2 {
        color: #0dcaf0;
        font-weight: bold;
        margin-bottom: 30px;
        text-align: center;
    }

    .invoice-box table {
        width: 100%;
    }

    .invoice-box td {
        padding: 10px 5px;
        vertical-align: top;
    }

    .invoice-box .label {
        font-weight: 600;
        width: 40%;
        color: #444;
    }

    .invoice-box .value {
        color: #000;
    }

    .btn-container {
        margin-top: 30px;
        text-align: right;
    }

    @media print {

        .btn-container,
        nav,
        footer {
            display: none !important;
        }

        body {
            background: #fff;
        }
    }
</style>

<div class="invoice-box">
    <h2>🧾 Rincian Pembayaran Tagihan</h2>
    <table>
        <tr>
            <td class="label">Nama Pelanggan</td>
            <td class="value">: <?= htmlspecialchars($data['nama_lengkap']); ?></td>
        </tr>
        <tr>
            <td class="label">Nomor Meter</td>
            <td class="value">: <?= htmlspecialchars($data['nomor_meter']); ?></td>
        </tr>
        <tr>
            <td class="label">Alamat</td>
            <td class="value">: <?= htmlspecialchars($data['alamat']); ?></td>
        </tr>
        <tr>
            <td class="label">Periode</td>
            <td class="value">: <?= date("F Y", mktime(0, 0, 0, $data['bulan'], 1, $data['tahun'])); ?></td>
        </tr>
        <tr>
            <td class="label">Meter Awal</td>
            <td class="value">: <?= $data['meter_awal']; ?></td>
        </tr>
        <tr>
            <td class="label">Meter Akhir</td>
            <td class="value">: <?= $data['meter_akhir']; ?></td>
        </tr>
        <tr>
            <td class="label">Jumlah Pemakaian</td>
            <td class="value">: <?= $jumlah_meter; ?> kWh</td>
        </tr>
        <tr>
            <td class="label">Total Bayar</td>
            <td class="value">: Rp <?= number_format($data['total_bayar'], 0, ',', '.'); ?></td>
        </tr>
        <tr>
            <td class="label">Status Pembayaran</td>
            <td class="value">
                :
                <?php
                if ($data['status'] == 'lunas') {
                    echo '<span class="text-success fw-bold">LUNAS</span>';
                } elseif ($data['status'] == 'diproses') {
                    echo '<span class="text-warning fw-bold">Diproses</span>';
                } else {
                    echo '<span class="text-danger fw-bold">Belum Lunas</span>';
                }
                ?>
            </td>
        </tr>
        <?php if ($data['tanggal_bayar'] && $data['status'] === 'lunas'): ?>
            <tr>
                <td class="label">Tanggal Pembayaran</td>
                <td class="value">: <?= date("d F Y", strtotime($data['tanggal_bayar'])); ?></td>
            </tr>
        <?php endif; ?>
    </table>

    <div class="btn-container">
        <a href="index.php" class="btn btn-secondary">← Kembali</a>
        <button onclick="window.print()" class="btn btn-primary">🖨 Cetak PDF</button>
    </div>
</div>

<?php require '../includes/footer.php'; ?>