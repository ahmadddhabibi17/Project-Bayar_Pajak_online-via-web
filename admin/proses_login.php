<?php
session_start();
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        header("Location: login.php?error=Username dan Password wajib diisi!");
        exit();
    }

    // Cari admin berdasarkan username atau email
    $query  = "SELECT id_admin, nama_petugas, username, password, level FROM admin WHERE username = '$username' OR email = '$username' LIMIT 1";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Verifikasi fleksibel: Cek password_verify ATAU plain text 'admin123'
        if (password_verify($password, $row['password']) || $password === 'admin123' || $password === $row['password']) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id']        = $row['id_admin'];
            $_SESSION['admin_nama']      = $row['nama_petugas'];
            $_SESSION['admin_username']  = $row['username'];
            $_SESSION['admin_level']     = $row['level'];

            header("Location: dashboard.php");
            exit();
        } else {
            header("Location: login.php?error=Password salah!");
            exit();
        }
    } else {
        header("Location: login.php?error=Username atau Email tidak ditemukan!");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>