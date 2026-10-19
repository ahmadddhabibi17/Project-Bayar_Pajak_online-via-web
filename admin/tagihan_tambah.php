<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$pesan = "";

// Proses Buat Tagihan Pajak Baru
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kendaraan   = mysqli_real_escape_string($koneksi, $_POST['id_kendaraan']);
    $tahun_pajak    = mysqli_real_escape_string($koneksi, $_POST['tahun_pajak']);
    $tgl_jatuh_tempo= mysqli_real_escape_string($koneksi, $_POST['tgl_jatuh_tempo']);
    $njkb           = floatval($_POST['njkb']); // Nilai Jual Kendaraan Bermotor

    // Ambil data kendaraan dan tarif terkait
    $q_k = mysqli_query($koneksi, "SELECT k.*, t.* 
                                  FROM kendaraan k 
                                  JOIN tarif_pajak t ON k.jenis_kendaraan = t.jenis_kendaraan 
                                  WHERE k.id_kendaraan = '$id_kendaraan'");
    $kData = mysqli_fetch_assoc($q_k);

    if ($kData) {
        // 1. Hitung Nominal PKB = (Persen PKB / 100) * NJKB
        $nominal_pkb = ($kData['persen_pkb'] / 100) * $njkb;
        $nominal_swdkllj = floatval($kData['swdkllj']);
        $biaya_adm = floatval($kData['biaya_adm_stnk']);

        // 2. Hitung Denda (Jika Tanggal Jatuh Tempo Kurang Dari Hari Ini)
        $tgl_sekarang = new DateTime();
        $tgl_tempo    = new DateTime($tgl_jatuh_tempo);
        $nominal_denda = 0;

        if ($tgl_sekarang > $tgl_tempo) {
            $diff = $tgl_sekarang->diff($tgl_tempo);
            $bulan_keterlambatan = ($diff->y * 12) + $diff->m + ($diff->d > 0 ? 1 : 0);
            $nominal_denda = $bulan_keterlambatan * floatval($kData['denda_per_bulan']);
        }

        // 3. Total Bayar
        $total_bayar = $nominal_pkb + $nominal_swdkllj + $biaya_adm + $nominal_denda;

        // 4. Generate Kode Bayar Unik
        $kode_bayar = "BYR" . date('Ymd') . rand(1000, 9999);

        // 5. Simpan ke database pajak_tagihan
        $query_insert = "INSERT INTO pajak_tagihan 
                        (id_kendaraan, kode_bayar, tahun_pajak, nominal_pkb, nominal_swdkllj, nominal_denda, biaya_adm, total_bayar, status_tagihan, tgl_jatuh_tempo) 
                        VALUES 
                        ('$id_kendaraan', '$kode_bayar', '$tahun_pajak', '$nominal_pkb', '$nominal_swdkllj', '$nominal_denda', '$biaya_adm', '$total_bayar', 'unpaid', '$tgl_jatuh_tempo')";

        if (mysqli_query($koneksi, $query_insert)) {
            header("Location: kendaraan_index.php?status=tagihan_sukses");
            exit();
        } else {
            $pesan = "Gagal membuat tagihan: " . mysqli_error($koneksi);
        }
    } else {
        $pesan = "Data tarif untuk jenis kendaraan ini belum dikonfigurasi.";
    }
}

// Ambil daftar kendaraan untuk pilihan dropdown
$res_kendaraan = mysqli_query($koneksi, "SELECT k.*, u.nama_lengkap FROM kendaraan k JOIN users u ON k.id_user = u.id_user ORDER BY k.id_kendaraan DESC");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Buat Tagihan Pajak - Admin Samsat</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
</head>
<body>

    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>

        <div class="admin-content">
            <div class="admin-header">
                <h3 style="margin:0; font-weight:700; color:#0f172a;">Terbitkan Tagihan Pajak Baru</h3>
                <a href="kendaraan_index.php" style="background: #64748b; color: white; padding: 0.5rem 1rem; border-radius: 8px; text-decoration: none; font-size: 0.85rem;">&larr; Kembali</a>
            </div>

            <div style="padding: 2rem;">
                <div class="card-detail" style="max-width: 650px; margin: 0 auto;">
                    <h3>🧾 Form Menerbitkan Tagihan Pajak</h3>
                    
                    <?php if (!empty($pesan)): ?>
                        <div class="alert alert-danger" style="margin-top: 1rem;"><?= $pesan ?></div>
                    <?php endif; ?>

                    <form action="" method="POST" style="margin-top: 1.5rem;">
                        <div class="form-group">
                            <label>Pilih Kendaraan Target</label>
                            <select name="id_kendaraan" style="width:100%; padding: 0.85rem; border-radius:8px; border:1px solid #e2e8f0;" required>
                                <option value="">-- Pilih Plat Nomor / Pemilik --</option>
                                <?php while ($k = mysqli_fetch_assoc($res_kendaraan)): ?>
                                    <option value="<?= $k['id_kendaraan'] ?>">
                                        <?= htmlspecialchars($k['no_plat']) ?> - <?= htmlspecialchars($k['merk'] . ' ' . $k['tipe']) ?> (a.n <?= htmlspecialchars($k['nama_lengkap']) ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>Tahun Pajak</label>
                                <input type="number" name="tahun_pajak" value="<?= date('Y') ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Jatuh Tempo</label>
                                <input type="date" name="tgl_jatuh_tempo" value="<?= date('Y-m-d', strtotime('+1 year')) ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>NJKB (Nilai Jual Kendaraan Bermotor - Rp)</label>
                            <input type="number" name="njkb" placeholder="Contoh: 18000000" required>
                            <small style="color: #64748b; font-size: 0.75rem;">Nilai NJKB digunakan untuk kalkulasi otomatis tarif persentase PKB.</small>
                        </div>

                        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                            <button type="submit" class="btn-submit" style="margin: 0; background: #2563eb;">Kalkulasi & Terbitkan Tagihan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>