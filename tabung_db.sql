-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 30, 2025 at 08:09 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tabung_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `tipe` enum('Pemasukan','Pengeluaran') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `id_pengguna`, `nama_kategori`, `tipe`) VALUES
(1, 1, 'Gaji', 'Pemasukan'),
(2, 1, 'Bonus', 'Pemasukan'),
(4, 1, 'Makanan dan Minuman', 'Pengeluaran'),
(6, 1, 'Tagihan', 'Pengeluaran'),
(7, 1, 'Hiburan', 'Pengeluaran'),
(8, 1, 'Belanja', 'Pengeluaran'),
(9, 1, 'Kesehatan', 'Pengeluaran'),
(11, 1, 'Gojek', 'Pemasukan'),
(13, 1, 'ss', 'Pemasukan'),
(14, 2, 'Gaji', 'Pemasukan'),
(15, 2, 'Bonus', 'Pemasukan'),
(16, 2, 'Investasi', 'Pemasukan'),
(17, 2, 'Freelance', 'Pemasukan'),
(18, 2, 'Hadiah', 'Pemasukan'),
(19, 2, 'Makanan', 'Pengeluaran'),
(20, 2, 'Transportasi', 'Pengeluaran'),
(21, 2, 'Hiburan', 'Pengeluaran'),
(22, 2, 'Belanja', 'Pengeluaran'),
(23, 2, 'Tagihan', 'Pengeluaran'),
(24, 2, 'Kesehatan', 'Pengeluaran'),
(25, 2, 'Pendidikan', 'Pengeluaran'),
(26, 2, 'Amal', 'Pengeluaran'),
(27, 2, 'Asuransi', 'Pengeluaran'),
(28, 2, 'Lainnya', 'Pengeluaran');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `nama_lengkap`, `email`, `password`, `dibuat_pada`) VALUES
(1, 'Izzat Fakhar Assyakur', 'izzat@example.com', '$2y$10$m8l1pp9QxkrObRgLxiBQG.ZGMXPp2gmz5rq1HqfMbNQgyfDoagt06', '2025-11-16 17:27:22'),
(2, 'Ijat', 'ijat@gmail.com', '$2y$10$WKrXOpNOVFirLGCY6sWCq.HEM4KMkadrFA4v5qKDar0yrRc4M/q/O', '2025-11-29 05:52:01');

-- --------------------------------------------------------

--
-- Table structure for table `share_links`
--

CREATE TABLE `share_links` (
  `id_share` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `share_links`
--

INSERT INTO `share_links` (`id_share`, `id_pengguna`, `token`, `dibuat_pada`) VALUES
(15, 1, 'a3b29fbc7f93cfc40473c8e70ff0445bde415d65a1b6375ee05c9d775d16caad', '2025-11-29 05:38:02'),
(16, 2, 'c4092214c6c3dc3c5472a7b9f9157ca1e65abbd521680aa0e7526984529dd5c8', '2025-11-29 06:35:53');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal_transaksi` date NOT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id_transaksi`, `id_pengguna`, `id_kategori`, `jumlah`, `keterangan`, `tanggal_transaksi`, `dibuat_pada`) VALUES
(1, 1, 1, 7000000.00, 'Gaji bulanan', '2025-11-01', '2025-11-01 01:00:00'),
(2, 1, 4, 50000.00, 'Makan siang', '2025-11-01', '2025-11-01 05:30:00'),
(19, 1, 2, 100000.00, 'Uang', '2025-11-29', '2025-11-29 05:26:39'),
(20, 1, 8, 222.00, '2', '2025-11-29', '2025-11-29 05:27:00'),
(21, 1, 2, 22.00, 'rr', '2025-11-29', '2025-11-29 05:28:44'),
(22, 1, 8, 24.00, 'sd', '2025-11-29', '2025-11-29 05:28:50'),
(23, 2, 14, 20000000.00, 'Gaji Bulan Oktober', '2025-10-05', '2025-11-29 05:52:01'),
(24, 2, 15, 50000000.00, 'Bonus Proyek', '2025-10-15', '2025-11-29 05:52:01'),
(25, 2, 16, 10000000.00, 'Dividen Saham', '2025-10-25', '2025-11-29 05:52:01'),
(26, 2, 14, 20000000.00, 'Gaji Bulan November', '2025-11-05', '2025-11-29 05:52:01'),
(27, 2, 17, 5000000.00, 'Proyek Website', '2025-11-10', '2025-11-29 05:52:01'),
(28, 2, 18, 5000000.00, 'Hadiah Ulang Tahun', '2025-11-20', '2025-11-29 05:52:01'),
(29, 2, 19, 500000.00, 'Belanja Bulanan', '2025-10-06', '2025-11-29 05:52:01'),
(30, 2, 20, 300000.00, 'Bensin', '2025-10-07', '2025-11-29 05:52:01'),
(31, 2, 21, 1000000.00, 'Nonton Konser', '2025-10-10', '2025-11-29 05:52:01'),
(32, 2, 23, 1500000.00, 'Listrik & Air', '2025-10-20', '2025-11-29 05:52:01'),
(33, 2, 19, 600000.00, 'Makan Malam', '2025-11-06', '2025-11-29 05:52:01'),
(34, 2, 22, 2000000.00, 'Baju Baru', '2025-11-15', '2025-11-29 05:52:01');

