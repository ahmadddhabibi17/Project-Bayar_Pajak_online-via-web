<?php
session_start();
include '../config/koneksi.php';

// Proteksi Halaman User
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$id_user = $_SESSION['user_id'];
$pesan = "";

// Ambil ID Tagihan dari URL
$id_tagihan = mysqli_real_escape_string($koneksi, $_GET['id_tagihan'] ?? '');

// Query detail tagihan yang dipilih
$query_detail = "SELECT pt.*, k.no_plat, k.merk, k.tipe 
                 FROM pajak_tagihan pt 
                 JOIN kendaraan k ON pt.id_kendaraan = k.id_kendaraan 
                 WHERE pt.id_tagihan = '$id_tagihan' AND k.id_user = '$id_user' AND pt.status_tagihan = 'unpaid'";
$res_detail = mysqli_query($koneksi, $query_detail);
$tagihan = mysqli_fetch_assoc($res_detail);

if (!$tagihan) {
    header("Location: dashboard.php");
    exit();
}

// Proses Submit Pembayaran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $metode_pembayaran = mysqli_real_escape_string($koneksi, $_POST['metode_pembayaran']);
    $tgl_pembayaran    = date('Y-m-d H:i:s');
    
    // Upload Bukti Transfer
    $bukti_nama = $_FILES['bukti_transfer']['name'];
    $bukti_tmp  = $_FILES['bukti_transfer']['tmp_name'];
    $bukti_ext  = strtolower(pathinfo($bukti_nama, PATHINFO_EXTENSION));
    
    $file_baru = "bukti_" . time() . "_" . rand(100, 999) . "." . $bukti_ext;
    $target_dir = "../assets/uploads/bukti/";

    // Pastikan folder upload ada
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    if (move_uploaded_file($bukti_tmp, $target_dir . $file_baru)) {
        $query_insert = "INSERT INTO pembayaran (id_tagihan, tgl_pembayaran, metode_pembayaran, bukti_transfer, status_konfirmasi) 
                        VALUES ('$id_tagihan', '$tgl_pembayaran', '$metode_pembayaran', '$file_baru', 'waiting')";
        
        if (mysqli_query($koneksi, $query_insert)) {
            header("Location: riwayat_pembayaran.php?status=sukses");
            exit();
        } else {
            $pesan = "Gagal menyimpan data pembayaran: " . mysqli_error($koneksi);
        }
    } else {
        $pesan = "Gagal mengunggah foto bukti transfer. Pastikan format file benar!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bayar Pajak - E-Samsat</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
</head>
<body style="background-color: #f8fafc;">

    <nav class="navbar">
        <div class="logo">E-SAMSAT ONLINE</div>
        <div>
            <a href="dashboard.php" class="btn-login" style="background-color: #64748b;">&larr; Kembali ke Dashboard</a>
        </div>
    </nav>

    <div style="max-width: 650px; margin: 2rem auto; padding: 0 1.5rem;">
        <div class="card-detail">
            <h3>💳 Pembayaran Pajak Kendaraan</h3>
            
            <?php if (!empty($pesan)): ?>
                <div class="alert alert-danger" style="margin-top: 1rem;"><?= $pesan ?></div>
            <?php endif; ?>

            <!-- Ringkasan Rincian Tagihan -->
            <div style="background-color: #eff6ff; border: 1px solid #bfdbfe; border-radius: 10px; padding: 1.25rem; margin: 1.25rem 0;">
                <div style="font-size: 0.85rem; color: #1e40af; font-weight: 700;">DETAIL TAGIHAN (#<?= htmlspecialchars($tagihan['kode_bayar']) ?>)</div>
                <div style="font-size: 1.25rem; font-weight: 800; color: #1e3a8a; margin-top: 0.25rem;"><?= htmlspecialchars($tagihan['no_plat']) ?> - <?= htmlspecialchars($tagihan['merk'] . ' ' . $tagihan['tipe']) ?></div>
                <div style="font-size: 0.85rem; color: #3b82f6; margin-top: 0.25rem;">Tahun Pajak: <?= $tagihan['tahun_pajak'] ?> | Jatuh Tempo: <?= date('d M Y', strtotime($tagihan['tgl_jatuh_tempo'])) ?></div>
                <hr style="border: none; border-top: 1px solid #bfdbfe; margin: 0.75rem 0;">
                <div style="display: flex; justify-content: space-between; align-items: center;">
                    <span style="font-size: 0.9rem; font-weight: 600; color: #1e40af;">Total Harus Dibayar:</span>
                    <span style="font-size: 1.35rem; font-weight: 800; color: #1d4ed8;">Rp <?= number_format($tagihan['total_bayar'], 0, ',', '.') ?></span>
                </div>
            </div>

            <!-- Form Upload Pembayaran -->
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Metode Pembayaran</label>
                    <select name="metode_pembayaran" style="width:100%; padding: 0.85rem; border-radius:8px; border:1px solid #e2e8f0;" required>
                        <option value="">-- Pilih Bank / Payment Gateway --</option>
                        <option value="transfer_bca">Transfer Bank BCA (8820-XXXX-XXXX)</option>
                        <option value="transfer_mandiri">Transfer Bank Mandiri (137-00-XXXX-XXXX)</option>
                        <option value="transfer_bri">Transfer Bank BRI (0021-XXXX-XXXX)</option>
                        <option value="qris">QRIS Standar E-Samsat</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Upload Bukti Transfer (JPG / PNG / PDF)</label>
                    <input type="file" name="bukti_transfer" accept="image/*,.pdf" style="padding: 0.6rem;" required>
                </div>

                <div style="margin-top: 1.5rem;">
                    <button type="submit" class="btn-submit" style="width: 100%; margin: 0; padding: 0.9rem;">Kirim Konfirmasi Pembayaran</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>