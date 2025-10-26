<?php
// Amankan halaman ini
require_once __DIR__ . '/auth/cek_masuk.php';

header('Location: dashboard.php');
exit;