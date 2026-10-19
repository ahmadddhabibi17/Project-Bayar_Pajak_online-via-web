<?php
session_start();

// Hapus semua data session khusus user
unset($_SESSION['user_logged_in']);
unset($_SESSION['user_id']);
unset($_SESSION['user_nama']);
unset($_SESSION['user_nik']);

// Hancurkan session
session_destroy();

// Redirect ke halaman login user
header("Location: login.php?pesan=logout");
exit();
?>