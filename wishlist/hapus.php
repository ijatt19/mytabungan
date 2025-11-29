<?php
require_once __DIR__ . '/../config/koneksi.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_pengguna'])) {
    header('Location: ../auth/login.php');
    exit;
}

$id_pengguna = $_SESSION['id_pengguna'];
$id_wishlist = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id_wishlist) {
    try {
        // Verifikasi kepemilikan sebelum menghapus
        $stmt_cek = $pdo->prepare("SELECT id_wishlist FROM wishlist WHERE id_wishlist = ? AND id_pengguna = ?");
        $stmt_cek->execute([$id_wishlist, $id_pengguna]);

        if ($stmt_cek->fetch()) {
            $stmt = $pdo->prepare("DELETE FROM wishlist WHERE id_wishlist = ?");
            $stmt->execute([$id_wishlist]);
            $_SESSION['pesan_sukses'] = 'Impian berhasil dihapus.';
        } else {
            $_SESSION['pesan_error'] = 'Data tidak ditemukan atau Anda tidak memiliki akses.';
        }
    } catch (PDOException $e) {
        $_SESSION['pesan_error'] = 'Gagal menghapus data: ' . $e->getMessage();
    }
} else {
    $_SESSION['pesan_error'] = 'ID tidak valid.';
}

header('Location: index.php');
exit;