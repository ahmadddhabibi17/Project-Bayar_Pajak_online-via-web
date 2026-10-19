<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$id_user   = $_SESSION['user_id'];
$nama_user = $_SESSION['user_nama'];

// Query Riwayat Pembayaran User
$query = "SELECT p.*, pt.kode_bayar, pt.tahun_pajak, pt.total_bayar, k.no_plat, k.merk, k.tipe 
          FROM pembayaran p 
          JOIN pajak_tagihan pt ON p.id_tagihan = pt.id_tagihan 
          JOIN kendaraan k ON pt.id_kendaraan = k.id_kendaraan 
          WHERE k.id_user = '$id_user' 
          ORDER BY p.id_pembayaran DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pembayaran - E-Samsat</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
</head>
<body style="background-color: #f8fafc;">

    <nav class="navbar">
        <div class="logo">E-SAMSAT ONLINE</div>
        <div style="display: flex; align-items: center; gap: 1rem;">
            <a href="dashboard.php" style="color: white; text-decoration: none; font-size: 0.9rem; font-weight: 600;">Dashboard</a>
            <a href="logout.php" class="btn-login" style="background-color: #ef4444;">Logout</a>
        </div>
    </nav>

    <div style="max-width: 1100px; margin: 2rem auto; padding: 0 1.5rem;">
        
        <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses'): ?>
            <div class="alert alert-success" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 0.85rem; border-radius: 8px; margin-bottom: 1.5rem;">
                🎉 Bukti pembayaran berhasil dikirim! Petugas Samsat akan melakukan verifikasi data Anda.
            </div>
        <?php endif; ?>

        <div class="card-detail">
            <h3>📜 Riwayat Konfirmasi Pembayaran Pajak</h3>
            <div style="overflow-x: auto; margin-top: 1rem;">
                <table class="table-detail" style="width: 100%;">
                    <thead>
                        <tr style="text-align: left; background-color: #f8fafc; font-size: 0.85rem; color: #64748b;">
                            <th style="padding: 0.75rem;">KODE BAYAR</th>
                            <th style="padding: 0.75rem;">NO PLAT</th>
                            <th style="padding: 0.75rem;">KENDARAAN</th>
                            <th style="padding: 0.75rem;">METODE</th>
                            <th style="padding: 0.75rem;">TANGGAL BAYAR</th>
                            <th style="padding: 0.75rem;">TOTAL</th>
                            <th style="padding: 0.75rem; text-align: center;">STATUS VERIFIKASI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && mysqli_num_rows($result) > 0): ?>
                            <?php while ($p = mysqli_fetch_assoc($result)): ?>
                                <tr>
                                    <td style="padding: 0.85rem; font-weight: 700; color: #2563eb;"><?= htmlspecialchars($p['kode_bayar']) ?></td>
                                    <td style="padding: 0.85rem; font-weight: 700;"><?= htmlspecialchars($p['no_plat']) ?></td>
                                    <td style="padding: 0.85rem;"><?= htmlspecialchars($p['merk'] . ' ' . $p['tipe']) ?></td>
                                    <td style="padding: 0.85rem; text-transform: uppercase; font-size: 0.8rem;"><?= htmlspecialchars($p['metode_pembayaran']) ?></td>
                                    <td style="padding: 0.85rem;"><?= date('d-m-Y H:i', strtotime($p['tgl_pembayaran'])) ?></td>
                                    <td style="padding: 0.85rem; font-weight: 800;">Rp <?= number_format($p['total_bayar'], 0, ',', '.') ?></td>
                                    <td style="padding: 0.85rem; text-align: center;">
                                        <?php if ($p['status_konfirmasi'] === 'verified'): ?>
                                            <span style="background: #ecfdf5; color: #065f46; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">✓ LUNAS / VALID</span>
                                        <?php elseif ($p['status_konfirmasi'] === 'waiting'): ?>
                                            <span style="background: #fef3c7; color: #92400e; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">⌛ MENUNGGU VERIFIKASI</span>
                                        <?php else: ?>
                                            <span style="background: #fef2f2; color: #991b1b; padding: 5px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">❌ DITOLAK</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" style="text-align: center; padding: 2rem; color: #64748b;">Belum ada riwayat pembayaran yang dikirimkan.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</body>
</html>