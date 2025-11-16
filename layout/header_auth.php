<?php
require_once __DIR__ . '/../config/koneksi.php'; // Memulai session
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyTabungan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/tabung/css/style.css">
</head>

<body>
    <!-- Konten pesan error dipindahkan ke footer.php untuk dijadikan toast -->
    <div class="container-fluid"></div>