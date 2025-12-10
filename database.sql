-- =====================================================
-- MyTabungan Database Schema
-- Personal Finance Management Application
-- =====================================================

-- Create database
CREATE DATABASE IF NOT EXISTS mytabungan CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mytabungan;

-- =====================================================
-- Table: pengguna (Users)
-- =====================================================
CREATE TABLE pengguna (
    id_pengguna INT AUTO_INCREMENT PRIMARY KEY,
    nama VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Table: kategori (Categories)
-- =====================================================
CREATE TABLE kategori (
    id_kategori INT AUTO_INCREMENT PRIMARY KEY,
    id_pengguna INT NULL COMMENT 'NULL for default categories',
    nama_kategori VARCHAR(100) NOT NULL,
    tipe ENUM('Pemasukan','Pengeluaran') NOT NULL,
    icon VARCHAR(50) DEFAULT 'bi-tag',
    warna VARCHAR(20) DEFAULT '#10b981',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Foreign key for kategori
ALTER TABLE kategori 
ADD CONSTRAINT fk_kategori_pengguna 
FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna) ON DELETE CASCADE;

-- =====================================================
-- Table: transaksi (Transactions)
-- =====================================================
CREATE TABLE transaksi (
    id_transaksi INT AUTO_INCREMENT PRIMARY KEY,
    id_pengguna INT NOT NULL,
    id_kategori INT NOT NULL,
    jumlah DECIMAL(15,2) NOT NULL,
    tanggal DATE NOT NULL,
    keterangan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Foreign keys for transaksi
ALTER TABLE transaksi 
ADD CONSTRAINT fk_transaksi_pengguna 
FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna) ON DELETE CASCADE;

ALTER TABLE transaksi 
ADD CONSTRAINT fk_transaksi_kategori 
FOREIGN KEY (id_kategori) REFERENCES kategori(id_kategori) ON DELETE CASCADE;

-- Index for faster queries
CREATE INDEX idx_transaksi_tanggal ON transaksi(tanggal);
CREATE INDEX idx_transaksi_pengguna ON transaksi(id_pengguna);

-- =====================================================
-- Table: wishlist (Savings Goals)
-- =====================================================
CREATE TABLE wishlist (
    id_wishlist INT AUTO_INCREMENT PRIMARY KEY,
    id_pengguna INT NOT NULL,
    nama_barang VARCHAR(200) NOT NULL,
    harga DECIMAL(15,2) NOT NULL,
    prioritas ENUM('Rendah','Sedang','Tinggi') DEFAULT 'Sedang',
    status ENUM('Belum','Tercapai') DEFAULT 'Belum',
    tabungan_terkumpul DECIMAL(15,2) DEFAULT 0,
    target_date DATE NULL,
    catatan TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Foreign key for wishlist
ALTER TABLE wishlist 
ADD CONSTRAINT fk_wishlist_pengguna 
FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna) ON DELETE CASCADE;

-- =====================================================
-- Share Token Table (for financial sharing)
-- =====================================================
CREATE TABLE IF NOT EXISTS share_token (
    id_share INT AUTO_INCREMENT PRIMARY KEY,
    id_pengguna INT NOT NULL,
    token VARCHAR(64) UNIQUE NOT NULL,
    title VARCHAR(100) DEFAULT 'Laporan Keuangan',
    expires_at DATETIME NULL,
    view_count INT DEFAULT 0,
    last_viewed_at DATETIME NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Default Categories (System-wide, id_pengguna = NULL)
-- =====================================================

-- Income Categories (Pemasukan)
INSERT INTO kategori (id_pengguna, nama_kategori, tipe, icon, warna) VALUES
(NULL, 'Gaji', 'Pemasukan', 'bi-wallet2', '#10b981'),
(NULL, 'Bonus', 'Pemasukan', 'bi-gift', '#059669'),
(NULL, 'Investasi', 'Pemasukan', 'bi-graph-up-arrow', '#0d9488'),
(NULL, 'Penjualan', 'Pemasukan', 'bi-shop', '#14b8a6'),
(NULL, 'Hadiah', 'Pemasukan', 'bi-box-seam', '#06b6d4'),
(NULL, 'Lainnya', 'Pemasukan', 'bi-plus-circle', '#0ea5e9');

-- Expense Categories (Pengeluaran)
INSERT INTO kategori (id_pengguna, nama_kategori, tipe, icon, warna) VALUES
(NULL, 'Makanan & Minuman', 'Pengeluaran', 'bi-cup-hot', '#f97316'),
(NULL, 'Transportasi', 'Pengeluaran', 'bi-car-front', '#ef4444'),
(NULL, 'Belanja', 'Pengeluaran', 'bi-bag', '#ec4899'),
(NULL, 'Tagihan', 'Pengeluaran', 'bi-receipt', '#8b5cf6'),
(NULL, 'Hiburan', 'Pengeluaran', 'bi-controller', '#a855f7'),
(NULL, 'Kesehatan', 'Pengeluaran', 'bi-heart-pulse', '#ef4444'),
(NULL, 'Pendidikan', 'Pengeluaran', 'bi-book', '#3b82f6'),
(NULL, 'Rumah Tangga', 'Pengeluaran', 'bi-house', '#6366f1'),
(NULL, 'Lainnya', 'Pengeluaran', 'bi-three-dots', '#64748b');
