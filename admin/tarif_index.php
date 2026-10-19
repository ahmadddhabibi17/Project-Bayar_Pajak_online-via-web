<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$query  = "SELECT * FROM tarif_pajak ORDER BY id_tarif ASC";
$result = mysqli_query($koneksi, $query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tarif Pajak - Admin Samsat</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
</head>
<body>

    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>

        <div class="admin-content">
            <div class="admin-header">
                <h3 style="margin:0; font-weight:700; color:#0f172a;">Tarif & Biaya Pajak</h3>
            </div>

            <div style="padding: 2rem;">
                <div class="card-detail">
                    <h3>💰 Daftar Tarif Komponen Pajak Kendaraan</h3>
                    <div style="overflow-x: auto; margin-top: 1rem;">
                        <table class="table-detail" style="width: 100%;">
                            <thead>
                                <tr style="text-align: left; background-color: #f8fafc; font-size: 0.85rem; color: #64748b;">
                                    <th style="padding: 0.75rem;">JENIS KENDARAAN</th>
                                    <th style="padding: 0.75rem;">PERSEN PKB</th>
                                    <th style="padding: 0.75rem;">SWDKLLJ</th>
                                    <th style="padding: 0.75rem;">BIAYA ADM STNK</th>
                                    <th style="padding: 0.75rem;">DENDA / BULAN</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($result && mysqli_num_rows($result) > 0): ?>
                                    <?php while ($row = mysqli_fetch_assoc($result)): ?>
                                        <tr>
                                            <td style="padding: 0.85rem; font-weight: 700; text-transform: capitalize; color: #2563eb;"><?= htmlspecialchars($row['jenis_kendaraan']) ?></td>
                                            <td style="padding: 0.85rem; font-weight: 600;"><?= $row['persen_pkb'] ?>%</td>
                                            <td style="padding: 0.85rem;">Rp <?= number_format($row['swdkllj'], 0, ',', '.') ?></td>
                                            <td style="padding: 0.85rem;">Rp <?= number_format($row['biaya_adm_stnk'], 0, ',', '.') ?></td>
                                            <td style="padding: 0.85rem; color: #ef4444; font-weight: 600;">Rp <?= number_format($row['denda_per_bulan'], 0, ',', '.') ?></td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="5" style="text-align: center; padding: 2rem; color: #64748b;">Belum ada data tarif pajak di database.</td>
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