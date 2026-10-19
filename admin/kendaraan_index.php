<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Ambil semua data kendaraan beserta nama pemiliknya
$query = "SELECT k.*, u.nama_lengkap, u.nik 
          FROM kendaraan k 
          JOIN users u ON k.id_user = u.id_user 
          ORDER BY k.id_kendaraan DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Data Kendaraan - Admin Samsat</title>
    <!-- Path CSS menggunakan versi relatif dan anti-cache -->
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
</head>
<body>

    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>

        <div class="admin-content">
            <div class="admin-header">
                <h3 style="margin:0; font-weight:700; color:#0f172a;">Kelola Data Kendaraan</h3>
                <div>
                <a href="tagihan_tambah.php" style="background: #10b981; color: white; padding: 0.6rem 1.2rem; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.85rem; margin-right: 0.5rem;">🧾 Terbitkan Tagihan</a>
                <a href="kendaraan_tambah.php" style="background: #2563eb; color: white; padding: 0.6rem 1.2rem; border-radius: 8px; text-decoration: none; font-weight: 600; font-size: 0.85rem;">+ Tambah Kendaraan</a>
            </div>
        </div>
            <div style="padding: 2rem;">
                
                <?php if (isset($_GET['status'])): ?>
                    <?php if ($_GET['status'] == 'sukses_tambah'): ?>
                        <div class="alert alert-success" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 0.85rem; border-radius: 8px; margin-bottom: 1rem;">Data kendaraan berhasil ditambahkan!</div>
                    <?php elseif ($_GET['status'] == 'sukses_hapus'): ?>
                        <div class="alert alert-success" style="background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; padding: 0.85rem; border-radius: 8px; margin-bottom: 1rem;">Data kendaraan berhasil dihapus!</div>
                    <?php endif; ?>
                <?php endif; ?>

                <div class="card-detail">
                    <h3>🚗 Daftar Kendaraan Terdaftar</h3>
                    <div style="overflow-x: auto; margin-top: 1rem;">
                        <table class="table-detail" style="width: 100%;">
                            <thead>
                                <tr style="text-align: left; background-color: #f8fafc; font-size: 0.85rem; color: #64748b;">
                                    <th style="padding: 0.75rem;">NO PLAT</th>
                                    <th style="padding: 0.75rem;">PEMILIK (NIK)</th>
                                    <th style="padding: 0.75rem;">MERK / TIPE</th>
                                    <th style="padding: 0.75rem;">JENIS</th>
                                    <th style="padding: 0.75rem;">WARNA / TAHUN</th>
                                    <th style="padding: 0.75rem; text-align: center;">AKSI</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td style="padding: 0.85rem; font-weight: 700; color: #0f172a;"><?= htmlspecialchars($row['no_plat']) ?></td>
                                            <td style="padding: 0.85rem;"><?= htmlspecialchars($row['nama_lengkap']) ?><br><span style="font-size: 0.75rem; color: #64748b;"><?= $row['nik'] ?></span></td>
                                            <td style="padding: 0.85rem;"><?= htmlspecialchars($row['merk'] . ' ' . $row['tipe']) ?></td>
                                            <td style="padding: 0.85rem; text-transform: capitalize;"><?= htmlspecialchars($row['jenis_kendaraan']) ?></td>
                                            <td style="padding: 0.85rem;"><?= htmlspecialchars($row['warna']) ?> (<?= $row['tahun_pembuatan'] ?>)</td>
                                            <td style="padding: 0.85rem; text-align: center;">
                                                <a href="kendaraan_edit.php?id=<?= $row['id_kendaraan'] ?>" style="background: #f59e0b; color: white; padding: 0.35rem 0.75rem; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: 600; margin-right: 0.25rem;">Edit</a>
                                                <a href="kendaraan_hapus.php?id=<?= $row['id_kendaraan'] ?>" onclick="return confirm('Yakin ingin menghapus kendaraan ini?')" style="background: #ef4444; color: white; padding: 0.35rem 0.75rem; border-radius: 6px; text-decoration: none; font-size: 0.8rem; font-weight: 600;">Hapus</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 2rem; color: #64748b;">Belum ada data kendaraan. Klik tombol "+ Tambah Kendaraan" di kanan atas.</td>
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