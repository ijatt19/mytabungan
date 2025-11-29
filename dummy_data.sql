-- Dummy Data for MyTabungan
-- User: Ijat (ijat@gmail.com)
-- Password: password (Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi)
-- Note: Please change password after login if needed.

INSERT INTO pengguna (nama_lengkap, email, password) VALUES ('Ijat', 'ijat@gmail.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
SET @id_pengguna = LAST_INSERT_ID();

-- Categories
INSERT INTO kategori (id_pengguna, nama_kategori, tipe) VALUES 
(@id_pengguna, 'Gaji', 'Pemasukan'),
(@id_pengguna, 'Bonus', 'Pemasukan'),
(@id_pengguna, 'Investasi', 'Pemasukan'),
(@id_pengguna, 'Freelance', 'Pemasukan'),
(@id_pengguna, 'Hadiah', 'Pemasukan'),
(@id_pengguna, 'Makanan', 'Pengeluaran'),
(@id_pengguna, 'Transportasi', 'Pengeluaran'),
(@id_pengguna, 'Hiburan', 'Pengeluaran'),
(@id_pengguna, 'Belanja', 'Pengeluaran'),
(@id_pengguna, 'Tagihan', 'Pengeluaran'),
(@id_pengguna, 'Kesehatan', 'Pengeluaran'),
(@id_pengguna, 'Pendidikan', 'Pengeluaran'),
(@id_pengguna, 'Amal', 'Pengeluaran'),
(@id_pengguna, 'Asuransi', 'Pengeluaran'),
(@id_pengguna, 'Lainnya', 'Pengeluaran');

-- Transactions (Income ~100M)
-- Assuming Category IDs are sequential from the insert above. 
-- We'll use subqueries to be safe.

INSERT INTO transaksi (id_pengguna, id_kategori, jumlah, tanggal_transaksi, keterangan) VALUES 
(@id_pengguna, (SELECT id_kategori FROM kategori WHERE nama_kategori = 'Gaji' AND id_pengguna = @id_pengguna LIMIT 1), 20000000, '2025-10-05', 'Gaji Bulan Oktober'),
(@id_pengguna, (SELECT id_kategori FROM kategori WHERE nama_kategori = 'Bonus' AND id_pengguna = @id_pengguna LIMIT 1), 50000000, '2025-10-15', 'Bonus Proyek'),
(@id_pengguna, (SELECT id_kategori FROM kategori WHERE nama_kategori = 'Investasi' AND id_pengguna = @id_pengguna LIMIT 1), 10000000, '2025-10-25', 'Dividen Saham'),
(@id_pengguna, (SELECT id_kategori FROM kategori WHERE nama_kategori = 'Gaji' AND id_pengguna = @id_pengguna LIMIT 1), 20000000, '2025-11-05', 'Gaji Bulan November'),
(@id_pengguna, (SELECT id_kategori FROM kategori WHERE nama_kategori = 'Freelance' AND id_pengguna = @id_pengguna LIMIT 1), 5000000, '2025-11-10', 'Proyek Website'),
(@id_pengguna, (SELECT id_kategori FROM kategori WHERE nama_kategori = 'Hadiah' AND id_pengguna = @id_pengguna LIMIT 1), 5000000, '2025-11-20', 'Hadiah Ulang Tahun');

-- Transactions (Expenses)
INSERT INTO transaksi (id_pengguna, id_kategori, jumlah, tanggal_transaksi, keterangan) VALUES 
(@id_pengguna, (SELECT id_kategori FROM kategori WHERE nama_kategori = 'Makanan' AND id_pengguna = @id_pengguna LIMIT 1), 500000, '2025-10-06', 'Belanja Bulanan'),
(@id_pengguna, (SELECT id_kategori FROM kategori WHERE nama_kategori = 'Transportasi' AND id_pengguna = @id_pengguna LIMIT 1), 300000, '2025-10-07', 'Bensin'),
(@id_pengguna, (SELECT id_kategori FROM kategori WHERE nama_kategori = 'Hiburan' AND id_pengguna = @id_pengguna LIMIT 1), 1000000, '2025-10-10', 'Nonton Konser'),
(@id_pengguna, (SELECT id_kategori FROM kategori WHERE nama_kategori = 'Tagihan' AND id_pengguna = @id_pengguna LIMIT 1), 1500000, '2025-10-20', 'Listrik & Air'),
(@id_pengguna, (SELECT id_kategori FROM kategori WHERE nama_kategori = 'Makanan' AND id_pengguna = @id_pengguna LIMIT 1), 600000, '2025-11-06', 'Makan Malam'),
(@id_pengguna, (SELECT id_kategori FROM kategori WHERE nama_kategori = 'Belanja' AND id_pengguna = @id_pengguna LIMIT 1), 2000000, '2025-11-15', 'Baju Baru');

-- Wishlist
INSERT INTO wishlist (id_pengguna, nama_barang, harga, prioritas, status) VALUES 
(@id_pengguna, 'iPhone 16', 20000000, 'Tinggi', 'Belum Tercapai'),
(@id_pengguna, 'MacBook Pro', 30000000, 'Tinggi', 'Belum Tercapai'),
(@id_pengguna, 'Liburan ke Jepang', 25000000, 'Sedang', 'Belum Tercapai'),
(@id_pengguna, 'Sepatu Lari', 2000000, 'Rendah', 'Selesai'),
(@id_pengguna, 'Jam Tangan', 5000000, 'Sedang', 'Belum Tercapai'),
(@id_pengguna, 'Kamera Mirrorless', 15000000, 'Tinggi', 'Belum Tercapai'),
(@id_pengguna, 'Meja Kerja', 3000000, 'Rendah', 'Selesai'),
(@id_pengguna, 'Kursi Ergonomis', 4000000, 'Rendah', 'Belum Tercapai'),
(@id_pengguna, 'Monitor 4K', 6000000, 'Sedang', 'Belum Tercapai'),
(@id_pengguna, 'PlayStation 5', 8000000, 'Sedang', 'Belum Tercapai');
