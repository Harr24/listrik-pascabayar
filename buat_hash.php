<?php
$password_baru = 'adminbaru'; // ganti dengan password yang kamu mau
$hash = password_hash($password_baru, PASSWORD_DEFAULT);
echo "Password baru: $password_baru<br>";
echo "Hash-nya: $hash";
?>