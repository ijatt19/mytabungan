<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/functions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_pengguna'])) {
    header('Location: ../auth/login.php');
    exit;
}

$id_pengguna = $_SESSION['id_pengguna'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_wishlist = cleanInput($_POST['id_wishlist']);
    $nama_barang = cleanInput($_POST['nama_barang']);
    $harga = cleanInput($_POST['harga']);
    $prioritas = cleanInput($_POST['prioritas']);

    if (empty($nama_barang) || empty($harga) || empty($prioritas) || empty($id_wishlist)) {
        $_SESSION['pesan_error'] = 'Gagal memperbarui, semua kolom wajib diisi.';
    } elseif (!is_numeric($harga) || $harga < 0) {
        $_SESSION['pesan_error'] = 'Harga harus berupa angka yang valid.';
    } else {
        try {
            // Verifikasi kepemilikan
            $stmt_cek = $pdo->prepare("SELECT id_wishlist FROM wishlist WHERE id_wishlist = ? AND id_pengguna = ?");
            $stmt_cek->execute([$id_wishlist, $id_pengguna]);

            if ($stmt_cek->fetch()) {
                $sql = "UPDATE wishlist SET nama_barang = ?, harga = ?, prioritas = ? WHERE id_wishlist = ? AND id_pengguna = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nama_barang, $harga, $prioritas, $id_wishlist, $id_pengguna]);
                $_SESSION['pesan_sukses'] = 'Impian berhasil diperbarui.';
            } else {
                $_SESSION['pesan_error'] = 'Data tidak ditemukan atau Anda tidak memiliki akses.';
            }
        } catch (PDOException $e) {
            $_SESSION['pesan_error'] = 'Terjadi kesalahan database: ' . $e->getMessage();
        }
    }
}

header('Location: index.php');
exit;
