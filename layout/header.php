<!doctype html>
<?php 
require_once __DIR__ . '/../includes/functions.php'; 
$base_url = '.';
?>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MyTabungan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="css/output.css">
    <link rel="stylesheet" href="css/style.css">
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased">

    <!-- Header untuk tampilan mobile -->
    <header class="md:hidden flex items-center justify-between p-4 bg-white border-b border-slate-200 sticky top-0 z-50">
        <a href="dashboard.php" class="text-lg font-bold text-emerald-600 no-underline">MyTabungan</a>
        <button class="p-2 text-slate-600 hover:text-emerald-600 transition-colors" type="button" id="sidebarToggleBtn">
            <i class="bi bi-list text-2xl"></i>
        </button>
    </header>

    <!-- Sidebar overlay for mobile -->
    <div class="fixed inset-0 bg-black/50 z-40 hidden transition-opacity opacity-0" id="sidebarOverlay"></div>
