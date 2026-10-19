-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 19 Okt 2026 pada 07.40
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_pajak_kendaraan_online`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `nip` varchar(20) NOT NULL,
  `nama_petugas` varchar(100) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `level` enum('superadmin','petugas') NOT NULL DEFAULT 'petugas',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `nip`, `nama_petugas`, `username`, `email`, `password`, `level`, `created_at`) VALUES
(1, '199001012023011001', 'Super Admin Samsat', 'admin_samsat', 'admin@samsat.go.id', '$2y$10$9d1Ajoq48Q/ulRLkryD7D.t3LBjV1/SZXQJvLklMpkA3PbPHMyAuO', 'superadmin', '2026-09-30 13:24:05'),
(2, '2008', 'habibi', 'admin_samsat2', 'mhmmdfaisal602@gmail.com', '$2y$10$GBYjdkh5T/spHWzliQPRje5y0q0rhe1UM/L6imexMooyI2dVUbZEi', 'petugas', '2026-10-08 15:03:21'),
(3, '2009', 'Habibi', 'admin_habibi', 'petugasHabibi@gmail.com', '$2y$10$SNRE2Es9GVzX8cY/zNu5E.nUWdUk60Iqq52htSjaplYwgiwpm3dw.', 'petugas', '2026-10-09 00:21:39');

-- --------------------------------------------------------

--
-- Struktur dari tabel `dokumen_pengajuan`
--

CREATE TABLE `dokumen_pengajuan` (
  `id_dokumen` int(11) NOT NULL,
  `id_tagihan` int(11) NOT NULL,
  `file_ktp` varchar(255) NOT NULL,
  `file_stnk` varchar(255) NOT NULL,
  `status_verifikasi` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `catatan_admin` text DEFAULT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kendaraan`
--

