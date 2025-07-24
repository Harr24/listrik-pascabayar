<?php
// PHP_SELF akan memberikan path lengkap dari script yang sedang dieksekusi,
// basename() akan mengambil nama file saja (misal: "index.php" dari "/admin/index.php")
$current_page_basename = basename($_SERVER['PHP_SELF']);
?>

<div class="d-flex" id="wrapper">
    <nav id="sidebar-wrapper" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
        <div class="position-sticky pt-3 px-2">
            <ul class="nav flex-column">
                <li class="nav-item mb-1">
                    <a class="nav-link <?= ($current_page_basename == 'index.php') ? 'active' : ''; ?>"
                        href="index.php">
                        <i class="bi bi-speedometer2 icon"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a class="nav-link <?= ($current_page_basename == 'pelanggan.php') ? 'active' : ''; ?>"
                        href="pelanggan.php">
                        <i class="bi bi-people-fill icon"></i> Manajemen Pelanggan
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a class="nav-link <?= ($current_page_basename == 'tarif.php') ? 'active' : ''; ?>"
                        href="tarif.php">
                        <i class="bi bi-tags-fill icon"></i> Manajemen Tarif
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a class="nav-link <?= ($current_page_basename == 'penggunaan.php') ? 'active' : ''; ?>"
                        href="penggunaan.php">
                        <i class="bi bi-pencil-square icon"></i> Input Penggunaan
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a class="nav-link <?= ($current_page_basename == 'tagihan.php') ? 'active' : ''; ?>"
                        href="tagihan.php">
                        <i class="bi bi-file-earmark-text-fill icon"></i> Manajemen Tagihan
                    </a>
                </li>
                <li class="nav-item mb-1">
                    <a class="nav-link <?= ($current_page_basename == 'pembayaran.php') ? 'active' : ''; ?>"
                        href="pembayaran.php">
                        <i class="bi bi-check-circle-fill icon"></i> Konfirmasi Pembayaran
                    </a>
                </li>
            </ul>
        </div>
    </nav>
    <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4"></main>