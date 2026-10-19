<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$query  = "SELECT u.*, COUNT(k.id_kendaraan) as jumlah_kendaraan 
          FROM users u 
          LEFT JOIN kendaraan k ON u.id_user = k.id_user 
          GROUP BY u.id_user 
          ORDER BY u.id_user DESC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Data Wajib Pajak - Admin Samsat</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
</head>
<body>

    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>

        <div class="admin-content">
            <div class="admin-header">
                <h3 style="margin:0; font-weight:700; color:#0f172a;">Data Wajib Pajak (Masyarakat)</h3>
            </div>

            <div style="padding: 2rem;">
                <div class="card-detail">
                    <h3>👥 Daftar Akun Wajib Pajak Terdaftar</h3>
                    <div style="overflow-x: auto; margin-top: 1rem;">
                        <table class="table-detail" style="width: 100%;">
                            <thead>
                                <tr style="text-align: left; background-color: #f8fafc; font-size: 0.85rem; color: #64748b;">
                                    <th style="padding: 0.75rem;">NIK</th>
                                    <th style="padding: 0.75rem;">NAMA LENGKAP</th>
                                    <th style="padding: 0.75rem;">EMAIL</th>
                                    <th style="padding: 0.75rem;">NO. HP</th>
                                    <th style="padding: 0.75rem;">ALAMAT</th>
                                    <th style="padding: 0.75rem; text-align: center;">JML KENDARAAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td style="padding: 0.85rem; font-weight: 700; color: #2563eb;"><?= htmlspecialchars($row['nik']) ?></td>
                                            <td style="padding: 0.85rem; font-weight: 600; color: #0f172a;"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                            <td style="padding: 0.85rem;"><?= htmlspecialchars($row['email']) ?></td>
                                            <td style="padding: 0.85rem;"><?= htmlspecialchars($row['no_hp'] ?? '-') ?></td>
                                            <td style="padding: 0.85rem; font-size: 0.85rem; color: #64748b;"><?= htmlspecialchars($row['alamat'] ?? '-') ?></td>
                                            <td style="padding: 0.85rem; text-align: center; font-weight: 700;">
                                                <span style="background: #eff6ff; color: #2563eb; padding: 4px 10px; border-radius: 20px; font-size: 0.8rem;"><?= $row['jumlah_kendaraan'] ?> Unit</span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; padding: 2rem; color: #64748b;">Belum ada data Wajib Pajak yang terdaftar.</td>
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