<?php
session_start();
require '../config/database.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit;
}

if (!isset($_GET['id'])) {
    echo "ID Tagihan tidak ditemukan.";
    exit;
}

$id_tagihan = intval($_GET['id']);

// Proses jika tombol "Set Lunas" diklik
if (isset($_POST['set_lunas'])) {
    $update = mysqli_query($koneksi, "
        UPDATE tagihan 
        SET status = 'lunas', tanggal_bayar = CURDATE()
        WHERE id_tagihan = $id_tagihan
    ");

    if ($update) {
        header("Location: detail_tagihan.php?id=$id_tagihan");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Gagal mengubah status.</div>";
    }
}

// Ambil data tagihan
$query = "SELECT 
            tagihan.id_tagihan,
            tagihan.total_bayar,
            tagihan.status,
            tagihan.tanggal_bayar,
            penggunaan.bulan,
            penggunaan.tahun,
            penggunaan.meter_awal,
            penggunaan.meter_akhir,
            users.nama_lengkap
          FROM tagihan
          JOIN penggunaan ON tagihan.id_penggunaan = penggunaan.id_penggunaan
          JOIN pelanggan ON penggunaan.id_pelanggan = pelanggan.id_pelanggan
          JOIN users ON pelanggan.id_user = users.id_user
          WHERE tagihan.id_tagihan = $id_tagihan
          LIMIT 1";

$result = mysqli_query($koneksi, $query);
$data = mysqli_fetch_assoc($result);

if (!$data) {
    echo "Data tagihan tidak ditemukan.";
    exit;
}

$jumlah_meter = $data['meter_akhir'] - $data['meter_awal'];

require '../includes/header.php';
?>

<style>
    .invoice-box {
        max-width: 800px;
        margin: auto;
        padding: 30px;
        border: 1px solid #eee;
        border-radius: 12px;
        font-size: 16px;
        line-height: 24px;
        font-family: 'Segoe UI', sans-serif;
        color: #333;
        background: #fff;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    }

    .invoice-box h2 {
        font-weight: 600;
        margin-bottom: 10px;
    }

    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
    }

    .invoice-box table td {
        padding: 8px 5px;
        vertical-align: top;
    }

    .invoice-box table tr.heading td {
        background: #f5f5f5;
        border-bottom: 1px solid #ddd;
        font-weight: 600;
    }

    .status-lunas {
        color: green;
        font-weight: bold;
    }

    .status-belum {
        color: red;
        font-weight: bold;
    }

    .status-diproses {
        color: orange;
        font-weight: bold;
    }

    @media print {

        .btn,
        nav,
        footer,
        form {
            display: none;
        }

        body {
            background: white;
        }

        .invoice-box {
            box-shadow: none;
            border: none;
        }
    }
</style>

<div class="invoice-box mt-4">
    <h2>Detail Tagihan</h2>
    <table>
        <tr>
            <td><strong>ID Tagihan:</strong> <?= $data['id_tagihan']; ?></td>
            <td align="right"><strong>Periode:</strong>
                <?= date("F Y", mktime(0, 0, 0, $data['bulan'], 1, $data['tahun'])); ?></td>
        </tr>
    </table>

    <hr>

    <table>
        <tr>
            <td><strong>Nama Pelanggan</strong></td>
            <td>: <?= htmlspecialchars($data['nama_lengkap']); ?></td>
        </tr>
        <tr>
            <td><strong>Meter Awal</strong></td>
            <td>: <?= $data['meter_awal']; ?></td>
        </tr>
        <tr>
            <td><strong>Meter Akhir</strong></td>
            <td>: <?= $data['meter_akhir']; ?></td>
        </tr>
        <tr>
            <td><strong>Pemakaian</strong></td>
            <td>: <?= $jumlah_meter; ?> kWh</td>
        </tr>
        <tr>
            <td><strong>Total Tagihan</strong></td>
            <td>: <strong>Rp <?= number_format($data['total_bayar'], 2, ',', '.'); ?></strong></td>
        </tr>
        <tr>
            <td><strong>Status</strong></td>
            <td>:
                <?php
                if ($data['status'] == 'lunas') {
                    echo "<span class='status-lunas'>Lunas</span>";
                } elseif ($data['status'] == 'diproses') {
                    echo "<span class='status-diproses'>Diproses</span>";
                } else {
                    echo "<span class='status-belum'>Belum Lunas</span>";
                    echo '<form method="post" class="mt-2 d-inline">';
                    echo '<button type="submit" name="set_lunas" class="btn btn-sm btn-success">Set Lunas Sekarang</button>';
                    echo '</form>';
                }
                ?>
            </td>
        </tr>
        <?php if ($data['tanggal_bayar'] && $data['status'] == 'lunas'): ?>
            <tr>
                <td><strong>Tanggal Pembayaran</strong></td>
                <td>: <?= date("d F Y", strtotime($data['tanggal_bayar'])); ?></td>
            </tr>
        <?php endif; ?>
    </table>

    <div class="mt-4">
        <a href="tagihan.php" class="btn btn-secondary">← Kembali</a>
        <button class="btn btn-primary" onclick="window.print()">🖨 Cetak PDF</button>
    </div>
</div>

<?php require '../includes/footer.php'; ?>