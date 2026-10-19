<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

// Ambil filter bulan dan tahun (Default: Bulan & Tahun saat ini)
$bulan_pilih = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun_pilih = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// 1. Query Tabel Laporan Transaksi Bulanan (Hanya yang 'verified' / Lunas)
$query_laporan = "SELECT p.tgl_pembayaran, pt.kode_bayar, pt.total_bayar, k.no_plat, k.jenis_kendaraan, u.nama_lengkap 
                  FROM pembayaran p 
                  JOIN pajak_tagihan pt ON p.id_tagihan = pt.id_tagihan 
                  JOIN kendaraan k ON pt.id_kendaraan = k.id_kendaraan 
                  JOIN users u ON k.id_user = u.id_user 
                  WHERE p.status_konfirmasi = 'verified' 
                  AND MONTH(p.tgl_pembayaran) = '$bulan_pilih' 
                  AND YEAR(p.tgl_pembayaran) = '$tahun_pilih'
                  ORDER BY p.tgl_pembayaran DESC";
$res_laporan = mysqli_query($koneksi, $query_laporan);

$total_pendapatan_bulanan = 0;

// 2. Query Data untuk Pie Chart (Berdasarkan Jenis Kendaraan pada bulan tersebut)
$query_chart = "SELECT k.jenis_kendaraan, COUNT(p.id_pembayaran) as jumlah_transaksi 
                FROM pembayaran p 
                JOIN pajak_tagihan pt ON p.id_tagihan = pt.id_tagihan 
                JOIN kendaraan k ON pt.id_kendaraan = k.id_kendaraan 
                WHERE p.status_konfirmasi = 'verified' 
                AND MONTH(p.tgl_pembayaran) = '$bulan_pilih' 
                AND YEAR(p.tgl_pembayaran) = '$tahun_pilih'
                GROUP BY k.jenis_kendaraan";
$res_chart = mysqli_query($koneksi, $query_chart);

