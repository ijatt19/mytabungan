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
    $nama_barang = cleanInput($_POST['nama_barang']);
    $harga = cleanInput($_POST['harga']);
    $prioritas = cleanInput($_POST['prioritas']);

    if (empty($nama_barang) || empty($harga) || empty($prioritas)) {
        $_SESSION['pesan_error'] = 'Semua kolom wajib diisi.';
    } elseif (!is_numeric($harga) || $harga < 0) {
        $_SESSION['pesan_error'] = 'Harga harus berupa angka yang valid.';
    } else {
        try {
            $sql = "INSERT INTO wishlist (id_pengguna, nama_barang, harga, prioritas, status, dibuat_pada) VALUES (?, ?, ?, ?, 'Aktif', NOW())";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$id_pengguna, $nama_barang, $harga, $prioritas]);
            $_SESSION['pesan_sukses'] = 'Impian baru berhasil ditambahkan!';
        } catch (PDOException $e) {
            $_SESSION['pesan_error'] = 'Terjadi kesalahan database: ' . $e->getMessage();
        }
    }
}

$redirect_to = $_POST['redirect_to'] ?? 'index.php';
header("Location: $redirect_to");
exit;
