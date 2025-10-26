<?php
require_once __DIR__ . '/../auth/cek_masuk.php';

$id_pengguna = $_SESSION['id_pengguna'];
$id_transaksi = $_GET['id'] ?? null;

// Simpan query string untuk redirect
$redirect_url = 'index.php?' . http_build_query(array_diff_key($_GET, array_flip(['id'])));

if (!$id_transaksi) {
    $_SESSION['pesan_error'] = 'ID Transaksi tidak valid.';
    header('Location: ' . $redirect_url);
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM transaksi WHERE id_transaksi = ? AND id_pengguna = ?");
    $stmt->execute([$id_transaksi, $id_pengguna]);

    $_SESSION['pesan_sukses'] = 'Transaksi berhasil dihapus.';
    header('Location: ' . $redirect_url);
    exit;

} catch (PDOException $e) {
    $_SESSION['pesan_error'] = "Gagal menghapus transaksi. Error: {$e->getMessage()}";
    header('Location: ' . $redirect_url);
    exit;
}
?>