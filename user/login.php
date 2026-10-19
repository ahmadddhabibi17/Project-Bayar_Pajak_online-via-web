<?php
session_start();

// Jika session user sudah aktif, langsung lempar ke dashboard user
if (isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Wajib Pajak - E-Samsat</title>
    <link rel="stylesheet" href="/PAJAK_KENDARAAN/assets/css/style.css">
</head>
<body>

    <div class="login-wrapper">
        <div class="login-container">
            <div style="text-align: center; margin-bottom: 0.5rem;">
                <span style="background-color: #eff6ff; color: #2563eb; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Portal Masyarakat</span>
            </div>
            <h2>Login Wajib Pajak</h2>
            <p class="subtitle">Masuk untuk melihat & membayar pajak kendaraan Anda</p>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <!-- Form Khusus User / Wajib Pajak -->
            <form action="proses_login.php" method="POST">
                <div class="form-group">
                    <label for="username">NIK / Email Wajib Pajak</label>
                    <input type="text" id="username" name="username" placeholder="Contoh: budi@gmail.com atau NIK" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-submit">Masuk Layanan</button>
            </form>

            <!-- Tautan Pindah Portal (Hanya Navigasi Halaman, Tanpa Submit Form) -->
            <div style="text-align: center; margin-top: 1.5rem; font-size: 0.85rem;">
                <a href="../admin/login.php" style="color: #64748b; text-decoration: none; font-weight: 600;">Anda Petugas? Login Admin &rarr;</a>
            </div>
            <div style="text-align: center; margin-top: 1rem; font-size: 0.85rem;">
                Belum punya akun? <a href="register.php" style="color: #2563eb; font-weight: 600;">Daftar Akun Baru</a>
         </div>
        </div>
    </div>

</body>
</html>