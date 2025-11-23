<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MyTabungan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="/css/style.css">
    <link rel="stylesheet" href="/css/sidebar.css">
</head>
<body class="app-layout">

    <!-- Header untuk tampilan mobile -->
    <header class="mobile-header d-md-none">
        <a href="/dashboard.php" class="text-decoration-none text-dark fs-5 fw-bold">MyTabungan</a>
        <button class="btn" type="button" id="sidebarToggleBtn">
            <i class="bi bi-list fs-3"></i>
        </button>
    </header>

    <!-- Sidebar overlay for mobile -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <div class="app-container">
        <!-- Sidebar akan dimuat di sini dari dashboard/pages -->
        
        <div class="main-content-wrapper">
        <!-- Main content akan dimuat di sini -->