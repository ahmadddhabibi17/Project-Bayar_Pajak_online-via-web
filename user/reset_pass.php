<?php
include '../config/koneksi.php';

$password_baru = "user123";
$hash_resmi    = password_hash($password_baru, PASSWORD_BCRYPT);

$email_user = "budi@gmail.com";

// Update ke database users
$query = "UPDATE users SET password = '$hash_resmi' WHERE email = '$email_user' OR nik = '3201010202020002'";
$run   = mysqli_query($koneksi, $query);

if ($run && mysqli_affected_rows($koneksi) > 0) {
    echo "<h3>SUKSES! Password akun Wajib Pajak berhasil di-reset.</h3>";
    echo "<p>Email / NIK: <strong>budi@gmail.com</strong> (atau <strong>3201010202020002</strong>)</p>";
    echo "<p>Password Baru: <strong>user123</strong></p>";
    echo "<br><a href='login.php'>Klik di sini untuk Login User</a>";
} else {
    echo "Gagal update database atau data user tidak ditemukan: " . mysqli_error($koneksi);
}
?>