<?php
session_start();
require '../config/database.php';
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'pelanggan') {
    header('Location: ../login.php');
    exit;
}

$result = mysqli_query($koneksi, "SELECT * FROM teknisi ORDER BY nama_teknisi ASC");
require '../includes/header.php';
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-info shadow-sm"></nav>
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="mb-0">Hubungi Teknisi</h2>
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Kembali</a>
    </div>
    <div class="row">
        <?php while ($teknisi = mysqli_fetch_assoc($result)):
            // Format nomor WA untuk link
            $nomor_wa_link = preg_replace('/[^0-9]/', '', $teknisi['no_wa']);
            if (substr($nomor_wa_link, 0, 1) === '0') {
                $nomor_wa_link = '62' . substr($nomor_wa_link, 1);
            }
            ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body text-center">
                        <i class="bi bi-person-circle fs-1 text-primary"></i>
                        <h5 class="card-title mt-2"><?= htmlspecialchars($teknisi['nama_teknisi']); ?></h5>
                        <p class="text-muted mb-2"><?= htmlspecialchars($teknisi['area_bertugas']); ?></p>
                        <a href="https://wa.me/<?= $nomor_wa_link; ?>" target="_blank" class="btn btn-success w-100">
                            <i class="bi bi-whatsapp"></i> Hubungi via WhatsApp
                        </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
<?php require '../includes/footer.php'; ?>