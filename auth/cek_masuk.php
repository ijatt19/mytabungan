<?php
// auth/cek_masuk.php

require_once __DIR__ . '/../config/koneksi.php';

if (!isset($_SESSION['id_pengguna'])) {
    $_SESSION['pesan_error'] = 'Anda harus login terlebih dahulu.';
    header('Location: /auth/login.php');
    exit;
}
?>