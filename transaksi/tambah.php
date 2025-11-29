<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/functions.php';

// Start Session & Check Login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id_pengguna'])) {
    header('Location: ../auth/login.php');
    exit;
}

$id_pengguna = $_SESSION['id_pengguna'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kategori = cleanInput($_POST['id_kategori']);
    $jumlah = cleanInput($_POST['jumlah']);
    $keterangan = cleanInput($_POST['keterangan']);
    $tanggal_transaksi = cleanInput($_POST['tanggal_transaksi']);

    // Validasi Input
    if (empty($id_kategori) || empty($jumlah) || empty($tanggal_transaksi) || !is_numeric($jumlah) || $jumlah <= 0) {
        $_SESSION['pesan_error'] = "Data tidak valid. Pastikan semua field terisi dengan benar.";
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO transaksi (id_pengguna, id_kategori, jumlah, keterangan, tanggal_transaksi) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$id_pengguna, $id_kategori, $jumlah, $keterangan, $tanggal_transaksi]);
            $_SESSION['pesan_sukses'] = 'Transaksi berhasil ditambahkan.';
        } catch (PDOException $e) {
            $_SESSION['pesan_error'] = "Gagal menyimpan: " . $e->getMessage();
        }
    }
    
    // Redirect
    $redirect_to = $_POST['redirect_to'] ?? 'index.php';
    header("Location: $redirect_to");
    exit;
} else {
    // Jika bukan POST, redirect ke index
    header('Location: index.php');
    exit;
}
?>
