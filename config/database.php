<?php
// config/database.php

$host = "localhost";
$user = "root";       // User default XAMPP
$pass = "";           // Password default XAMPP (kosong)
$db = "listrikaja";  // Nama database yang kita pakai sekarang

// Membuat koneksi ke database
$koneksi = mysqli_connect($host, $user, $pass, $db);

// Cek jika koneksi gagal
if (!$koneksi) {
    die("Koneksi ke database gagal: " . mysqli_connect_error());
}
?>