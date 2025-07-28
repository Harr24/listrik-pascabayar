<?php
// Selalu mulai sesi di paling atas
session_start();

// Hancurkan semua data sesi yang ada
session_destroy();

// Alihkan (redirect) pengguna kembali ke halaman utama (homepage)
header('Location: index.php?status=logout');
exit;
?>