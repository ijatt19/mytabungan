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
    $id_kategori = cleanInput($_POST['id_kategori']);
    $nama_kategori = cleanInput($_POST['nama_kategori']);
    $tipe = cleanInput($_POST['tipe']);
    
    if (!empty($nama_kategori) && !empty($tipe) && !empty($id_kategori)) {
        // Cek duplikasi nama (kecuali diri sendiri)
        $stmt_cek = $pdo->prepare("SELECT id_kategori FROM kategori WHERE nama_kategori = ? AND id_pengguna = ? AND id_kategori != ?");
        $stmt_cek->execute([$nama_kategori, $id_pengguna, $id_kategori]);
        
        if ($stmt_cek->fetch()) {
            $_SESSION['pesan_error'] = "Gagal memperbarui. Kategori dengan nama '{$nama_kategori}' sudah ada.";
        } else {
            $stmt = $pdo->prepare("UPDATE kategori SET nama_kategori = ?, tipe = ? WHERE id_kategori = ? AND id_pengguna = ?");
            if ($stmt->execute([$nama_kategori, $tipe, $id_kategori, $id_pengguna])) {
                $_SESSION['pesan_sukses'] = 'Kategori berhasil diperbarui.';
            } else {
                $_SESSION['pesan_error'] = 'Terjadi kesalahan saat memperbarui kategori.';
            }
        }
    } else {
        $_SESSION['pesan_error'] = 'Semua field wajib diisi.';
    }
}

header('Location: index.php');
exit;
