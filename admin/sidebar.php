<?php
// Mendapatkan nama file saat ini untuk penentuan status active menu
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">PANEL SAMSAT</div>
        <div style="font-size: 0.75rem; color: #64748b; margin-top: 0.25rem;">Sistem Pajak Online</div>
    </div>

    <ul class="sidebar-menu">
        <li class="<?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
            <a href="dashboard.php">📊 <span>Dashboard</span></a>
        </li>
        <li class="<?= (strpos($current_page, 'kendaraan') !== false) ? 'active' : '' ?>">
            <a href="kendaraan_index.php">🚗 <span>Data Kendaraan</span></a>
        </li>
        <li class="<?= (strpos($current_page, 'tarif') !== false) ? 'active' : '' ?>">
            <a href="tarif_index.php">💰 <span>Tarif & Biaya Pajak</span></a>
        </li>
        <li class="<?= (strpos($current_page, 'pembayaran') !== false) ? 'active' : '' ?>">
            <a href="pembayaran_index.php">📑 <span>Verifikasi Bayar</span></a>
        </li>
       <li class="<?= (strpos($current_page, 'user') !== false) ? 'active' : '' ?>">
           <a href="users_index.php">👥 <span>Data Wajib Pajak</span></a>
       </li>
       <li class="<?= ($current_page == 'admin_tambah.php') ? 'active' : '' ?>">
            <a href="admin_tambah.php">👮‍♂️ <span>Tambah Admin</span></a>
        </li>
        <li class="<?= (strpos($current_page, 'laporan') !== false) ? 'active' : '' ?>">
            <a href="laporan_transaksi.php">📈 <span>Laporan Transaksi</span></a>
        </li>
    </ul>

    <div style="margin-top: auto; padding: 1.5rem; border-top: 1px solid #1e293b; font-size: 0.8rem; color: #64748b;">
        Login sebagai:<br>
        <strong style="color: #e2e8f0;"><?= htmlspecialchars($_SESSION['admin_nama'] ?? 'Admin') ?></strong>
    </div>
</div>