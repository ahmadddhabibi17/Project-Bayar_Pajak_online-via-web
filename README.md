# E-Samsat — Sistem Pembayaran Pajak Kendaraan Online

Aplikasi web berbasis **PHP** dan **MySQL** yang menyediakan portal terpisah untuk **Admin Samsat** dan **Wajib Pajak (User)**. Sistem ini menangani autentikasi multi-peran, pengelolaan data kendaraan, penerbitan tagihan pajak, pengunggahan bukti pembayaran, verifikasi transaksi, serta rekap laporan bulanan dengan visualisasi grafik.

## Fitur Utama

### Portal Wajib Pajak (User)
- **Registrasi & Login Akun:** Pendaftaran mandiri dengan validasi NIK dan email unik, serta login aman menggunakan `password_hash()` / `password_verify()`.
- **Dashboard Wajib Pajak:** Ringkasan kendaraan terdaftar atas akun pengguna dan daftar tagihan pajak yang masih berstatus `unpaid`.
- **Pembayaran Pajak:** Pemilihan metode pembayaran (Transfer Bank / QRIS) dan unggah bukti transfer (`JPG`, `PNG`, `PDF`).
- **Riwayat Pembayaran:** Pemantauan status verifikasi transaksi (`waiting`, `verified`, `rejected`).

### Portal Petugas / Admin Samsat
- **Dashboard Admin:** Statistik ringkas (total kendaraan terdaftar, transaksi menunggu verifikasi, tagihan menunggak) serta tabel transaksi masuk terbaru.
- **CRUD Data Kendaraan:** Tambah, lihat, ubah, dan hapus data kendaraan (`kendaraan_index.php`, `kendaraan_tambah.php`, `kendaraan_edit.php`, `kendaraan_hapus.php`).
- **Penerbitan Tagihan Pajak:** Kalkulasi otomatis PKB, SWDKLLJ, biaya administrasi STNK, dan denda keterlambatan berdasarkan jenis kendaraan (`tarif_pajak`) dan jatuh tempo.
- **Verifikasi Pembayaran:** Persetujuan atau penolakan bukti pembayaran (`waiting` → `verified` / `rejected`) yang otomatis memperbarui status tagihan (`pajak_tagihan`) menjadi `paid` saat disetujui.
- **Manajemen Akun & Tarif:** Daftar Wajib Pajak terdaftar, daftar tarif pajak, dan penambahan akun petugas admin baru.
- **Laporan Transaksi Bulanan:** Filter bulan/tahun, rekap total pendapatan transaksi terverifikasi, **Pie Chart** distribusi jenis kendaraan menggunakan **Chart.js**, serta mode cetak (`window.print()`).

## Struktur Database (`db_pajak_kendaraan_online`)

Tabel utama yang digunakan:
- `users`: Data akun Wajib Pajak (`id_user`, `nik`, `nama_lengkap`, `email`, `no_hp`, `alamat`, `password`).
- `admin`: Data akun petugas Samsat (`id_admin`, `nip`, `nama_petugas`, `username`, `email`, `password`, `level`).
- `kendaraan`: Data kendaraan (`id_kendaraan`, `id_user`, `no_plat`, `no_mesin`, `no_rangka`, `merk`, `tipe`, `warna`, `tahun_pembuatan`, `cc_kendaraan`, `jenis_kendaraan`).
- `tarif_pajak`: Referensi tarif per jenis kendaraan (`id_tarif`, `jenis_kendaraan`, `persen_pkb`, `swdkllj`, `biaya_adm_stnk`, `denda_per_bulan`).
- `pajak_tagihan`: Data tagihan pajak (`id_tagihan`, `id_kendaraan`, `kode_bayar`, `tahun_pajak`, `nominal_pkb`, `nominal_swdkllj`, `nominal_denda`, `biaya_adm`, `total_bayar`, `status_tagihan`, `tgl_jatuh_tempo`).
- `pembayaran`: Data konfirmasi pembayaran (`id_pembayaran`, `id_tagihan`, `tgl_pembayaran`, `metode_pembayaran`, `bukti_transfer`, `status_konfirmasi`, `verified_by`).
- `dokumen_pengajuan`: Dokumen pendukung pengajuan (`id_dokumen`, `id_tagihan`, `file_ktp`, `file_stnk`, `status_verifikasi`, `catatan_admin`).

## Struktur Direktori

```text
PAJAK_KENDARAAN/
├── admin/
│   ├── admin_tambah.php
│   ├── dashboard.php
│   ├── kendaraan_edit.php
│   ├── kendaraan_hapus.php
│   ├── kendaraan_index.php
│   ├── kendaraan_tambah.php
│   ├── laporan_transaksi.php
│   ├── login.php
│   ├── logout.php
│   ├── pembayaran_index.php
│   ├── sidebar.php
│   ├── tagihan_tambah.php
│   ├── tarif_index.php
│   └── users_index.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── uploads/
│       └── bukti/
├── config/
│   └── koneksi.php
├── user/
│   ├── dashboard.php
│   ├── login.php
│   ├── logout.php
│   ├── pembayaran_tambah.php
│   ├── register.php
│   └── riwayat_pembayaran.php
└── README.md
```

## Cara Instalasi & Menjalankan (XAMPP)

1. **Clone atau salin folder proyek** ke direktori `htdocs` XAMPP:
   ```bash
   git clone <URL_REPOSITORY_KAMU> C:\xampp\htdocs\PAJAK_KENDARAAN
   ```
2. **Aktifkan Apache dan MySQL** melalui XAMPP Control Panel.
3. **Buat database & impor skema:**
   - Buka `http://localhost/phpmyadmin`.
   - Buat database bernama `db_pajak_kendaraan_online`.
   - Impor file `.sql` proyek (atau buat tabel sesuai struktur di atas).
4. **Konfigurasi koneksi database:**
   - Sesuaikan host, username, password, dan nama database pada `config/koneksi.php`.
5. **Akses aplikasi di browser:**
   - Portal Wajib Pajak: `http://localhost/PAJAK_KENDARAAN/user/login.php`
   - Portal Admin Samsat: `http://localhost/PAJAK_KENDARAAN/admin/login.php`

## Teknologi yang Digunakan

- **Backend:** PHP (Native PHP dengan `mysqli`)
- **Database:** MySQL / MariaDB
- **Frontend:** HTML5, CSS3, JavaScript
- **Visualisasi Data:** Chart.js (via CDN)
- **Lingkungan Lokal:** XAMPP (Apache, PHP, MySQL)
