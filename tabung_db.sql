-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 23, 2025 at 05:00 PM
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
(3, 1, 'Investasi', 'Pemasukan'),
(4, 1, 'Makanan & Minuman', 'Pengeluaran'),
(5, 1, 'Transportasi', 'Pengeluaran'),
(6, 1, 'Tagihan', 'Pengeluaran'),
(7, 1, 'Hiburan', 'Pengeluaran'),
(8, 1, 'Belanja', 'Pengeluaran'),
(9, 1, 'Kesehatan', 'Pengeluaran'),
(10, 1, 'Pendidikan', 'Pengeluaran');

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
(1, 'Izzat Fakhar', 'izzat@example.com', '$2y$10$uW0swpVFTcBFdryGYEaid.Hq4SFK3e2UDjD6GjBEGBaSOEc15pH3O', '2025-11-16 17:27:22');

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
(6, 1, '93337296deb5dd3b6e2615f5b19e3d96a3ee4ce81d83d2fd52cd8972f6a45405', '2025-11-23 15:52:53');

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
(3, 1, 5, 25000.00, 'Gojek ke kantor', '2025-11-02', '2025-11-02 00:30:00'),
(4, 1, 7, 150000.00, 'Nonton bioskop', '2025-11-02', '2025-11-02 12:00:00'),
(5, 1, 4, 75000.00, 'Makan malam', '2025-11-03', '2025-11-03 13:00:00'),
(6, 1, 6, 350000.00, 'Bayar tagihan internet', '2025-11-04', '2025-11-04 02:00:00'),
(7, 1, 8, 250000.00, 'Beli baju baru', '2025-11-05', '2025-11-05 09:00:00'),
(8, 1, 2, 1000000.00, 'Bonus project', '2025-11-05', '2025-11-05 10:00:00'),
(9, 1, 4, 45000.00, 'Ngopi sore', '2025-11-06', '2025-11-06 09:30:00'),
(10, 1, 9, 200000.00, 'Beli obat', '2025-11-07', '2025-11-07 04:00:00');

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
(1, 1, 'Sony WH-1000XM5', 4500000.00, 'Tinggi', 'Aktif', '2025-10-01 03:00:00'),
(2, 1, 'Laptop Gaming Baru', 25000000.00, 'Tinggi', 'Aktif', '2025-09-15 04:30:00'),
(3, 1, 'Sepatu Lari Nike', 1800000.00, 'Sedang', 'Aktif', '2025-10-10 07:00:00'),
(4, 1, 'Kursi Gaming', 3200000.00, 'Rendah', 'Selesai', '2025-10-05 02:00:00'),
(5, 1, 'Smartphone Flagship', 15000000.00, 'Tinggi', 'Aktif', '2025-10-20 11:00:00'),
(6, 1, 'Liburan ke Bali', 8000000.00, 'Sedang', 'Aktif', '2025-08-20 05:00:00'),
(7, 1, 'Jam Tangan G-Shock', 2500000.00, 'Rendah', 'Selesai', '2025-07-01 13:00:00'),
(8, 1, 'Monitor Ultrawide', 6000000.00, 'Tinggi', 'Selesai', '2025-11-01 04:00:00'),
(9, 1, 'Kamera Mirrorless', 12500000.00, 'Sedang', 'Aktif', '2025-11-05 08:00:00');

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
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `share_links`
--
ALTER TABLE `share_links`
  MODIFY `id_share` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `wishlist`
--
ALTER TABLE `wishlist`
  MODIFY `id_wishlist` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
