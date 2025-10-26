<?php
// auth/cek_masuk.php

// Panggil file koneksi (naik 1 folder, lalu masuk ke /config)
require_once __DIR__ . '/../config/koneksi.php';

// Periksa apakah sesi 'id_pengguna' TIDAK ada
if (!isset($_SESSION['id_pengguna'])) {
    // Jika tidak ada sesi, paksa pengguna kembali ke halaman login
    $_SESSION['pesan_error'] = 'Anda harus login terlebih dahulu.';
    header('Location: /auth/login.php');
    exit; // Hentikan eksekusi skrip lebih lanjut
}
?>