<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$pesan = "";

// Proses Simpan Data (Create)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_user          = mysqli_real_escape_string($koneksi, $_POST['id_user']);
    $no_plat          = mysqli_real_escape_string($koneksi, $_POST['no_plat']);
    $no_mesin         = mysqli_real_escape_string($koneksi, $_POST['no_mesin']);
    $no_rangka        = mysqli_real_escape_string($koneksi, $_POST['no_rangka']);
    $merk             = mysqli_real_escape_string($koneksi, $_POST['merk']);
    $tipe             = mysqli_real_escape_string($koneksi, $_POST['tipe']);
    $warna            = mysqli_real_escape_string($koneksi, $_POST['warna']);
    $tahun_pembuatan  = mysqli_real_escape_string($koneksi, $_POST['tahun_pembuatan']);
    $cc_kendaraan     = mysqli_real_escape_string($koneksi, $_POST['cc_kendaraan']);
    $jenis_kendaraan  = mysqli_real_escape_string($koneksi, $_POST['jenis_kendaraan']);

    $query = "INSERT INTO kendaraan (id_user, no_plat, no_mesin, no_rangka, merk, tipe, warna, tahun_pembuatan, cc_kendaraan, jenis_kendaraan) 
              VALUES ('$id_user', '$no_plat', '$no_mesin', '$no_rangka', '$merk', '$tipe', '$warna', '$tahun_pembuatan', '$cc_kendaraan', '$jenis_kendaraan')";

    if (mysqli_query($koneksi, $query)) {
        header("Location: kendaraan_index.php?status=sukses_tambah");
        exit();
    } else {
        $pesan = "Gagal menyimpan data: " . mysqli_error($koneksi);
    }
}

// Ambil data Wajib Pajak untuk dropdown
$users = mysqli_query($koneksi, "SELECT id_user, nama_lengkap, nik FROM users");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Kendaraan - Admin Samsat</title>
    <link rel="stylesheet" href="/PAJAK_KENDARAAN/assets/css/style.css">
</head>
<body style="background-color: #f1f5f9;">

    <div style="max-width: 700px; margin: 3rem auto; padding: 0 1.5rem;">
        <div class="card-detail">
            <h3>➕ Tambah Data Kendaraan Baru</h3>
            
            <?php if (!empty($pesan)): ?>
                <div class="alert alert-danger"><?= $pesan ?></div>
            <?php endif; ?>

            <form action="" method="POST" style="margin-top: 1.5rem;">
                <div class="form-group">
                    <label>Pemilik (Wajib Pajak)</label>
                    <select name="id_user" style="width:100%; padding: 0.85rem; border-radius:8px; border:1px solid #e2e8f0;" required>
                        <option value="">-- Pilih Wajib Pajak --</option>
                        <?php while ($u = mysqli_fetch_assoc($users)): ?>
                            <option value="<?= $u['id_user'] ?>"><?= htmlspecialchars($u['nama_lengkap']) ?> (NIK: <?= $u['nik'] ?>)</option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Nomor Plat (No. Polisi)</label>
                        <input type="text" name="no_plat" placeholder="Contoh: B 1234 CD" required>
                    </div>
                    <div class="form-group">
                        <label>Jenis Kendaraan</label>
                        <select name="jenis_kendaraan" style="width:100%; padding: 0.85rem; border-radius:8px; border:1px solid #e2e8f0;" required>
                            <option value="motor">Motor</option>
                            <option value="mobil">Mobil</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Merk</label>
                        <input type="text" name="merk" placeholder="Contoh: Honda" required>
                    </div>
                    <div class="form-group">
                        <label>Tipe</label>
                        <input type="text" name="tipe" placeholder="Contoh: Vario 160" required>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Warna</label>
                        <input type="text" name="warna" placeholder="Hitam" required>
                    </div>
                    <div class="form-group">
                        <label>Tahun Pembuatan</label>
                        <input type="number" name="tahun_pembuatan" placeholder="2022" required>
                    </div>
                    <div class="form-group">
                        <label>Kapasitas (CC)</label>
                        <input type="number" name="cc_kendaraan" placeholder="160" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nomor Mesin</label>
                    <input type="text" name="no_mesin" required>
                </div>

                <div class="form-group">
                    <label>Nomor Rangka</label>
                    <input type="text" name="no_rangka" required>
                </div>

                <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                    <button type="submit" class="btn-submit" style="margin: 0;">Simpan Data</button>
                    <a href="kendaraan_index.php" style="padding: 0.9rem 1.5rem; background: #64748b; color: white; border-radius: 8px; text-decoration: none; font-weight: 600; text-align: center;">Batal</a>
                </div>
            </form>
        </div>
    </div>

</body>
</html>