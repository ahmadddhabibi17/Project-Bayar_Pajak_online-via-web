<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$id_kendaraan = mysqli_real_escape_string($koneksi, $_GET['id'] ?? '');
$pesan = "";

// Cek data kendaraan
$q_get = mysqli_query($koneksi, "SELECT * FROM kendaraan WHERE id_kendaraan = '$id_kendaraan'");
if (!$q_get || mysqli_num_rows($q_get) == 0) {
    header("Location: kendaraan_index.php");
    exit();
}
$data = mysqli_fetch_assoc($q_get);

// Proses Update Data
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

    $query_update = "UPDATE kendaraan SET 
                     id_user = '$id_user',
                     no_plat = '$no_plat',
                     no_mesin = '$no_mesin',
                     no_rangka = '$no_rangka',
                     merk = '$merk',
                     tipe = '$tipe',
                     warna = '$warna',
                     tahun_pembuatan = '$tahun_pembuatan',
                     cc_kendaraan = '$cc_kendaraan',
                     jenis_kendaraan = '$jenis_kendaraan'
                     WHERE id_kendaraan = '$id_kendaraan'";

    if (mysqli_query($koneksi, $query_update)) {
        header("Location: kendaraan_index.php?status=sukses_edit");
        exit();
    } else {
        $pesan = "Gagal memperbarui data: " . mysqli_error($koneksi);
    }
}

$users = mysqli_query($koneksi, "SELECT id_user, nama_lengkap, nik FROM users");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Edit Kendaraan - Admin Samsat</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
</head>
<body>

    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>

        <div class="admin-content">
            <div class="admin-header">
                <h3 style="margin:0; font-weight:700; color:#0f172a;">Edit Data Kendaraan</h3>
                <a href="kendaraan_index.php" style="background: #64748b; color: white; padding: 0.5rem 1rem; border-radius: 8px; text-decoration: none; font-size: 0.85rem;">&larr; Kembali</a>
            </div>

            <div style="padding: 2rem;">
                <div class="card-detail" style="max-width: 800px; margin: 0 auto;">
                    <h3>✏️ Edit Data Kendaraan (<?= htmlspecialchars($data['no_plat']) ?>)</h3>
                    
                    <?php if (!empty($pesan)): ?>
                        <div class="alert alert-danger" style="margin-top: 1rem;"><?= $pesan ?></div>
                    <?php endif; ?>

                    <form action="" method="POST" style="margin-top: 1.5rem;">
                        <div class="form-group">
                            <label>Pemilik (Wajib Pajak)</label>
                            <select name="id_user" style="width:100%; padding: 0.85rem; border-radius:8px; border:1px solid #e2e8f0;" required>
                                <?php while ($u = mysqli_fetch_assoc($users)): ?>
                                    <option value="<?= $u['id_user'] ?>" <?= ($u['id_user'] == $data['id_user']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($u['nama_lengkap']) ?> (NIK: <?= $u['nik'] ?>)
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>Nomor Plat (No. Polisi)</label>
                                <input type="text" name="no_plat" value="<?= htmlspecialchars($data['no_plat']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Jenis Kendaraan</label>
                                <select name="jenis_kendaraan" style="width:100%; padding: 0.85rem; border-radius:8px; border:1px solid #e2e8f0;" required>
                                    <option value="motor" <?= ($data['jenis_kendaraan'] == 'motor') ? 'selected' : '' ?>>Motor</option>
                                    <option value="mobil" <?= ($data['jenis_kendaraan'] == 'mobil') ? 'selected' : '' ?>>Mobil</option>
                                </select>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>Merk</label>
                                <input type="text" name="merk" value="<?= htmlspecialchars($data['merk']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Tipe</label>
                                <input type="text" name="tipe" value="<?= htmlspecialchars($data['tipe']) ?>" required>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>Warna</label>
                                <input type="text" name="warna" value="<?= htmlspecialchars($data['warna']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Tahun Pembuatan</label>
                                <input type="number" name="tahun_pembuatan" value="<?= $data['tahun_pembuatan'] ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Kapasitas (CC)</label>
                                <input type="number" name="cc_kendaraan" value="<?= $data['cc_kendaraan'] ?>" required>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>Nomor Mesin</label>
                                <input type="text" name="no_mesin" value="<?= htmlspecialchars($data['no_mesin']) ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Nomor Rangka</label>
                                <input type="text" name="no_rangka" value="<?= htmlspecialchars($data['no_rangka']) ?>" required>
                            </div>
                        </div>

                        <div style="display: flex; gap: 1rem; margin-top: 1.5rem;">
                            <button type="submit" class="btn-submit" style="margin: 0; background: #2563eb;">Simpan Perubahan</button>
                            <a href="kendaraan_index.php" style="padding: 0.9rem 1.5rem; background: #64748b; color: white; border-radius: 8px; text-decoration: none; font-weight: 600; text-align: center;">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>