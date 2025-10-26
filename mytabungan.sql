-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 26, 2025 at 08:08 AM
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
-- Database: `mytabungan`
--

-- --------------------------------------------------------

--
-- Table structure for table `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL,
  `id_pengguna` int(11) NOT NULL,
  `nama_kategori` varchar(100) NOT NULL,
  `tipe` enum('Pemasukan','Pengeluaran') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `id_pengguna`, `nama_kategori`, `tipe`) VALUES
(63, 1, 'Gaji', 'Pemasukan'),
(64, 1, 'Bonus', 'Pemasukan'),
(65, 1, 'Freelance', 'Pemasukan'),
(66, 1, 'Makanan & Minuman', 'Pengeluaran'),
(67, 1, 'Transportasi', 'Pengeluaran'),
(68, 1, 'Tagihan', 'Pengeluaran'),
(69, 1, 'Belanja', 'Pengeluaran'),
(70, 1, 'Hiburan', 'Pengeluaran'),
(71, 2, 'Gaji', 'Pemasukan'),
(72, 2, 'Bonus', 'Pemasukan'),
(73, 2, 'Freelance', 'Pemasukan'),
(74, 2, 'Makanan & Minuman', 'Pengeluaran'),
(75, 2, 'Transportasi', 'Pengeluaran'),
(76, 2, 'Tagihan', 'Pengeluaran'),
(77, 2, 'Belanja', 'Pengeluaran'),
(78, 2, 'Hiburan', 'Pengeluaran');

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `nama_lengkap` varchar(100) NOT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`id_pengguna`, `username`, `password`, `nama_lengkap`, `dibuat_pada`) VALUES
(1, 'budi', '$2y$10$/IpgwxAhy0kWczHZuGqvLeaF/aGNRSHs5tX0lphOIrv5H3NMiHfhO', 'Budi Hartono', '2025-10-26 06:40:26'),
(2, 'ijat', '$2y$10$kYg3ofD1cvU8FigUynXkvOPF3TfS7l3e6d8iDWXbca9f0rpKPZcNS', 'ijat', '2025-10-26 06:46:32');

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
(86, 1, 63, 7000000.00, 'Gaji bulanan', '2025-09-28', '2025-10-26 07:07:30'),
(87, 1, 65, 1500000.00, 'Proyek desain web', '2025-10-11', '2025-10-26 07:07:30'),
(88, 1, 64, 500000.00, 'Bonus kinerja', '2025-10-21', '2025-10-26 07:07:30'),
(89, 1, 68, 450000.00, 'Bayar internet & listrik', '2025-09-29', '2025-10-26 07:07:30'),
(90, 1, 67, 250000.00, 'Isi bensin mobil', '2025-10-01', '2025-10-26 07:07:30'),
(91, 1, 66, 75000.00, 'Makan siang di luar', '2025-10-04', '2025-10-26 07:07:30'),
(92, 1, 69, 300000.00, 'Belanja bulanan', '2025-10-06', '2025-10-26 07:07:30'),
(93, 1, 70, 120000.00, 'Nonton bioskop', '2025-10-08', '2025-10-26 07:07:30'),
(94, 1, 66, 45000.00, 'Ngopi sore', '2025-10-14', '2025-10-26 07:07:30'),
(95, 1, 67, 50000.00, 'Parkir & tol', '2025-10-18', '2025-10-26 07:07:30'),
(96, 1, 69, 150000.00, 'Beli kado ulang tahun', '2025-10-23', '2025-10-26 07:07:30'),
(97, 1, 66, 200000.00, 'Makan malam keluarga', '2025-10-25', '2025-10-26 07:07:30'),
(98, 2, 71, 7000000.00, 'Gaji bulanan', '2025-09-28', '2025-10-26 07:07:42'),
(99, 2, 73, 1500000.00, 'Proyek desain web', '2025-10-11', '2025-10-26 07:07:42'),
(100, 2, 72, 500000.00, 'Bonus kinerja', '2025-10-21', '2025-10-26 07:07:42'),
(101, 2, 76, 450000.00, 'Bayar internet & listrik', '2025-09-29', '2025-10-26 07:07:42'),
(102, 2, 75, 250000.00, 'Isi bensin mobil', '2025-10-01', '2025-10-26 07:07:42'),
(103, 2, 74, 75000.00, 'Makan siang di luar', '2025-10-04', '2025-10-26 07:07:42'),
(104, 2, 77, 300000.00, 'Belanja bulanan', '2025-10-06', '2025-10-26 07:07:42'),
(105, 2, 78, 120000.00, 'Nonton bioskop', '2025-10-08', '2025-10-26 07:07:42'),
(106, 2, 74, 45000.00, 'Ngopi sore', '2025-10-14', '2025-10-26 07:07:42'),
(107, 2, 75, 50000.00, 'Parkir & tol', '2025-10-18', '2025-10-26 07:07:42'),
(108, 2, 77, 150000.00, 'Beli kado ulang tahun', '2025-10-23', '2025-10-26 07:07:42'),
(109, 2, 74, 200000.00, 'Makan malam keluarga', '2025-10-25', '2025-10-26 07:07:42');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`),
  ADD UNIQUE KEY `unik_kategori_pengguna` (`id_pengguna`,`nama_kategori`),
  ADD KEY `id_pengguna` (`id_pengguna`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`id_pengguna`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id_transaksi`),
  ADD KEY `id_pengguna` (`id_pengguna`),
  ADD KEY `id_kategori` (`id_kategori`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `pengguna`
--
ALTER TABLE `pengguna`
  MODIFY `id_pengguna` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id_transaksi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=110;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kategori`
--
ALTER TABLE `kategori`
  ADD CONSTRAINT `kategori_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
