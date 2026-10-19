<?php
session_start();

// Jika session admin sudah aktif, langsung lempar ke dashboard admin
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: dashboard.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Petugas Admin - E-Samsat</title>
    <link rel="stylesheet" href="/PAJAK_KENDARAAN/assets/css/style.css">
</head>
<body>

    <div class="login-wrapper">
        <div class="login-container">
            <div style="text-align: center; margin-bottom: 0.5rem;">
                <span style="background-color: #1e293b; color: #60a5fa; padding: 4px 12px; border-radius: 20px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 1px;">Portal Petugas</span>
            </div>
            <h2>Login Admin Samsat</h2>
            <p class="subtitle">Masuk ke panel pengelola data pajak</p>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger">
                    <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <!-- Form Khusus Admin -->
            <form action="proses_login.php" method="POST">
                <div class="form-group">
                    <label for="username">Username / Email Admin</label>
                    <input type="text" id="username" name="username" placeholder="Masukkan username admin" required>
                </div>

                <div class="form-group">
                    <label for="password">Password Admin</label>
                    <input type="password" id="password" name="password" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn-submit" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">Masuk Panel Admin</button>
            </form>

            <div style="text-align: center; margin-top: 1.5rem; font-size: 0.85rem;">
                <a href="../user/login.php" style="color: #3b82f6; text-decoration: none; font-weight: 600;">&larr; Login sebagai Wajib Pajak (User)</a>
            </div>
        </div>
    </div>

</body>
</html>