<?php
/**
 * Sidebar Layout Component
 * MyTabungan - Personal Finance Management
 * Desktop: Sidebar | Mobile: Bottom Navigation
 */

$currentPage = basename($_SERVER['PHP_SELF'], '.php');

// Navigation items
$navItems = [
    ['name' => 'Dashboard', 'icon' => 'bi-grid-1x2-fill', 'href' => 'dashboard.php', 'page' => 'dashboard'],
    ['name' => 'Transaksi', 'icon' => 'bi-arrow-left-right', 'href' => 'transaksi.php', 'page' => 'transaksi'],
    ['name' => 'Kategori', 'icon' => 'bi-tags-fill', 'href' => 'kategori.php', 'page' => 'kategori'],
    ['name' => 'Wishlist', 'icon' => 'bi-heart-fill', 'href' => 'wishlist.php', 'page' => 'wishlist'],
    ['name' => 'Bagikan', 'icon' => 'bi-share-fill', 'href' => 'share.php', 'page' => 'share'],
];
?>

<!-- Sidebar (Desktop Only) -->
<aside id="sidebar" class="sidebar fixed left-0 top-0 h-full w-64 bg-white/80 backdrop-blur-xl border-r border-white/20 shadow-xl z-50 hidden lg:block transition-all duration-300">
    
    <!-- Logo -->
    <div class="h-20 flex items-center justify-between px-6 border-b border-slate-100">
        <a href="dashboard.php" class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                <i class="bi bi-wallet2 text-white text-lg"></i>
            </div>
            <span class="sidebar-logo-text font-bold text-xl bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                MyTabungan
            </span>
        </a>
        
        <!-- Desktop Toggle Button -->
        <button id="sidebar-toggle" class="w-8 h-8 flex items-center justify-center rounded-lg hover:bg-slate-100 transition-colors text-slate-400">
            <i class="bi bi-chevron-left text-sm"></i>
        </button>
    </div>
    
    <!-- Navigation -->
    <nav class="p-4 space-y-2">
        <p class="sidebar-text text-xs font-semibold text-slate-400 uppercase tracking-wider px-3 mb-3">Menu</p>
        
        <?php foreach ($navItems as $item): ?>
        <a href="<?= $item['href'] ?>" 
           class="nav-link <?= $currentPage === $item['page'] ? 'active' : '' ?>">
            <i class="bi <?= $item['icon'] ?>"></i>
            <span class="sidebar-text"><?= $item['name'] ?></span>
        </a>
        <?php endforeach; ?>
    </nav>
    
    <!-- User Section at Bottom -->
    <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-slate-100">
        <div class="flex items-center gap-3">
            <a href="profil.php" class="flex items-center gap-3 flex-1 p-3 rounded-xl hover:bg-slate-50 transition-colors <?= $currentPage === 'profil' ? 'bg-emerald-50' : '' ?>">
                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-semibold">
                    <?= strtoupper(substr(getCurrentUserName() ?? 'U', 0, 1)) ?>
                </div>
                <div class="sidebar-text flex-1 min-w-0">
                    <p class="font-medium text-slate-700 truncate"><?= htmlspecialchars(getCurrentUserName() ?? 'User') ?></p>
                    <p class="text-xs text-slate-400 truncate"><?= htmlspecialchars(getCurrentUserEmail() ?? '') ?></p>
                </div>
            </a>
            <a href="logout.php" class="sidebar-text p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Keluar">
                <i class="bi bi-box-arrow-right text-lg"></i>
            </a>
        </div>
    </div>
</aside>

<!-- Main Content Wrapper -->
<div id="main-content" class="lg:ml-64 min-h-screen transition-all duration-300">
    
    <!-- Top Header Bar -->
    <header class="sticky top-0 z-30 bg-white/80 backdrop-blur-xl border-b border-slate-100 px-4 lg:px-6 h-16 flex items-center justify-between">
        
        <!-- Page Title / Logo (Mobile) -->
        <div class="flex items-center gap-2">
            <div class="lg:hidden w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center">
                <i class="bi bi-wallet2 text-white text-sm"></i>
            </div>
            <h1 class="text-lg font-semibold text-slate-800">
                <span class="lg:hidden">MyTabungan</span>
                <span class="hidden lg:inline"><?= $pageTitle ?? 'Dashboard' ?></span>
            </h1>
        </div>
        
        <!-- Right Side Actions -->
        <div class="flex items-center gap-3">
            <!-- Date Display -->
            <div class="hidden md:flex items-center gap-2 text-sm text-slate-500">
                <i class="bi bi-calendar3"></i>
                <span><?= formatTanggal(date('Y-m-d'), 'short') ?></span>
            </div>
            
            <!-- User Dropdown -->
            <div class="relative">
                <button data-dropdown-toggle="user-dropdown" class="flex items-center gap-2 p-2 rounded-xl hover:bg-slate-100 transition-colors">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white text-sm font-semibold">
                        <?= strtoupper(substr(getCurrentUserName() ?? 'U', 0, 1)) ?>
                    </div>
                    <i class="bi bi-chevron-down text-slate-400 text-xs hidden lg:block"></i>
                </button>
                
                <!-- Dropdown Menu -->
                <div id="user-dropdown" class="dropdown-menu absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-2">
                    <div class="px-4 py-2 border-b border-slate-100">
                        <p class="font-medium text-slate-800"><?= htmlspecialchars(getCurrentUserName() ?? 'User') ?></p>
                        <p class="text-xs text-slate-400"><?= htmlspecialchars(getCurrentUserEmail() ?? '') ?></p>
                    </div>
                    <a href="profil.php" class="flex items-center gap-2 px-4 py-2 text-sm text-slate-600 hover:bg-slate-50">
                        <i class="bi bi-person"></i>
                        <span>Profil Saya</span>
                    </a>
                    <a href="logout.php" class="flex items-center gap-2 px-4 py-2 text-sm text-red-500 hover:bg-red-50">
                        <i class="bi bi-box-arrow-right"></i>
                        <span>Keluar</span>
                    </a>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Flash Messages Container -->
    <div class="px-4 lg:px-6 pt-4">
        <?= displayFlashMessage() ?>
    </div>
    
    <!-- Main Content Area -->
    <main class="p-4 lg:p-6 pb-24 lg:pb-20">
