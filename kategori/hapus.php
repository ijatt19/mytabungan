<?php
require_once __DIR__ . '/../config/koneksi.php';

// Start Session & Check Login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id_pengguna'])) {
    header('Location: ../auth/login.php');
    exit;
}

$id_pengguna = $_SESSION['id_pengguna'];

if (isset($_GET['id'])) {
    $id_kategori = $_GET['id'];

    // Validasi ID
    if (!is_numeric($id_kategori)) {
        $_SESSION['pesan_error'] = "ID Kategori tidak valid.";
        header('Location: index.php');
        exit;
    }

    try {
        // Cek apakah kategori digunakan di transaksi
        $stmt_cek = $pdo->prepare("SELECT COUNT(*) FROM transaksi WHERE id_kategori = ? AND id_pengguna = ?");
        $stmt_cek->execute([$id_kategori, $id_pengguna]);
        $count = $stmt_cek->fetchColumn();

        if ($count > 0) {
            $_SESSION['pesan_error'] = "Kategori tidak dapat dihapus karena sedang digunakan dalam transaksi.";
        } else {
            // Hapus kategori
            $stmt = $pdo->prepare("DELETE FROM kategori WHERE id_kategori = ? AND id_pengguna = ?");
            $stmt->execute([$id_kategori, $id_pengguna]);
            $_SESSION['pesan_sukses'] = "Kategori berhasil dihapus.";
        }
    } catch (PDOException $e) {
        $_SESSION['pesan_error'] = "Gagal menghapus: " . $e->getMessage();
    }
} else {
    $_SESSION['pesan_error'] = "ID Kategori tidak ditemukan.";
}

header('Location: index.php');
exit;