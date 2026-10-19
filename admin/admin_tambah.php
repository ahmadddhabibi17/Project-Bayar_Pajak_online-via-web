<?php
session_start();
include '../config/koneksi.php';

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

$pesan = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nip          = mysqli_real_escape_string($koneksi, trim($_POST['nip']));
    $nama_petugas = mysqli_real_escape_string($koneksi, trim($_POST['nama_petugas']));
    $username     = mysqli_real_escape_string($koneksi, trim($_POST['username']));
    $email        = mysqli_real_escape_string($koneksi, trim($_POST['email']));
    $level        = mysqli_real_escape_string($koneksi, $_POST['level']);
    $password     = trim($_POST['password']);

    // Cek username atau NIP
    $cek = mysqli_query($koneksi, "SELECT id_admin FROM admin WHERE username = '$username' OR nip = '$nip'");
    if (mysqli_num_rows($cek) > 0) {
        $pesan = "Username atau NIP sudah terdaftar!";
    } else {
        $hash_password = password_hash($password, PASSWORD_BCRYPT);

        $query = "INSERT INTO admin (nip, nama_petugas, username, email, password, level) 
                  VALUES ('$nip', '$nama_petugas', '$username', '$email', '$hash_password', '$level')";

        if (mysqli_query($koneksi, $query)) {
            header("Location: dashboard.php?status=admin_tambah_sukses");
            exit();
        } else {
            $pesan = "Gagal menambahkan admin: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Petugas Admin - Samsat</title>
    <link rel="stylesheet" href="../assets/css/style.css?v=<?= time() ?>">
</head>
<body>

    <div class="admin-wrapper">
        <?php include 'sidebar.php'; ?>

        <div class="admin-content">
            <div class="admin-header">
                <h3 style="margin:0; font-weight:700; color:#0f172a;">Tambah Petugas Admin Baru</h3>
                <a href="dashboard.php" style="background: #64748b; color: white; padding: 0.5rem 1rem; border-radius: 8px; text-decoration: none; font-size: 0.85rem;">&larr; Kembali</a>
            </div>

            <div style="padding: 2rem;">
                <div class="card-detail" style="max-width: 650px; margin: 0 auto;">
                    <h3>👮‍♂️ Form Pendaftaran Petugas Samsat</h3>
                    
                    <?php if (!empty($pesan)): ?>
                        <div class="alert alert-danger" style="margin-top: 1rem;"><?= $pesan ?></div>
                    <?php endif; ?>

                    <form action="" method="POST" style="margin-top: 1.5rem;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>NIP Petugas</label>
                                <input type="text" name="nip" placeholder="1990xxxx..." required>
                            </div>
                            <div class="form-group">
                                <label>Nama Petugas</label>
                                <input type="text" name="nama_petugas" placeholder="Nama Lengkap & Gelar" required>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>Username Admin</label>
                                <input type="text" name="username" placeholder="username_samsat" required>
                            </div>
                            <div class="form-group">
                                <label>Email Office</label>
                                <input type="email" name="email" placeholder="petugas@samsat.go.id" required>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                            <div class="form-group">
                                <label>Level Akses</label>
                                <select name="level" style="width:100%; padding: 0.85rem; border-radius:8px; border:1px solid #e2e8f0;" required>
                                    <option value="petugas">Petugas Verifikasi</option>
                                    <option value="superadmin">Super Admin</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" name="password" placeholder="••••••••" required>
                            </div>
                        </div>

                        <div style="margin-top: 1.5rem;">
                            <button type="submit" class="btn-submit" style="margin: 0; background: #0f172a; width: 100%;">Simpan Akun Admin</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</body>
</html>