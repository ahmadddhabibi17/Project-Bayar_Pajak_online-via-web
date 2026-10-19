<?php
session_start();
include '../config/koneksi.php';

$pesan_error = "";
$pesan_sukses = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nik          = mysqli_real_escape_string($koneksi, trim($_POST['nik']));
    $nama_lengkap = mysqli_real_escape_string($koneksi, trim($_POST['nama_lengkap']));
    $email        = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $no_hp        = mysqli_real_escape_string($koneksi, trim($_POST['no_hp']));
    $alamat       = mysqli_real_escape_string($koneksi, trim($_POST['alamat']));
    $password     = trim($_POST['password']);

    // Cek apakah NIK atau Email sudah terdaftar
    $cek = mysqli_query($koneksi, "SELECT id_user FROM users WHERE nik = '$nik' OR email = '$email'");
    if (mysqli_num_rows($cek) > 0) {
        $pesan_error = "NIK atau Email sudah terdaftar!";
    } else {
        // Enkripsi password
        $hash_password = password_hash($password, PASSWORD_BCRYPT);

        $query = "INSERT INTO users (nik, nama_lengkap, email, no_hp, alamat, password) 
                  VALUES ('$nik', '$nama_lengkap', '$email', '$no_hp', '$alamat', '$hash_password')";

        if (mysqli_query($koneksi, $query)) {
            $pesan_sukses = "Registrasi berhasil! Silakan login.";
        } else {
            $pesan_error = "Gagal mendaftar: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrasi Wajib Pajak - E-Samsat</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
</head>
<body>

    <div class="login-wrapper">
        <div class="login-container" style="max-width: 500px;">
            <div style="text-align: center; margin-bottom: 0.5rem;">
                <span style="background-color: #eff6ff; color: #2563eb; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase;">Pendaftaran Akun</span>
            </div>
            <h2>Daftar Wajib Pajak</h2>
            <p class="subtitle">Buat akun baru untuk mengelola pajak kendaraan Anda</p>

            <?php if (!empty($pesan_error)): ?>
                <div class="alert alert-danger"><?= $pesan_error ?></div>
            <?php endif; ?>

            <?php if (!empty($pesan_sukses)): ?>
                <div class="alert alert-success" style="background: #ecfdf5; color: #065f46; padding: 0.85rem; border-radius: 8px; margin-bottom: 1rem; font-weight: 600; text-align: center;">
                    <?= $pesan_sukses ?> <br><a href="login.php" style="color: #2563eb; text-decoration: underline;">Klik di sini untuk Login</a>
                </div>
            <?php else: ?>

            <form action="" method="POST">
                <div class="form-group">
                    <label>NIK (Sesuai KTP)</label>
                    <input type="text" name="nik" placeholder="16 digit NIK" required maxLength="16">
                </div>

                <div class="form-group">
                    <label>Nama Lengkap</label>
                    <input type="text" name="nama_lengkap" placeholder="Nama sesuai KTP" required>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label>Email</label>
                        <input type="email" name="email" placeholder="contoh@gmail.com" required>
                    </div>
                    <div class="form-group">
                        <label>No. HP / WhatsApp</label>
                        <input type="text" name="no_hp" placeholder="08xxxxxxxxxx" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Alamat Lengkap</label>
                    <input type="text" name="alamat" placeholder="Alamat rumah" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-submit">Daftar Akun Baru</button>
            </form>

            <?php endif; ?>

            <div style="text-align: center; margin-top: 1.5rem; font-size: 0.85rem;">
                Sudah punya akun? <a href="login.php" style="color: #2563eb; font-weight: 600; text-decoration: none;">Login di sini</a>
            </div>
        </div>
    </div>

</body>
</html>