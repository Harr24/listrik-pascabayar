<?php
// Tes paling fundamental, tanpa database sama sekali.

$passwordAsli = 'admin123';
$hashYangBenar = '$2y$10$r/J3r2y2T1t9Xz.5s.1y4u9A6W4S8u2p3O.c0s.5c1i3E.a1B2c3D';

echo "<h2>Tes Verifikasi Password Tanpa Database</h2>";
echo "<b>Password yang diuji:</b> " . $passwordAsli . "<br>";
echo "<b>Hash yang seharusnya:</b> " . $hashYangBenar . "<br><br>";

echo "--- Melakukan verifikasi... --- <br><br>";

if (password_verify($passwordAsli, $hashYangBenar)) {
    echo "<h1 style='color:green;'>HASIL: COCOK!</h1>";
    echo "<b>Kesimpulan:</b> Fungsi PHP Anda normal. Masalahnya 100% ada pada saat pengambilan data dari database atau struktur kolomnya. Silakan jalankan lagi perintah `ALTER TABLE` dan `UPDATE` dari langkah sebelumnya.";
} else {
    echo "<h1 style='color:red;'>HASIL: TIDAK COCOK!</h1>";
    echo "<b>Kesimpulan:</b> Ini adalah masalah yang SANGAT ANEH dan kemungkinan besar ada pada konfigurasi atau instalasi XAMPP/PHP Anda. Fungsi verifikasi password inti tidak berjalan sebagaimana mestinya.";
}
?>