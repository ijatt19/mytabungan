-- Nama Database: mytabungan
-- Deskripsi: Script untuk membuat ulang struktur database dan mengisi data dummy.

-- Mengatur zona waktu server ke Waktu Indonesia Barat (WIB)
SET time_zone = '+07:00';

-- Menghapus tabel yang ada dengan urutan yang benar untuk menghindari error foreign key
DROP TABLE IF EXISTS `transaksi`;
DROP TABLE IF EXISTS `wishlist`;
DROP TABLE IF EXISTS `kategori`;
DROP TABLE IF EXISTS `pengguna`;

-- --------------------------------------------------------

--
-- Struktur tabel untuk `pengguna`
--
CREATE TABLE `pengguna` (
  `id_pengguna` int(11) NOT NULL AUTO_INCREMENT,
  `nama_lengkap` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_pengguna`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `pengguna`
-- Password untuk semua user adalah: "password123"
--
INSERT INTO `pengguna` (`id_pengguna`, `nama_lengkap`, `email`, `password`) VALUES
(1, 'Izzat Fakhar', 'izzat@example.com', '$2y$10$Y.MCT3V/tq9F.IIJg.ECv.L4lJp2j5sJ5u9sJ5u9sJ5u9sJ5u9sJ5');

-- --------------------------------------------------------

--
-- Struktur tabel untuk `kategori`
--
CREATE TABLE `kategori` (
  `id_kategori` int(11) NOT NULL AUTO_INCREMENT,
  `id_pengguna` int(11) NOT NULL,
  `nama_kategori` varchar(50) NOT NULL,
  `tipe` enum('Pemasukan','Pengeluaran') NOT NULL,
  PRIMARY KEY (`id_kategori`),
  KEY `id_pengguna` (`id_pengguna`),
  CONSTRAINT `kategori_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
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
-- Struktur tabel untuk `wishlist`
--
CREATE TABLE `wishlist` (
  `id_wishlist` int(11) NOT NULL AUTO_INCREMENT,
  `id_pengguna` int(11) NOT NULL,
  `nama_barang` varchar(100) NOT NULL,
  `harga` decimal(15,2) NOT NULL,
  `prioritas` enum('Tinggi','Sedang','Rendah') NOT NULL,
  `status` enum('Aktif','Selesai') NOT NULL DEFAULT 'Aktif',
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_wishlist`),
  KEY `id_pengguna` (`id_pengguna`),
  CONSTRAINT `wishlist_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `wishlist`
--
INSERT INTO `wishlist` (`id_wishlist`, `id_pengguna`, `nama_barang`, `harga`, `prioritas`, `status`, `dibuat_pada`) VALUES
(1, 1, 'Sony WH-1000XM5', 4500000.00, 'Tinggi', 'Aktif', '2025-10-01 10:00:00'),
(2, 1, 'Laptop Gaming Baru', 25000000.00, 'Tinggi', 'Aktif', '2025-09-15 11:30:00'),
(3, 1, 'Sepatu Lari Nike', 1800000.00, 'Sedang', 'Aktif', '2025-10-10 14:00:00'),
(4, 1, 'Kursi Gaming', 3200000.00, 'Rendah', 'Aktif', '2025-10-05 09:00:00'),
(5, 1, 'Smartphone Flagship', 15000000.00, 'Tinggi', 'Aktif', '2025-10-20 18:00:00'),
(6, 1, 'Liburan ke Bali', 8000000.00, 'Sedang', 'Aktif', '2025-08-20 12:00:00'),
(7, 1, 'Jam Tangan G-Shock', 2500000.00, 'Rendah', 'Selesai', '2025-07-01 20:00:00'),
(8, 1, 'Monitor Ultrawide', 6000000.00, 'Tinggi', 'Aktif', '2025-11-01 11:00:00'),
(9, 1, 'Kamera Mirrorless', 12500000.00, 'Sedang', 'Aktif', '2025-11-05 15:00:00');

-- --------------------------------------------------------

--
-- Struktur tabel untuk `transaksi`
--
CREATE TABLE `transaksi` (
  `id_transaksi` int(11) NOT NULL AUTO_INCREMENT,
  `id_pengguna` int(11) NOT NULL,
  `id_kategori` int(11) NOT NULL,
  `jumlah` decimal(15,2) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `tanggal_transaksi` date NOT NULL,
  `dibuat_pada` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id_transaksi`),
  KEY `id_pengguna` (`id_pengguna`),
  KEY `id_kategori` (`id_kategori`),
  CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_pengguna`) REFERENCES `pengguna` (`id_pengguna`) ON DELETE CASCADE,
  CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `transaksi` (untuk bulan November 2025)
--
INSERT INTO `transaksi` (`id_transaksi`, `id_pengguna`, `id_kategori`, `jumlah`, `keterangan`, `tanggal_transaksi`, `dibuat_pada`) VALUES
(1, 1, 1, 7000000.00, 'Gaji bulanan', '2025-11-01', '2025-11-01 08:00:00'),
(2, 1, 4, 50000.00, 'Makan siang', '2025-11-01', '2025-11-01 12:30:00'),
(3, 1, 5, 25000.00, 'Gojek ke kantor', '2025-11-02', '2025-11-02 07:30:00'),
(4, 1, 7, 150000.00, 'Nonton bioskop', '2025-11-02', '2025-11-02 19:00:00'),
(5, 1, 4, 75000.00, 'Makan malam', '2025-11-03', '2025-11-03 20:00:00'),
(6, 1, 6, 350000.00, 'Bayar tagihan internet', '2025-11-04', '2025-11-04 09:00:00'),
(7, 1, 8, 250000.00, 'Beli baju baru', '2025-11-05', '2025-11-05 16:00:00'),
(8, 1, 2, 1000000.00, 'Bonus project', '2025-11-05', '2025-11-05 17:00:00'),
(9, 1, 4, 45000.00, 'Ngopi sore', '2025-11-06', '2025-11-06 16:30:00'),
(10, 1, 9, 200000.00, 'Beli obat', '2025-11-07', '2025-11-07 11:00:00');

COMMIT;
