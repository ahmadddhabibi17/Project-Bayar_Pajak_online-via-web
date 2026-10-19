<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$nama_admin = $_SESSION['admin_nama'];

// Stat queries
$q_kendaraan    = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM kendaraan");
$stat_kendaraan = ($q_kendaraan) ? mysqli_fetch_assoc($q_kendaraan)['total'] : 0;

$q_pending    = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pembayaran WHERE status_konfirmasi = 'waiting'");
$stat_pending = ($q_pending) ? mysqli_fetch_assoc($q_pending)['total'] : 0;

$q_unpaid    = mysqli_query($koneksi, "SELECT COUNT(*) as total FROM pajak_tagihan WHERE status_tagihan = 'unpaid'");
$stat_unpaid = ($q_unpaid) ? mysqli_fetch_assoc($q_unpaid)['total'] : 0;

// Query Transaksi
$query_pembayaran = "SELECT p.*, pt.kode_bayar, pt.tahun_pajak, pt.total_bayar, k.no_plat, u.nama_lengkap 
                    FROM pembayaran p 
                    JOIN pajak_tagihan pt ON p.id_tagihan = pt.id_tagihan 
                    JOIN kendaraan k ON pt.id_kendaraan = k.id_kendaraan 
                    JOIN users u ON k.id_user = u.id_user 
                    ORDER BY p.tgl_pembayaran DESC";
$res_pembayaran = mysqli_query($koneksi, $query_pembayaran);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - E-Samsat</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
</head>
<body>

    <div class="admin-wrapper">
        <!-- Sidebar Menu Kiri -->
        <?php include 'sidebar.php'; ?>

        <!-- Area Konten Utama -->
        <div class="admin-content">
            <!-- Header Atas -->
            <div class="admin-header">
                <h3 style="margin:0; font-weight:700; color:#0f172a;">Dashboard Overview</h3>
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <span style="font-size: 0.9rem; font-weight: 600; color: #475569;">👮‍♂️ <?= htmlspecialchars($nama_admin) ?></span>
                    <a href="logout.php" class="btn-login" style="background-color: #ef4444; padding: 0.4rem 1rem;">Logout</a>
                </div>
            </div>

            <!-- Isi Konten Dashboard -->
            <div style="padding: 2rem;">
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                    <div style="background: white; padding: 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0;">
                        <div style="color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Total Kendaraan Terdaftar</div>
                        <div style="font-size: 2.25rem; font-weight: 800; color: #2563eb; margin-top: 0.5rem;"><?= $stat_kendaraan ?></div>
                    </div>

                    <div style="background: white; padding: 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0;">
                        <div style="color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Perlu Verifikasi Bayar</div>
                        <div style="font-size: 2.25rem; font-weight: 800; color: #f59e0b; margin-top: 0.5rem;"><?= $stat_pending ?></div>
                    </div>

                    <div style="background: white; padding: 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0;">
                        <div style="color: #64748b; font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">Tagihan Menunggak</div>
                        <div style="font-size: 2.25rem; font-weight: 800; color: #ef4444; margin-top: 0.5rem;"><?= $stat_unpaid ?></div>
                    </div>
                </div>

                <div class="card-detail">
                    <h3>📑 Data Transaksi Masuk & Verifikasi</h3>
                    <div style="overflow-x: auto; margin-top: 1rem;">
                        <table class="table-detail" style="width: 100%;">
                            <thead>
                                <tr style="text-align: left; background-color: #f8fafc; font-size: 0.85rem; color: #64748b;">
                                    <th style="padding: 0.75rem;">KODE BAYAR</th>
                                    <th style="padding: 0.75rem;">NAMA WAJIB PAJAK</th>
                                    <th style="padding: 0.75rem;">NO PLAT</th>
                                    <th style="padding: 0.75rem;">TANGGAL BAYAR</th>
                                    <th style="padding: 0.75rem;">TOTAL</th>
                                    <th style="padding: 0.75rem;">STATUS</th>
                                    <th style="padding: 0.75rem; text-align: center;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($res_pembayaran && mysqli_num_rows($res_pembayaran) > 0): ?>
                                    <?php while ($p = mysqli_fetch_assoc($res_pembayaran)): ?>
                                        <tr>
                                            <td style="padding: 0.85rem; font-weight: 600; color: #2563eb;"><?= htmlspecialchars($p['kode_bayar']) ?></td>
                                            <td style="padding: 0.85rem;"><?= htmlspecialchars($p['nama_lengkap']) ?></td>
                                            <td style="padding: 0.85rem; font-weight: 700;"><?= htmlspecialchars($p['no_plat']) ?></td>
                                            <td style="padding: 0.85rem;"><?= date('d-m-Y H:i', strtotime($p['tgl_pembayaran'])) ?></td>
                                            <td style="padding: 0.85rem; font-weight: 700;">Rp <?= number_format($p['total_bayar'], 0, ',', '.') ?></td>
                                            <td style="padding: 0.85rem;">
                                                <?php if ($p['status_konfirmasi'] === 'verified'): ?>
                                                    <span style="background: #ecfdf5; color: #065f46; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">DISETUJUI</span>
                                                <?php elseif ($p['status_konfirmasi'] === 'waiting'): ?>
                                                    <span style="background: #fef3c7; color: #92400e; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">MENUNGGU</span>
                                                <?php else: ?>
                                                    <span style="background: #fef2f2; color: #991b1b; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">DITOLAK</span>
                                                <?php endif; ?>
                                            </td>
                                            </td>
                                            <td style="padding: 0.85rem; text-align: center;">
                                                <button onclick="alert('Verifikasi pembayaran')" style="background: #0f172a; color: white; border: none; padding: 0.35rem 0.8rem; border-radius: 6px; font-size: 0.8rem; cursor: pointer;">Review</button>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; padding: 2rem; color: #64748b;">Belum ada riwayat transaksi pembayaran masuk.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

</body>
</html>