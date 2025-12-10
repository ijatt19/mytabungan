<?php
/**
 * Header Layout Component
 * MyTabungan - Personal Finance Management
 */

// Get current page for active nav
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="MyTabungan - Aplikasi Manajemen Keuangan Pribadi">
    <meta name="author" content="MyTabungan">
    
    <title><?= $pageTitle ?? 'MyTabungan' ?> - Kelola Keuangan Anda</title>
    
    <!-- Google Fonts - Outfit with display=swap -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Tailwind CSS (Compiled) -->
    <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body class="bg-slate-100 min-h-screen">


