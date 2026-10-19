<?php
$host     = "localhost";
$user     = "root";    
$password = "";        
$database = "db_pajak_kendaraan_online"; // Nama database terbaru

$koneksi = mysqli_connect($host, $user, $password, $database);

if (!$koneksi) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>