-- --------------------------------------------------------

--
-- Table structure for table `wishlist`
--

CREATE TABLE `wishlist` (
  `id_wishlist` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `prioritas` enum('Tinggi','Sedang','Rendah') NOT NULL,
  `status` enum('Aktif','Selesai') NOT NULL DEFAULT 'Aktif',
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `wishlist`
--

INSERT INTO `wishlist` (`id_wishlist`, `id_pengguna`, `nama_barang`, `harga`, `prioritas`, `status`, `dibuat_pada`) VALUES
(5, 1, 'Smartphone Flagship', 15000000.00, 'Tinggi', 'Selesai', '2025-10-20 11:00:00'),
(9, 1, 'Kamera Mirrorless', 12500000.00, 'Sedang', 'Selesai', '2025-11-05 08:00:00'),
(17, 1, 'Liburan Jepang', 50000000.00, 'Sedang', 'Aktif', '2025-11-29 05:19:09'),
(18, 1, 'Kawin', 50000000.00, 'Sedang', 'Aktif', '2025-11-29 05:19:50'),
(19, 1, '222', 222.00, 'Sedang', 'Aktif', '2025-11-29 05:27:12'),
(20, 1, 'ew', 21.00, 'Sedang', 'Aktif', '2025-11-29 05:28:55'),
(21, 2, 'iPhone 16', 20000000.00, 'Tinggi', '', '2025-11-29 05:52:01'),
(22, 2, 'MacBook Pro', 30000000.00, 'Tinggi', '', '2025-11-29 05:52:01'),
(23, 2, 'Liburan ke Jepang', 25000000.00, 'Sedang', '', '2025-11-29 05:52:01'),
(24, 2, 'Sepatu Lari', 2000000.00, 'Rendah', 'Selesai', '2025-11-29 05:52:01'),
(25, 2, 'Jam Tangan', 5000000.00, 'Sedang', '', '2025-11-29 05:52:01'),
(26, 2, 'Kamera Mirrorless', 15000000.00, 'Tinggi', '', '2025-11-29 05:52:01'),
(27, 2, 'Meja Kerja', 3000000.00, 'Rendah', 'Selesai', '2025-11-29 05:52:01'),
(28, 2, 'Kursi Ergonomis', 4000000.00, 'Rendah', '', '2025-11-29 05:52:01'),
(29, 2, 'Monitor 4K', 6000000.00, 'Sedang', '', '2025-11-29 05:52:01'),
(30, 2, 'PlayStation 5', 8000000.00, 'Sedang', '', '2025-11-29 05:52:01'),
(31, 2, 'Palisade', 500000000.00, 'Rendah', 'Aktif', '2025-11-29 05:54:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `share_links`
--
ALTER TABLE `share_links`
  ADD PRIMARY KEY (`id_share`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_pengguna` (`id_pengguna`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- Indexes for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD PRIMARY KEY (`id_wishlist`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `share_links`
--
ALTER TABLE `share_links`
  MODIFY `id_share` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=35;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id_wishlist` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kategori`
--
ALTER TABLE `kategori`
  ADD CONSTRAINT `kategori_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE;

--
-- Constraints for table `share_links`
--
ALTER TABLE `share_links`
  ADD CONSTRAINT `share_links_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE,
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE;

--
-- Constraints for table `wishlist`
--
ALTER TABLE `wishlist`
  ADD CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