CREATE TABLE `kendaraan` (
  `id_kendaraan` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `no_plat` varchar(12) NOT NULL,
  `no_rangka` varchar(50) NOT NULL,
  `no_mesin` varchar(50) NOT NULL,
  `merk` varchar(50) NOT NULL,
  `tipe` varchar(50) NOT NULL,
  `jenis_kendaraan` enum('motor','mobil','truk','bus') NOT NULL,
  `tahun_pembuatan` year(4) NOT NULL,
  `cc_kendaraan` int(5) NOT NULL,
  `warna` varchar(30) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kendaraan`
--

INSERT INTO `kendaraan` (`id_kendaraan`, `id_user`, `no_plat`, `no_rangka`, `no_mesin`, `merk`, `tipe`, `jenis_kendaraan`, `tahun_pembuatan`, `cc_kendaraan`, `warna`, `created_at`) VALUES
(2, 2, 'DA 2872 BU', '2342423432', '34241424214', 'Honda', 'scoopy', 'motor', '2017', 155, 'HITAM', '2026-10-08 14:31:28'),
(4, 2, 'DA 2872 HA', '32443432', '43243324', 'Honda', 'PCX', 'motor', '2026', 160, 'HITAM', '2026-10-09 02:33:33'),
(5, 4, 'DA 2872 BL', '0897665', '3546789', 'Honda', 'SCOOPY', 'motor', '2027', 160, 'HITAM', '2026-10-15 13:17:10'),
(6, 2, 'DA 2872 HJ', '4567', '878567', 'TOYOTA', 'ALPARD', 'mobil', '2027', 500, 'HITAM', '2026-10-15 13:50:53'),
(7, 3, 'B 2872 HJ', '543', '243567', 'Honda', 'HRV', 'mobil', '2027', 500, 'KHAKI', '2026-10-15 13:51:45'),
(8, 4, 'B 2826 HJ', '5467', '098725', 'Honda', 'Brio', 'mobil', '2027', 500, 'Merah', '2026-10-15 13:52:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pajak_tagihan`
--

CREATE TABLE `pajak_tagihan` (
  `id_tagihan` int(11) NOT NULL,
  `id_kendaraan` int(11) NOT NULL,
  `kode_bayar` varchar(20) NOT NULL,
  `tahun_pajak` year(4) NOT NULL,
  `tgl_jatuh_tempo` date NOT NULL,
  `nominal_pkb` decimal(12,2) NOT NULL,
  `nominal_swdkllj` decimal(12,2) NOT NULL,
  `biaya_adm` decimal(12,2) NOT NULL DEFAULT 0.00,
  `nominal_denda` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_bayar` decimal(12,2) NOT NULL,
  `status_tagihan` enum('unpaid','pending','paid','expired') NOT NULL DEFAULT 'unpaid',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pajak_tagihan`
--

INSERT INTO `pajak_tagihan` (`id_tagihan`, `id_kendaraan`, `kode_bayar`, `tahun_pajak`, `tgl_jatuh_tempo`, `nominal_pkb`, `nominal_swdkllj`, `biaya_adm`, `nominal_denda`, `total_bayar`, `status_tagihan`, `created_at`) VALUES
(1, 2, 'BYR202610084852', '2026', '2027-10-08', 30000.00, 35000.00, 25000.00, 0.00, 90000.00, 'paid', '2026-10-08 14:53:18'),
(3, 2, 'BYR202610099544', '2026', '2027-10-09', 10000.00, 35000.00, 25000.00, 0.00, 70000.00, 'paid', '2026-10-09 02:13:46'),
(4, 2, 'BYR202610097917', '2026', '2027-10-09', 100000.00, 35000.00, 25000.00, 0.00, 160000.00, 'paid', '2026-10-09 02:25:35'),
(5, 2, 'BYR202610094690', '2026', '2027-10-09', 1400000.00, 35000.00, 25000.00, 0.00, 1460000.00, 'paid', '2026-10-09 02:29:56'),
(6, 4, 'BYR202610095229', '2026', '2027-10-09', 16000.00, 35000.00, 25000.00, 0.00, 76000.00, 'paid', '2026-10-09 02:33:57'),
(7, 5, 'BYR202610157940', '2027', '2028-10-15', 18000000.00, 35000.00, 25000.00, 0.00, 18060000.00, 'paid', '2026-10-15 13:17:56'),
(8, 5, 'BYR202610155566', '2024', '2024-10-15', 1000000.00, 35000.00, 25000.00, 48.00, 1060048.00, 'paid', '2026-10-15 13:40:16'),
(9, 8, 'BYR202610151147', '2026', '2027-10-15', 12000000.00, 143000.00, 50000.00, 0.00, 12193000.00, 'paid', '2026-10-15 13:52:42'),
(10, 6, 'BYR202610151628', '2026', '2027-10-15', 180000000.00, 143000.00, 50000.00, 0.00, 180193000.00, 'paid', '2026-10-15 13:52:53'),
(11, 6, 'BYR202610154398', '2026', '2027-10-15', 13400000.00, 143000.00, 50000.00, 0.00, 13593000.00, 'paid', '2026-10-15 13:53:06'),
(12, 5, 'BYR202610153664', '2026', '2027-10-15', 3000000.00, 35000.00, 25000.00, 0.00, 3060000.00, 'paid', '2026-10-15 13:53:15'),
(13, 2, 'BYR202610152082', '2026', '2027-10-15', 160000000.00, 35000.00, 25000.00, 0.00, 160060000.00, 'paid', '2026-10-15 13:53:25'),
(14, 4, 'BYR202610151410', '2026', '2027-10-15', 1400000.00, 35000.00, 25000.00, 0.00, 1460000.00, 'paid', '2026-10-15 13:56:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id_pembayaran` int(11) NOT NULL,
  `id_tagihan` int(11) NOT NULL,
  `metode_pembayaran` varchar(50) NOT NULL,
  `bukti_transfer` varchar(255) DEFAULT NULL,
  `tgl_pembayaran` datetime DEFAULT NULL,
  `status_konfirmasi` enum('waiting','verified','rejected') NOT NULL DEFAULT 'waiting',
  `verified_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pembayaran`
--

INSERT INTO `pembayaran` (`id_pembayaran`, `id_tagihan`, `metode_pembayaran`, `bukti_transfer`, `tgl_pembayaran`, `status_konfirmasi`, `verified_by`, `created_at`) VALUES
(1, 1, 'qris', 'bukti_1791471228_559.png', '2026-10-08 16:53:48', '', 1, '2026-10-08 14:53:48'),
(3, 3, 'transfer_bca', 'bukti_1791512061_239.png', '2026-10-09 04:14:21', '', 3, '2026-10-09 02:14:21'),
(4, 4, 'qris', 'bukti_1791512754_375.png', '2026-10-09 04:25:54', '', 2, '2026-10-09 02:25:54'),
(5, 5, 'transfer_mandiri', 'bukti_1791513012_112.png', '2026-10-09 04:30:12', '', 2, '2026-10-09 02:30:12'),
(6, 6, 'qris', 'bukti_1791513260_253.png', '2026-10-09 04:34:20', '', 1, '2026-10-09 02:34:20'),
(7, 7, 'qris', 'bukti_1792070295_714.png', '2026-10-15 15:18:15', '', 2, '2026-10-15 13:18:15'),
(8, 8, 'transfer_mandiri', 'bukti_1792071646_402.png', '2026-10-15 15:40:46', 'verified', 2, '2026-10-15 13:40:46'),
(9, 12, 'transfer_bri', 'bukti_1792072421_518.png', '2026-10-15 15:53:41', 'verified', 2, '2026-10-15 13:53:41'),
(10, 9, 'qris', 'bukti_1792072456_794.png', '2026-10-15 15:54:16', 'verified', 2, '2026-10-15 13:54:16'),
(11, 13, 'transfer_bca', 'bukti_1792072486_241.webp', '2026-10-15 15:54:46', 'verified', 2, '2026-10-15 13:54:46'),
(12, 10, 'transfer_bri', 'bukti_1792072524_737.webp', '2026-10-15 15:55:24', 'verified', 2, '2026-10-15 13:55:24'),
(13, 11, 'qris', 'bukti_1792072545_171.png', '2026-10-15 15:55:45', 'verified', 2, '2026-10-15 13:55:45'),
(14, 14, 'transfer_mandiri', 'bukti_1792072598_419.png', '2026-10-15 15:56:38', 'verified', 2, '2026-10-15 13:56:38');

-- --------------------------------------------------------

--
-- Struktur dari tabel `tarif_pajak`
--

CREATE TABLE `tarif_pajak` (
  `id_tarif` int(11) NOT NULL,
  `jenis_kendaraan` enum('motor','mobil','truk','bus') NOT NULL,
  `persen_pkb` decimal(4,2) NOT NULL DEFAULT 2.00,
  `swdkllj` decimal(12,2) NOT NULL,
  `biaya_adm_stnk` decimal(12,2) NOT NULL,
  `denda_per_bulan` decimal(4,2) NOT NULL DEFAULT 2.00
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `tarif_pajak`
--

INSERT INTO `tarif_pajak` (`id_tarif`, `jenis_kendaraan`, `persen_pkb`, `swdkllj`, `biaya_adm_stnk`, `denda_per_bulan`) VALUES
(1, 'motor', 2.00, 35000.00, 25000.00, 2.00),
(2, 'mobil', 2.00, 143000.00, 50000.00, 2.00),
(3, 'truk', 2.00, 163000.00, 50000.00, 2.00),
(4, 'bus', 2.00, 163000.00, 50000.00, 2.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id_user` int(11) NOT NULL,
  `nik` varchar(16) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `no_hp` varchar(15) NOT NULL,
  `alamat` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id_user`, `nik`, `nama_lengkap`, `email`, `password`, `no_hp`, `alamat`, `created_at`, `updated_at`) VALUES
(1, '3201010101010001', 'Administrator Samsat', 'admin@samsat.go.id', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1FpLqK8bSgD3wQO5Q/WjF2S0X5lP6Gu', '081234567890', 'Kantor Samsat Utama', '2026-09-30 12:35:48', '2026-09-30 12:35:48'),
(2, '3201010202020002', 'Budi Santoso', 'budi@gmail.com', '$2y$10$e0MYzXyjpJS7Pd0RVvHwHe1FpLqK8bSgD3wQO5Q/WjF2S0X5lP6Gu', '085678901234', 'Jl. Merdeka No. 12', '2026-09-30 12:35:48', '2026-09-30 12:35:48'),
(3, '123456789', 'Agung', 'agungmts123@gmai.com', '$2y$10$lsPikcv5dTUzB0AQeJJAEO5yKPxdpJIQN8I9ay2PAXVRuGiXvFkgC', '0823456798', 'kelapa gading', '2026-10-09 00:16:13', '2026-10-09 00:16:13'),
(4, '19821480124', 'Ilmi', 'ilmitaatpajak@gmai.com', '$2y$10$2TgvA43vzPsV.pUUTx1ayeozU4u8nbxOByjCmTc53nZ/f3g1i9aKq', '0812345678', 'banjarbaru', '2026-10-15 13:16:12', '2026-10-15 13:16:12');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `nip` (`nip`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indeks untuk tabel `dokumen_pengajuan`
--
ALTER TABLE `dokumen_pengajuan`
  ADD PRIMARY KEY (`id_dokumen`),
  ADD KEY `fk_dokumen_tagihan` (`id_tagihan`);

--
-- Indeks untuk tabel `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD PRIMARY KEY (`id_kendaraan`),
  ADD UNIQUE KEY `no_plat` (`no_plat`),
  ADD UNIQUE KEY `no_rangka` (`no_rangka`),
  ADD UNIQUE KEY `no_mesin` (`no_mesin`),
  ADD KEY `fk_kendaraan_user` (`id_user`);

--
-- Indeks untuk tabel `pajak_tagihan`
--
ALTER TABLE `pajak_tagihan`
  ADD PRIMARY KEY (`id_tagihan`),
  ADD UNIQUE KEY `kode_bayar` (`kode_bayar`),
  ADD KEY `fk_tagihan_kendaraan` (`id_kendaraan`);

--
-- Indeks untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id_pembayaran`),
  ADD KEY `fk_pembayaran_tagihan` (`id_tagihan`),
  ADD KEY `fk_pembayaran_admin` (`verified_by`);

--
-- Indeks untuk tabel `tarif_pajak`
--
ALTER TABLE `tarif_pajak`
  ADD PRIMARY KEY (`id_tarif`),
  ADD UNIQUE KEY `jenis_kendaraan` (`jenis_kendaraan`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `nik` (`nik`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `dokumen_pengajuan`
--
ALTER TABLE `dokumen_pengajuan`
  MODIFY `id_dokumen` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `kendaraan`
--
ALTER TABLE `kendaraan`
  MODIFY `id_kendaraan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `pajak_tagihan`
--
ALTER TABLE `pajak_tagihan`
  MODIFY `id_tagihan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id_pembayaran` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `tarif_pajak`
--
ALTER TABLE `tarif_pajak`
  MODIFY `id_tarif` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `dokumen_pengajuan`
--
ALTER TABLE `dokumen_pengajuan`
  ADD CONSTRAINT `fk_dokumen_tagihan` FOREIGN KEY (`id_tagihan`) REFERENCES `pajak_tagihan` (`id_tagihan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `kendaraan`
--
ALTER TABLE `kendaraan`
  ADD CONSTRAINT `fk_kendaraan_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pajak_tagihan`
--
ALTER TABLE `pajak_tagihan`
  ADD CONSTRAINT `fk_tagihan_kendaraan` FOREIGN KEY (`id_kendaraan`) REFERENCES `kendaraan` (`id_kendaraan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `fk_pembayaran_admin` FOREIGN KEY (`verified_by`) REFERENCES `admin` (`id_admin`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pembayaran_tagihan` FOREIGN KEY (`id_tagihan`) REFERENCES `pajak_tagihan` (`id_tagihan`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
