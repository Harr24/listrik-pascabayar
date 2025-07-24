<?php
// File untuk mengecek status folder upload dan konfigurasi PHP

echo "<h1>Pengecekan Sistem Upload</h1>";

// 1. Tentukan path folder tujuan
$folder_tujuan = 'uploads/bukti_pembayaran/';
echo "<b>Folder yang dicek:</b> " . $folder_tujuan . "<br>";

// 2. Cek apakah folder ada
if (file_exists($folder_tujuan) && is_dir($folder_tujuan)) {
    echo "<p style='color:green;'>✔️ Folder ditemukan.</p>";

    // 3. Cek apakah folder bisa ditulisi (writable)
    if (is_writable($folder_tujuan)) {
        echo "<p style='color:green;'>✔️ Folder bisa ditulisi (writable).</p>";
        echo "<p>Seharusnya upload bisa berjalan. Jika masih gagal, kemungkinan besar masalahnya ada pada batas ukuran file.</p>";
    } else {
        echo "<p style='color:red;'>❌ Folder TIDAK bisa ditulisi (not writable).</p>";
        echo "<p><b>Solusi:</b> Klik kanan folder 'uploads' di File Explorer, pilih 'Properties', buka tab 'Security', dan pastikan user 'SYSTEM' atau 'Users' memiliki izin 'Full control' atau setidaknya 'Write'.</p>";
    }
} else {
    echo "<p style='color:red;'>❌ Folder TIDAK ditemukan.</p>";
    echo "<p><b>Solusi:</b> Pastikan Anda sudah membuat folder 'uploads' dan di dalamnya ada folder 'bukti_pembayaran'.</p>";
}

echo "<hr>";
echo "<h2>Konfigurasi PHP (php.ini)</h2>";
echo "<ul>";
echo "<li><b>upload_max_filesize:</b> " . ini_get('upload_max_filesize') . " (Batas maksimal ukuran satu file)</li>";
echo "<li><b>post_max_size:</b> " . ini_get('post_max_size') . " (Batas maksimal total data yang dikirim lewat form)</li>";
echo "</ul>";
echo "<p>Pastikan file yang Anda upload tidak melebihi batas 'upload_max_filesize'.</p>";

?>