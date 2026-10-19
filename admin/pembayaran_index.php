<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$id_admin = $_SESSION['admin_id'];
// Aksi Konfirmasi Pembayaran oleh Admin
if (isset($_GET['aksi']) && isset($_GET['id'])) {
    $id_pembayaran = mysqli_real_escape_string($koneksi, $_GET['id']);
    $status_baru   = ($_GET['aksi'] === 'setuju') ? 'verified' : 'rejected';
    $u_bayar = mysqli_query($koneksi, "UPDATE pembayaran SET status_konfirmasi = '$status_baru', verified_by = '$id_admin' WHERE id_pembayaran = '$id_pembayaran'");
    if ($u_bayar && $status_baru === 'verified') {
        $q_get_tagihan = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT id_tagihan FROM pembayaran WHERE id_pembayaran = '$id_pembayaran'"));
        if ($q_get_tagihan) {
            $id_tagihan = $q_get_tagihan['id_tagihan'];
            mysqli_query($koneksi, "UPDATE pajak_tagihan SET status_tagihan = 'paid' WHERE id_tagihan = '$id_tagihan'");
        }
    }

    header("Location: pembayaran_index.php?status=sukses_verifikasi");
    exit();
}
// Query Semua Data Pembayaran Masuk
$query = "SELECT p.*, pt.kode_bayar, pt.total_bayar, k.no_plat, u.nama_lengkap, a.nama_petugas 
          FROM pembayaran p 
          JOIN pajak_tagihan pt ON p.id_tagihan = pt.id_tagihan 
          JOIN kendaraan k ON pt.id_kendaraan = k.id_kendaraan 
          JOIN users u ON k.id_user = u.id_user 
          LEFT JOIN admin a ON p.verified_by = a.id_admin 
          ORDER BY p.id_pembayaran DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Verifikasi Pembayaran - Admin Samsat</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
</head>
<body>

    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>

        <div class="admin-content">
            <div class="admin-header">
                <h3 style="margin:0; font-weight:700; color:#0f172a;">Verifikasi Pembayaran Pajak</h3>
            </div>

            <div style="padding: 2rem;">
                
                <?php if (isset($_GET['status']) && $_GET['status'] == 'sukses_verifikasi'): ?>
                    <div class="alert alert-success" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 0.85rem; border-radius: 8px; margin-bottom: 1rem;">Status verifikasi pembayaran berhasil diperbarui!</div>
                <?php endif; ?>

                <div class="card-detail">
                    <h3>📑 Pengajuan Konfirmasi Pembayaran Masuk</h3>
                    <div style="overflow-x: auto; margin-top: 1rem;">
                        <table class="table-detail" style="width: 100%;">
                            <thead>
                                <tr style="text-align: left; background-color: #f8fafc; font-size: 0.85rem; color: #64748b;">
                                    <th style="padding: 0.75rem;">KODE BAYAR</th>
                                    <th style="padding: 0.75rem;">WAJIB PAJAK</th>
                                    <th style="padding: 0.75rem;">NO PLAT</th>
                                    <th style="padding: 0.75rem;">METODE</th>
                                    <th style="padding: 0.75rem;">TOTAL</th>
                                    <th style="padding: 0.75rem;">STATUS</th>
                                    <th style="padding: 0.75rem;">DIVERIFIKASI OLEH</th>
                                    <th style="padding: 0.75rem; text-align: center;">AKSI VERIFIKASI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td style="padding: 0.85rem; font-weight: 700; color: #2563eb;"><?= htmlspecialchars($row['kode_bayar']) ?></td>
                                            <td style="padding: 0.85rem;"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                            <td style="padding: 0.85rem; font-weight: 700;"><?= htmlspecialchars($row['no_plat']) ?></td>
                                            <td style="padding: 0.85rem; text-transform: uppercase; font-size: 0.8rem;"><?= htmlspecialchars($row['metode_pembayaran']) ?></td>
                                            <td style="padding: 0.85rem; font-weight: 800;">Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
                                            <td style="padding: 0.85rem;">
                                                <?php if ($row['status_konfirmasi'] === 'verified' || $row['status_konfirmasi'] === 'confirmed'): ?>
                                                    <span style="background: #ecfdf5; color: #065f46; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">VALID</span>
                                                <?php elseif ($row['status_konfirmasi'] === 'waiting'): ?>
                                                    <span style="background: #fef3c7; color: #92400e; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">WAITING</span>
                                                <?php else: ?>
                                                    <span style="background: #fef2f2; color: #991b1b; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 700;">REJECTED</span>
                                                <?php endif; ?>
                                            </td>
                                            <td style="padding: 0.85rem; font-size: 0.85rem; color: #64748b;"><?= htmlspecialchars($row['nama_petugas'] ?? '-') ?></td>
                                            <td style="padding: 0.85rem; text-align: center;">
                                                <?php if ($row['status_konfirmasi'] === 'waiting'): ?>
                                                    <a href="pembayaran_index.php?aksi=setuju&id=<?= $row['id_pembayaran'] ?>" onclick="return confirm('Setujui pembayaran ini?')" style="background: #10b981; color: white; padding: 0.35rem 0.6rem; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: 600; margin-right: 0.2rem;">Setujui</a>
                                                    <a href="pembayaran_index.php?aksi=tolak&id=<?= $row['id_pembayaran'] ?>" onclick="return confirm('Tolak pembayaran ini?')" style="background: #ef4444; color: white; padding: 0.35rem 0.6rem; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: 600;">Tolak</a>
                                                <?php else: ?>
                                                    <span style="font-size: 0.8rem; color: #94a3b8; font-style: italic;">Selesai</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="8" style="text-align: center; padding: 2rem; color: #64748b;">Belum ada pengajuan verifikasi pembayaran.</td>
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