<?php
session_start();
require 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];

    $query_user = "SELECT id_user FROM users WHERE username = ?";
    $stmt_user = mysqli_prepare($koneksi, $query_user);
    mysqli_stmt_bind_param($stmt_user, "s", $username);
    mysqli_stmt_execute($stmt_user);
    $result_user = mysqli_stmt_get_result($stmt_user);

    if ($user = mysqli_fetch_assoc($result_user)) {
        $id_user = $user['id_user'];

        $token = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", time() + 3600); // Token berlaku 1 jam

        $query_update = "UPDATE users SET reset_token = ?, token_expiry = ? WHERE id_user = ?";
        $stmt_update = mysqli_prepare($koneksi, $query_update);
        mysqli_stmt_bind_param($stmt_update, "ssi", $token, $expiry, $id_user);
        mysqli_stmt_execute($stmt_update);

        // --- SIMULASI PENGIRIMAN EMAIL ---
        $reset_link = "http://localhost/listrik-pascabayar/reset_sandi.php?token=" . $token;

        $pesan = "Jika akun dengan username tersebut ada, link reset telah dibuat. <br><br>" .
            "<strong>[SIMULASI EMAIL]</strong> Klik link di bawah ini untuk mereset password:<br>" .
            "<a href='$reset_link' class='fw-bold'>$reset_link</a>";
    } else {
        $pesan = "Jika akun dengan username tersebut ada, instruksi reset telah dikirim.";
    }

    $_SESSION['pesan_reset'] = $pesan;
    header('Location: lupa_sandi.php');
    exit;
}
?>