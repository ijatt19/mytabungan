<?php
require_once __DIR__ . '/../auth/cek_masuk.php';

$id_pengguna = $_SESSION['id_pengguna'];
$id_kategori = $_GET['id'] ?? null;

// Simpan query string untuk redirect
$redirect_url = 'index.php?' . http_build_query(array_diff_key($_GET, array_flip(['id'])));


if (!$id_kategori) {
    $_SESSION['pesan_error'] = 'ID Kategori tidak valid.';
    header('Location: ' . $redirect_url);
    exit;
}

try {
    $stmt_cek = $pdo->prepare("SELECT COUNT(*) AS total FROM transaksi WHERE id_kategori = ? AND id_pengguna = ?");
    $stmt_cek->execute([$id_kategori, $id_pengguna]);
    $hasil_cek = $stmt_cek->fetch();

    if ($hasil_cek['total'] > 0) {
        $_SESSION['pesan_error'] = "Gagal menghapus! Kategori ini masih digunakan oleh {$hasil_cek['total']} transaksi.";
        header('Location: ' . $redirect_url);
        exit;
    }

    $stmt = $pdo->prepare("DELETE FROM kategori WHERE id_kategori = ? AND id_pengguna = ?");
    $stmt->execute([$id_kategori, $id_pengguna]);

    $_SESSION['pesan_sukses'] = 'Kategori berhasil dihapus.';
    header('Location: ' . $redirect_url);
    exit;

} catch (PDOException $e) {
    $_SESSION['pesan_error'] = "Gagal menghapus kategori. Error: {$e->getMessage()}";
    header('Location: ' . $redirect_url);
    exit;
}
?>