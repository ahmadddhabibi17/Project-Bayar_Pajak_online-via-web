<?php
session_start();
include '../config/koneksi.php';

// Proteksi Halaman User
if (!isset($_SESSION['user_logged_in']) || $_SESSION['user_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$id_user   = $_SESSION['user_id'];
$nama_user = $_SESSION['user_nama'];

// Query Kendaraan Milik User
$query_kendaraan = "SELECT * FROM kendaraan WHERE id_user = '$id_user'";
$res_kendaraan   = mysqli_query($koneksi, $query_kendaraan);

// Query Tagihan Pajak yang Belum Dibayar ('unpaid')
$query_tagihan = "SELECT pt.*, k.no_plat, k.merk, k.tipe 
                 FROM pajak_tagihan pt 
                 JOIN kendaraan k ON pt.id_kendaraan = k.id_kendaraan 
                 WHERE k.id_user = '$id_user' AND pt.status_tagihan = 'unpaid'
                 ORDER BY pt.tgl_jatuh_tempo ASC";
$res_tagihan = mysqli_query($koneksi, $query_tagihan);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Wajib Pajak - E-Samsat</title>
    <link rel="stylesheet" href="/PAJAK_KENDARAAN/assets/css/style.css">
</head>
<body>

    <nav class="navbar">
        <div class="logo">E-SAMSAT ONLINE</div>
        <div style="display: flex; align-items: center; gap: 1rem;">
            <span style="font-size: 0.9rem; font-weight: 600;">👋 <?= htmlspecialchars($nama_user) ?></span>
            <a href="logout.php" class="btn-login" style="background-color: #ef4444;">Logout</a>
        </div>
    </nav>

    <div style="max-width: 1100px; margin: 2rem auto; padding: 0 1.5rem;">
        
        <div style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white; padding: 2rem; border-radius: 16px; margin-bottom: 2rem;">
            <h2 style="margin-bottom: 0.5rem;">Selamat Datang, <?= htmlspecialchars($nama_user) ?>!</h2>
            <p style="color: #94a3b8; font-size: 0.95rem;">Kelola data kendaraan dan cek kewajiban pembayaran pajak tahunan Anda di sini.</p>
        </div>

        <!-- Section Tagihan -->
        <div class="card-detail" style="margin-bottom: 2.5rem;">
            <h3>⚠️ Tagihan Pajak Perlu Dibayar</h3>
            
            <?php if ($res_tagihan && mysqli_num_rows($res_tagihan) > 0): ?>
                <div style="overflow-x: auto; margin-top: 1rem;">
                    <table class="table-detail" style="width: 100%;">
                        <thead>
                            <tr style="text-align: left; background-color: #f8fafc; font-size: 0.85rem; color: #64748b;">
                                <th style="padding: 0.75rem;">KODE BAYAR</th>
                                <th style="padding: 0.75rem;">NO PLAT</th>
                                <th style="padding: 0.75rem;">KENDARAAN</th>
                                <th style="padding: 0.75rem;">TAHUN</th>
                                <th style="padding: 0.75rem;">JATUH TEMPO</th>
                                <th style="padding: 0.75rem;">TOTAL BAYAR</th>
                                <th style="padding: 0.75rem; text-align: center;">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($tagihan = mysqli_fetch_assoc($res_tagihan)): ?>
                                <tr>
                                    <td style="padding: 0.85rem; font-weight: 700; color: #2563eb;"><?= htmlspecialchars($tagihan['kode_bayar']) ?></td>
                                    <td style="padding: 0.85rem; font-weight: 700;"><?= htmlspecialchars($tagihan['no_plat']) ?></td>
                                    <td style="padding: 0.85rem;"><?= htmlspecialchars($tagihan['merk'] . ' ' . $tagihan['tipe']) ?></td>
                                    <td style="padding: 0.85rem;"><?= $tagihan['tahun_pajak'] ?></td>
                                    <td style="padding: 0.85rem; color: #ef4444; font-weight: 600;"><?= date('d M Y', strtotime($tagihan['tgl_jatuh_tempo'])) ?></td>
                                    <td style="padding: 0.85rem; font-weight: 800;">Rp <?= number_format($tagihan['total_bayar'], 0, ',', '.') ?></td>
                                    <td style="padding: 0.85rem; text-align: center;">
                                        <a href="pembayaran_tambah.php?id_tagihan=<?= $tagihan['id_tagihan'] ?>" class="btn-submit" style="padding: 0.4rem 1rem; font-size: 0.8rem; margin: 0; text-decoration: none; display: inline-block;">Bayar Sekarang</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #10b981; padding: 2rem 0; font-weight: 600;">🎉 Tidak ada tagihan pajak yang menunggak!</p>
            <?php endif; ?>
        </div>

        <!-- Section Kendaraan Saya -->
        <div class="card-detail">
            <h3>🚗 Kendaraan Saya</h3>
            <?php if ($res_kendaraan && mysqli_num_rows($res_kendaraan) > 0): ?>
                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; margin-top: 1rem;">
                    <?php while ($k = mysqli_fetch_assoc($res_kendaraan)): ?>
                        <div style="border: 1px solid #e2e8f0; border-radius: 12px; padding: 1.25rem; background: #ffffff;">
                            <div style="font-size: 1.1rem; font-weight: 800; color: #0f172a; margin-bottom: 0.5rem;"><?= htmlspecialchars($k['no_plat']) ?></div>
                            <div style="font-size: 0.9rem; color: #475569; margin-bottom: 0.25rem;"><?= htmlspecialchars($k['merk'] . ' ' . $k['tipe']) ?> (<?= $k['tahun_pembuatan'] ?>)</div>
                            <div style="font-size: 0.8rem; color: #64748b; margin-bottom: 0.5rem;">Warna: <?= htmlspecialchars($k['warna']) ?> | <?= htmlspecialchars($k['cc_kendaraan']) ?> cc</div>
                            <div style="font-size: 0.75rem; color: #94a3b8;">No. Rangka: <?= htmlspecialchars($k['no_rangka']) ?></div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p style="text-align: center; color: #64748b; padding: 1.5rem 0;">Belum ada data kendaraan terdaftar atas nama NIK Anda.</p>
            <?php endif; ?>
        </div>

    </div>

</body>
</html>