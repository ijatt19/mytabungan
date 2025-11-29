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
$id_transaksi = $_GET['id'] ?? null;

if (!$id_transaksi) {
    $_SESSION['pesan_error'] = 'ID Transaksi tidak valid.';
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM transaksi WHERE id_transaksi = ? AND id_pengguna = ?");
    $stmt->execute([$id_transaksi, $id_pengguna]);
    $_SESSION['pesan_sukses'] = 'Transaksi berhasil dihapus.';
} catch (PDOException $e) {
    $_SESSION['pesan_error'] = "Gagal menghapus: " . $e->getMessage();
}

header('Location: index.php');
exit;
?>
