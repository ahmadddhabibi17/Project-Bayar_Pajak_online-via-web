<?php
session_start();
include '../config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identity = mysqli_real_escape_string($koneksi, trim($_POST['username'])); 
    $password = trim($_POST['password']);

    if (empty($identity) || empty($password)) {
        header("Location: login.php?error=Username/Email dan Password wajib diisi!");
        exit();
    }

    // Cari user berdasarkan NIK atau Email
    $query  = "SELECT id_user, nik, nama_lengkap, email, password FROM users WHERE email = '$identity' OR nik = '$identity' LIMIT 1";
    $result = mysqli_query($koneksi, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);

        // Verifikasi fleksibel: Cek password_verify ATAU plain text 'user123'
        if (password_verify($password, $row['password']) || $password === 'user123' || $password === $row['password']) {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id']        = $row['id_user'];
            $_SESSION['user_nama']      = $row['nama_lengkap'];
            $_SESSION['user_nik']       = $row['nik'];

            header("Location: dashboard.php");
            exit();
        } else {
            header("Location: login.php?error=Password salah!");
            exit();
        }
    } else {
        header("Location: login.php?error=NIK atau Email tidak terdaftar!");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>