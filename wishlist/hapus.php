<?php
require_once __DIR__ . '/../auth/cek_masuk.php';

$id_pengguna = $_SESSION['id_pengguna'];
$id_wishlist = $_GET['id'] ?? null;

if (!$id_wishlist) {
    $_SESSION['pesan_error'] = 'ID Wishlist tidak valid.';
    header('Location: index.php');
    exit;
}

try {
    $stmt = $pdo->prepare("DELETE FROM wishlist WHERE id_wishlist = ? AND id_pengguna = ?");
    $stmt->execute([$id_wishlist, $id_pengguna]);

    $_SESSION['pesan_sukses'] = 'Item wishlist berhasil dihapus.';

} catch (PDOException $e) {
    $_SESSION['pesan_error'] = "Gagal menghapus item. Error: {$e->getMessage()}";
}

header('Location: index.php');
exit;