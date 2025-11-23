<?php
// Panggil koneksi.php untuk memulai sesi
require_once __DIR__ . '/../config/koneksi.php';

// Hapus semua variabel sesi
session_unset();

// Hancurkan sesi
session_destroy();

// Arahkan kembali ke halaman login (relative path)
header("Location: login.php");
exit;
?>