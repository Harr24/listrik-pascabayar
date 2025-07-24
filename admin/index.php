<?php
// Selalu mulai sesi di baris paling atas
session_start();
require '../config/database.php';

// Keamanan: Cek apakah pengguna sudah login dan rolenya adalah admin
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php?status=ilegal');
    exit;
}

// Menghitung jumlah pelanggan
$query_pelanggan = mysqli_query($koneksi, "SELECT COUNT(id_pelanggan) as total_pelanggan FROM pelanggan");
$data_pelanggan = mysqli_fetch_assoc($query_pelanggan);
$total_pelanggan = $data_pelanggan['total_pelanggan'];

// PERBAIKAN QUERY: Menghitung jumlah tagihan yang belum lunas dengan benar
$query_tagihan = mysqli_query($koneksi, "SELECT COUNT(id_tagihan) as total_tagihan FROM tagihan WHERE status = 'belum_lunas'");
$data_tagihan = mysqli_fetch_assoc($query_tagihan);
$total_tagihan_belum_lunas = $data_tagihan['total_tagihan'];

require '../includes/header.php';
?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link rel="stylesheet" href="../assets/css/admin_style.css">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark sticky-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand" href="index.php">⚡ ADMIN PANEL</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle"></i> Halo, <?= htmlspecialchars($_SESSION['nama_lengkap']); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="profil_admin.php">Ubah Profil</a></li>
                        <li><a class="dropdown-item" href="ubah_password.php">Ubah Password</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="../logout.php">Logout</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    <div class="row">
        <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
            <div class="position-sticky pt-3 px-2">
                <ul class="nav flex-column">
                    <li class="nav-item mb-1"><a class="nav-link active" href="index.php"><i
                                class="bi bi-speedometer2 icon"></i> Dashboard</a></li>
                    <li class="nav-item mb-1"><a class="nav-link" href="pelanggan.php"><i
                                class="bi bi-people-fill icon"></i> Manajemen Pelanggan</a></li>
                    <li class="nav-item mb-1"><a class="nav-link" href="tarif.php"><i class="bi bi-tags-fill icon"></i>
                            Manajemen Tarif</a></li>
                    <li class="nav-item mb-1"><a class="nav-link" href="penggunaan.php"><i
                                class="bi bi-pencil-square icon"></i> Input Penggunaan</a></li>
                    <li class="nav-item mb-1"><a class="nav-link" href="tagihan.php"><i
                                class="bi bi-file-earmark-text-fill icon"></i> Manajemen Tagihan</a></li>
                    <li class="nav-item mb-1"><a class="nav-link" href="pembayaran.php"><i
                                class="bi bi-check-circle-fill icon"></i> Konfirmasi Pembayaran</a></li>
                </ul>
            </div>
        </nav>

        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div
                class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
            </div>

            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="card card-statistic text-white bg-primary shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">TOTAL PELANGGAN</h5>
                                    <h3 class="fw-bold"><?= $total_pelanggan; ?> Orang</h3>
                                </div><i class="bi bi-people-fill" style="font-size: 3rem; opacity: 0.5;"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="card card-statistic text-white bg-danger shadow">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <h5 class="card-title">TAGIHAN BELUM LUNAS</h5>
                                    <h3 class="fw-bold"><?= $total_tagihan_belum_lunas; ?> Tagihan</h3>
                                </div><i class="bi bi-receipt" style="font-size: 3rem; opacity: 0.5;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-bar-chart-line-fill me-2"></i>Grafik Pendapatan Bulanan per
                        Golongan</h5>
                </div>
                <div class="card-body" style="height: 400px;">
                    <canvas id="grafikPendapatan"></canvas>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        fetch('data_grafik.php')
            .then(response => response.json())
            .then(serverData => {
                const ctx = document.getElementById('grafikPendapatan').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: serverData.labels,      // Label bulan dari PHP
                        datasets: serverData.datasets  // Seluruh dataset (per daya) dari PHP
                    },
                    options: {
                        scales: {
                            x: {
                                stacked: true, // Membuat batang bertumpuk
                            },
                            y: {
                                stacked: true, // Membuat batang bertumpuk
                                beginAtZero: true,
                                ticks: {
                                    callback: function (value) {
                                        return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
                                    }
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        let label = context.dataset.label || '';
                                        if (label) label += ': ';
                                        if (context.parsed.y !== null) {
                                            label += new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(context.parsed.y);
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        responsive: true,
                        maintainAspectRatio: false
                    }
                });
            })
            .catch(error => console.error('Error fetching chart data:', error));
    });
</script>


<?php
// Panggil footer
require '../includes/footer.php';
?>