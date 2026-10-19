<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $id_kendaraan = mysqli_real_escape_string($koneksi, $_GET['id']);
    
    $query = "DELETE FROM kendaraan WHERE id_kendaraan = '$id_kendaraan'";
    if (mysqli_query($koneksi, $query)) {
        header("Location: kendaraan_index.php?status=sukses_hapus");
    } else {
        header("Location: kendaraan_index.php?status=gagal_hapus");
    }
} else {
    header("Location: kendaraan_index.php");
}
exit();
?>