// Siapkan Array untuk Chart.js
$label_chart = [];
$data_chart  = [];
while ($row_chart = mysqli_fetch_assoc($res_chart)) {
    $label_chart[] = ucfirst($row_chart['jenis_kendaraan']);
    $data_chart[]  = $row_chart['jumlah_transaksi'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Transaksi - Admin Samsat</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
    <!-- Load Library Chart.js via CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>

        <div class="admin-content">
            <div class="admin-header">
                <h3 style="margin:0; font-weight:700; color:#0f172a;">Laporan Transaksi E-Samsat</h3>
                <button onclick="window.print()" style="background: #10b981; color: white; padding: 0.5rem 1rem; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">🖨️ Cetak Laporan</button>
            </div>

            <div style="padding: 2rem;">
                
                <!-- Filter Bulan & Tahun -->
                <div style="background: white; padding: 1.5rem; border-radius: 12px; border: 1px solid #e2e8f0; margin-bottom: 2rem;">
                    <form action="" method="GET" style="display: flex; gap: 1rem; align-items: flex-end;">
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #64748b; margin-bottom: 0.5rem;">Bulan</label>
                            <select name="bulan" style="padding: 0.6rem; border-radius: 6px; border: 1px solid #cbd5e1; width: 150px;">
                                <?php
                                $nama_bulan = ["", "Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"];
                                for ($i = 1; $i <= 12; $i++) {
                                    $val = str_pad($i, 2, "0", STR_PAD_LEFT);
                                    $selected = ($val == $bulan_pilih) ? "selected" : "";
                                    echo "<option value='$val' $selected>{$nama_bulan[$i]}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <label style="display: block; font-size: 0.85rem; font-weight: 600; color: #64748b; margin-bottom: 0.5rem;">Tahun</label>
                            <select name="tahun" style="padding: 0.6rem; border-radius: 6px; border: 1px solid #cbd5e1; width: 120px;">
                                <?php
                                $tahun_sekarang = date('Y');
                                for ($t = $tahun_sekarang; $t >= 2024; $t--) {
                                    $selected = ($t == $tahun_pilih) ? "selected" : "";
                                    echo "<option value='$t' $selected>$t</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <button type="submit" class="btn-submit" style="margin: 0; padding: 0.65rem 1.5rem;">Tampilkan</button>
                    </form>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 350px; gap: 1.5rem;">
                    
                    <!-- Tabel Data Laporan -->
                    <div class="card-detail" style="margin: 0;">
                        <h3>📊 Rekapan Pembayaran Berhasil</h3>
                        <div style="overflow-x: auto; margin-top: 1rem;">
                            <table class="table-detail" style="width: 100%;">
                                <thead>
                                    <tr style="text-align: left; background-color: #f8fafc; font-size: 0.85rem; color: #64748b;">
                                        <th style="padding: 0.75rem;">TANGGAL</th>
                                        <th style="padding: 0.75rem;">NO PLAT</th>
                                        <th style="padding: 0.75rem;">WAJIB PAJAK</th>
                                        <th style="padding: 0.75rem;">JENIS</th>
                                        <th style="padding: 0.75rem; text-align: right;">NOMINAL (Rp)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($res_laporan && mysqli_num_rows($res_laporan) > 0): ?>
                                        <?php while ($row = mysqli_fetch_assoc($res_laporan)): ?>
                                            <?php $total_pendapatan_bulanan += $row['total_bayar']; ?>
                                            <tr>
                                                <td style="padding: 0.85rem;"><?= date('d/m/Y', strtotime($row['tgl_pembayaran'])) ?></td>
                                                <td style="padding: 0.85rem; font-weight: 700;"><?= htmlspecialchars($row['no_plat']) ?></td>
                                                <td style="padding: 0.85rem;"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                                                <td style="padding: 0.85rem; text-transform: capitalize;"><?= htmlspecialchars($row['jenis_kendaraan']) ?></td>
                                                <td style="padding: 0.85rem; text-align: right; font-weight: 600;"><?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                        <tr style="background-color: #eff6ff;">
                                            <td colspan="4" style="padding: 1rem; text-align: right; font-weight: 800; color: #1e40af;">TOTAL PENDAPATAN BULAN INI</td>
                                            <td style="padding: 1rem; text-align: right; font-weight: 800; color: #1d4ed8;">Rp <?= number_format($total_pendapatan_bulanan, 0, ',', '.') ?></td>
                                        </tr>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="5" style="text-align: center; padding: 2rem; color: #64748b;">Belum ada transaksi berhasil pada bulan ini.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Panel Pie Chart -->
                    <div class="card-detail" style="margin: 0;">
                        <h3>Pie Chart Kendaraan</h3>
                        <p style="font-size: 0.8rem; color: #64748b; margin-bottom: 1rem;">Indikator jenis kendaraan yang membayar pajak bulan ini.</p>
                        
                        <?php if (count($data_chart) > 0): ?>
                            <!-- Canvas untuk Render Chart.js -->
                            <canvas id="kendaraanPieChart" width="100" height="100"></canvas>
                        <?php else: ?>
                            <div style="padding: 2rem 0; text-align: center; color: #94a3b8; font-size: 0.85rem;">
                                Data tidak tersedia untuk grafik.
                            </div>
                        <?php endif; ?>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- Script Konfigurasi Chart.js -->
    <?php if (count($data_chart) > 0): ?>
    <script>
        const ctx = document.getElementById('kendaraanPieChart').getContext('2d');
        const kendaraanPieChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: <?= json_encode($label_chart) ?>,
                datasets: [{
                    label: 'Jumlah Transaksi',
                    data: <?= json_encode($data_chart) ?>,
                    backgroundColor: [
                        '#3b82f6', // Biru untuk Motor
                        '#10b981', // Hijau untuk Mobil
                        '#f59e0b', // Kuning (jika ada jenis lain)
                        '#ef4444'  // Merah
                    ],
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });
    </script>
    <?php endif; ?>

    <!-- CSS khusus sembunyikan menu saat nge-print -->
    <style>
        @media print {
            .sidebar, .admin-header button, form { display: none !important; }
            .admin-wrapper { display: block; }
            .admin-content { background: white; }
            body { background: white; }
        }
    </style>

</body>
</html>