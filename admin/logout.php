<?php
session_start();

// Hapus semua data session khusus admin
unset($_SESSION['admin_logged_in']);
unset($_SESSION['admin_id']);
unset($_SESSION['admin_nama']);
unset($_SESSION['admin_username']);
unset($_SESSION['admin_level']);

// Hancurkan session
session_destroy();

// Redirect ke halaman login admin dengan pesan
header("Location: login.php?pesan=logout");
exit();
?>