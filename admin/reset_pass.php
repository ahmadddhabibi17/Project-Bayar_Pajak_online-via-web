<?php
include '../config/koneksi.php';

// Generate hash password resmi dari server PHP kamu langsung
$password_baru = "admin123";
$hash_resmi    = password_hash($password_baru, PASSWORD_BCRYPT);

// Update ke database admin
$query = "UPDATE admin SET password = '$hash_resmi' WHERE username = 'admin_samsat'";
$run   = mysqli_query($koneksi, $query);

if ($run) {
    echo "<h3>SUKSES! Password admin berhasil di-reset.</h3>";
    echo "<p>Username: <strong>admin_samsat</strong></p>";
    echo "<p>Password: <strong>admin123</strong></p>";
    echo "<br><a href='login.php'>Klik di sini untuk Login</a>";
} else {
    echo "Gagal update database: " . mysqli_error($koneksi);
}
?>