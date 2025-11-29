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
    $nama_kategori = cleanInput($_POST['nama_kategori']);
    $tipe = cleanInput($_POST['tipe']);
    
    if (!empty($nama_kategori) && !empty($tipe)) {
        // Cek duplikasi nama kategori
        $stmt_cek = $pdo->prepare("SELECT id_kategori FROM kategori WHERE nama_kategori = ? AND id_pengguna = ?");
        $stmt_cek->execute([$nama_kategori, $id_pengguna]);
        
        if ($stmt_cek->fetch()) {
            $_SESSION['pesan_error'] = "Gagal menambah. Kategori dengan nama '{$nama_kategori}' sudah ada.";
        } else {
            try {
                $stmt = $pdo->prepare("INSERT INTO kategori (nama_kategori, tipe, id_pengguna) VALUES (?, ?, ?)");
                $stmt->execute([$nama_kategori, $tipe, $id_pengguna]);
                $_SESSION['pesan_sukses'] = 'Kategori baru berhasil ditambahkan.';
            } catch (PDOException $e) {
                $_SESSION['pesan_error'] = "Gagal menyimpan: " . $e->getMessage();
            }
        }
    } else {
        $_SESSION['pesan_error'] = 'Nama kategori dan tipe wajib diisi.';
    }
}

header('Location: index.php');
exit;
