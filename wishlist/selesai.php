<?php
require_once __DIR__ . '/../auth/cek_masuk.php';

// Pastikan ada ID wishlist yang dikirim melalui URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    $_SESSION['pesan_error'] = 'ID Impian tidak valid atau tidak ditemukan.';
    header('Location: index.php');
    exit;
}

$id_wishlist = $_GET['id'];
$id_pengguna = $_SESSION['id_pengguna'];

try {
    // Query untuk mengubah status menjadi 'Selesai'.
    // Klausa "AND id_pengguna = ?" sangat penting untuk keamanan,
    // agar pengguna tidak bisa mengubah status impian milik orang lain.
    $sql = "UPDATE wishlist SET status = 'Selesai' WHERE id_wishlist = ? AND id_pengguna = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_wishlist, $id_pengguna]);

    // Cek apakah ada baris data yang berhasil diubah
    if ($stmt->rowCount() > 0) {
        $_SESSION['pesan_sukses'] = 'Selamat! Satu impianmu telah berhasil tercapai.';
    } else {
        // Ini terjadi jika ID impian tidak ada atau bukan milik pengguna yang login
        $_SESSION['pesan_error'] = 'Gagal menandai impian. Impian tidak ditemukan.';
    }
} catch (PDOException $e) {
    $_SESSION['pesan_error'] = "Terjadi kesalahan database: " . $e->getMessage();
}

// Kembalikan pengguna ke halaman daftar impian
header('Location: index.php');
exit;