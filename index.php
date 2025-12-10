<?php
/**
 * Landing Page
 * MyTabungan - Personal Finance Management
 * Halaman perkenalan untuk pengguna sebelum login
 */

require_once __DIR__ . '/auth/auth.php';

// Redirect to dashboard if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyTabungan - Kelola Keuangan dengan Mudah</title>
    <meta name="description" content="MyTabungan adalah aplikasi pencatatan keuangan pribadi yang membantu Anda mengatur pemasukan, pengeluaran, dan mencapai target tabungan dengan mudah.">
    
    <!-- Compiled Tailwind CSS -->
    <link rel="stylesheet" href="assets/css/styles.css">
    
    <!-- Google Fonts - Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <style>
        .landing-delay-100 { animation-delay: 0.1s; opacity: 0; }
        .landing-delay-200 { animation-delay: 0.2s; opacity: 0; }
        .landing-delay-300 { animation-delay: 0.3s; opacity: 0; }
        .landing-delay-400 { animation-delay: 0.4s; opacity: 0; }
        .landing-delay-500 { animation-delay: 0.5s; opacity: 0; }
    </style>
</head>
<body class="bg-slate-50 overflow-x-hidden">
    
    <!-- Navbar -->
    <nav id="landing-navbar" class="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-xl border-b border-slate-100 transition-transform duration-300 ease-in-out">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <!-- Logo -->
                <a href="index.php" class="flex items-center gap-2 sm:gap-3 flex-shrink-0">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <i class="bi bi-wallet2 text-white text-sm sm:text-lg"></i>
                    </div>
                    <span class="font-bold text-lg sm:text-xl bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                        MyTabungan
                    </span>
                </a>
                
                <!-- Desktop Menu -->
                <div class="hidden lg:flex items-center gap-8">
                    <a href="#features" class="text-slate-600 hover:text-emerald-600 transition-colors font-medium">Fitur</a>
                    <a href="#how-it-works" class="text-slate-600 hover:text-emerald-600 transition-colors font-medium">Cara Kerja</a>
                    <a href="#about" class="text-slate-600 hover:text-emerald-600 transition-colors font-medium">Tentang</a>
                </div>
                
                <!-- Auth Buttons -->
                <div class="flex items-center gap-2 sm:gap-3">
                    <a href="login.php" class="text-slate-600 hover:text-emerald-600 font-medium px-2 sm:px-4 py-2 transition-colors text-sm sm:text-base">
                        Masuk
                    </a>
                    <a href="register.php" class="btn-primary text-white font-semibold px-3 py-1.5 sm:px-4 sm:py-2 rounded-lg sm:rounded-xl shadow-lg shadow-emerald-500/30 text-xs sm:text-sm whitespace-nowrap">
                        Daftar
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Mobile Menu (scroll links) -->
        <div class="lg:hidden border-t border-slate-100 bg-white/90 backdrop-blur-xl">
            <div class="flex items-center justify-center gap-6 py-2 px-4 overflow-x-auto">
                <a href="#features" class="text-slate-600 hover:text-emerald-600 transition-colors text-sm font-medium whitespace-nowrap">Fitur</a>
                <a href="#how-it-works" class="text-slate-600 hover:text-emerald-600 transition-colors text-sm font-medium whitespace-nowrap">Cara Kerja</a>
                <a href="#about" class="text-slate-600 hover:text-emerald-600 transition-colors text-sm font-medium whitespace-nowrap">Tentang</a>
            </div>
        </div>
    </nav>
    
    <!-- Hero Section -->
    <section class="gradient-bg min-h-screen flex items-center relative overflow-hidden pt-24 lg:pt-16">
        <!-- Background Decorations -->
        <div class="absolute inset-0 overflow-hidden pointer-events-none">
            <div class="absolute -top-40 -right-40 w-96 h-96 bg-emerald-300/40 rounded-full blur-3xl float-animation"></div>
            <div class="absolute -bottom-40 -left-40 w-96 h-96 bg-teal-300/40 rounded-full blur-3xl float-animation" style="animation-delay: -3s;"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[600px] h-[600px] bg-cyan-200/20 rounded-full blur-3xl"></div>
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-20 relative z-10">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                
                <!-- Left Content -->
                <div class="text-center lg:text-left">
                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-white/80 backdrop-blur rounded-full shadow-sm mb-6 animate-fade-in-up">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                        <span class="text-sm font-medium text-slate-600">100% Gratis & Tanpa Iklan</span>
                    </div>
                    
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-slate-800 leading-tight mb-6 animate-fade-in-up landing-delay-100">
                        Kelola Keuangan
                        <span class="bg-gradient-to-r from-emerald-600 via-teal-600 to-cyan-600 bg-clip-text text-transparent"> Pribadi</span>
                        dengan Mudah
                    </h1>
                    
                    <p class="text-lg md:text-xl text-slate-600 mb-8 max-w-lg mx-auto lg:mx-0 animate-fade-in-up landing-delay-200">
                        Catat pemasukan, pantau pengeluaran, dan capai target tabungan Anda dengan aplikasi yang simpel namun powerful.
                    </p>
                    
                    <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-4 justify-center lg:justify-start animate-fade-in-up landing-delay-300">
                        <a href="register.php" class="btn-primary text-white font-semibold px-6 py-3 sm:px-8 sm:py-4 rounded-xl sm:rounded-2xl shadow-xl shadow-emerald-500/30 flex items-center gap-2 text-base sm:text-lg w-full sm:w-auto justify-center">
                            <i class="bi bi-rocket-takeoff"></i>
                            Mulai Sekarang
                        </a>
                        <a href="#how-it-works" class="btn-secondary font-semibold px-6 py-3 sm:px-8 sm:py-4 rounded-xl sm:rounded-2xl flex items-center gap-2 w-full sm:w-auto justify-center text-base sm:text-lg">
                            <i class="bi bi-chevron-double-down"></i>
                            Lihat Cara Kerja
                        </a>
                    </div>
                    
                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-6 mt-12 animate-fade-in-up landing-delay-400">
                        <div class="text-center">
                            <p class="text-2xl md:text-3xl font-bold text-emerald-600">100%</p>
                            <p class="text-sm text-slate-500">Gratis</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl md:text-3xl font-bold text-emerald-600">Aman</p>
                            <p class="text-sm text-slate-500">Data Terlindungi</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl md:text-3xl font-bold text-emerald-600">Mudah</p>
                            <p class="text-sm text-slate-500">Digunakan</p>
                        </div>
                    </div>
                </div>
                
                <!-- Right Content - Mockup -->
                <div class="relative animate-fade-in-up delay-500">
                    <div class="glass-card rounded-3xl p-6 shadow-2xl shadow-emerald-500/10 pulse-glow">
                        <!-- Mock Dashboard -->
                        <div class="bg-slate-50 rounded-2xl p-4 mb-4">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white font-bold">A</div>
                                <div>
                                    <p class="font-semibold text-slate-800">Selamat Pagi, Andi! 👋</p>
                                    <p class="text-xs text-slate-500">Ringkasan keuangan Desember 2025</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Mock Cards -->
                        <div class="grid grid-cols-2 gap-3 mb-4">
                            <div class="bg-emerald-50 rounded-xl p-4">
                                <div class="w-8 h-8 rounded-lg bg-emerald-500 flex items-center justify-center mb-2">
                                    <i class="bi bi-arrow-down text-white"></i>
                                </div>
                                <p class="text-xs text-slate-500">Pemasukan</p>
                                <p class="font-bold text-emerald-600">Rp 5.000.000</p>
                            </div>
                            <div class="bg-red-50 rounded-xl p-4">
                                <div class="w-8 h-8 rounded-lg bg-red-500 flex items-center justify-center mb-2">
                                    <i class="bi bi-arrow-up text-white"></i>
                                </div>
                                <p class="text-xs text-slate-500">Pengeluaran</p>
                                <p class="font-bold text-red-600">Rp 2.500.000</p>
                            </div>
                        </div>
                        
                        <!-- Mock Balance -->
                        <div class="bg-gradient-to-r from-emerald-500 to-teal-500 rounded-xl p-4 text-white">
                            <p class="text-sm opacity-80 mb-1">Saldo Saat Ini</p>
                            <p class="text-2xl font-bold">Rp 2.500.000</p>
                            <div class="flex items-center gap-1 mt-2 text-sm">
                                <i class="bi bi-graph-up-arrow"></i>
                                <span>Kondisi keuangan sehat!</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Floating Elements -->
                    <div class="absolute -top-4 -right-4 w-16 h-16 bg-white rounded-2xl shadow-lg flex items-center justify-center float-animation">
                        <i class="bi bi-piggy-bank text-3xl text-emerald-500"></i>
                    </div>
                    <div class="absolute -bottom-4 -left-4 w-14 h-14 bg-white rounded-2xl shadow-lg flex items-center justify-center float-animation" style="animation-delay: -2s;">
                        <i class="bi bi-graph-up-arrow text-2xl text-teal-500"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 scroll-indicator">
            <a href="#features" class="flex flex-col items-center text-slate-400 hover:text-emerald-500 transition-colors">
                <span class="text-sm mb-2">Scroll ke bawah</span>
                <i class="bi bi-chevron-double-down text-2xl"></i>
            </a>
        </div>
    </section>
    
    <!-- Features Section -->
    <section id="features" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-emerald-100 text-emerald-700 rounded-full text-sm font-semibold mb-4">
                    <i class="bi bi-stars mr-1"></i> Fitur Unggulan
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-slate-800 mb-4">
                    Semua yang Anda Butuhkan untuk Mengelola Keuangan
                </h2>
                <p class="text-lg text-slate-500 max-w-2xl mx-auto">
                    Fitur lengkap dan mudah digunakan untuk membantu Anda mengatur keuangan sehari-hari.
                </p>
            </div>
            
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="feature-card glass-card rounded-2xl p-6 border border-slate-100 hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center mb-5 shadow-lg shadow-emerald-500/30">
                        <i class="bi bi-arrow-left-right text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Catat Transaksi</h3>
                    <p class="text-slate-500">
                        Catat semua pemasukan dan pengeluaran dengan mudah. Lengkap dengan kategori dan keterangan.
                    </p>
                </div>
                
                <!-- Feature 2 -->
                <div class="feature-card glass-card rounded-2xl p-6 border border-slate-100 hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center mb-5 shadow-lg shadow-blue-500/30">
                        <i class="bi bi-pie-chart-fill text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Grafik & Statistik</h3>
                    <p class="text-slate-500">
                        Pantau keuangan Anda dengan grafik interaktif. Lihat tren pemasukan dan pengeluaran bulanan.
                    </p>
                </div>
                
                <!-- Feature 3 -->
                <div class="feature-card glass-card rounded-2xl p-6 border border-slate-100 hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-purple-500 to-pink-600 flex items-center justify-center mb-5 shadow-lg shadow-purple-500/30">
                        <i class="bi bi-tags-fill text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Kategori Custom</h3>
                    <p class="text-slate-500">
                        Buat kategori sesuai kebutuhan Anda. Pilih ikon dan warna yang Anda suka.
                    </p>
                </div>
                
                <!-- Feature 4 -->
                <div class="feature-card glass-card rounded-2xl p-6 border border-slate-100 hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center mb-5 shadow-lg shadow-orange-500/30">
                        <i class="bi bi-heart-fill text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Wishlist & Target</h3>
                    <p class="text-slate-500">
                        Tentukan target tabungan untuk barang impian Anda dan pantau progresnya.
                    </p>
                </div>
                
                <!-- Feature 5 -->
                <div class="feature-card glass-card rounded-2xl p-6 border border-slate-100 hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-cyan-500 to-teal-600 flex items-center justify-center mb-5 shadow-lg shadow-cyan-500/30">
                        <i class="bi bi-share-fill text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Bagikan Laporan</h3>
                    <p class="text-slate-500">
                        Bagikan ringkasan keuangan Anda dengan mudah kepada pasangan atau keluarga.
                    </p>
                </div>
                
                <!-- Feature 6 -->
                <div class="feature-card glass-card rounded-2xl p-6 border border-slate-100 hover:shadow-xl transition-all duration-300">
                    <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center mb-5 shadow-lg shadow-green-500/30">
                        <i class="bi bi-shield-check text-white text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Aman & Private</h3>
                    <p class="text-slate-500">
                        Data Anda terenkripsi dan aman. Hanya Anda yang bisa mengakses data keuangan Anda.
                    </p>
                </div>
            </div>
        </div>
    </section>
    
    <!-- How It Works Section -->
    <section id="how-it-works" class="py-24 gradient-bg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="inline-block px-4 py-2 bg-white text-emerald-700 rounded-full text-sm font-semibold mb-4 shadow-sm">
                    <i class="bi bi-lightning-charge mr-1"></i> Mudah & Cepat
                </span>
                <h2 class="text-3xl md:text-4xl font-bold text-slate-800 mb-4">
                    Cara Menggunakan MyTabungan
                </h2>
                <p class="text-lg text-slate-500 max-w-2xl mx-auto">
                    Hanya butuh 3 langkah sederhana untuk mulai mengelola keuangan Anda.
                </p>
            </div>
            
            <div class="grid md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="relative text-center">
                    <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center mx-auto mb-6 shadow-xl shadow-emerald-500/30">
                        <span class="text-3xl font-bold text-white">1</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Daftar Gratis</h3>
                    <p class="text-slate-500">
                        Buat akun gratis hanya dengan email dan password. Tidak perlu kartu kredit.
                    </p>
                    <!-- Connector (hidden on mobile) -->
                    <div class="hidden md:block absolute top-10 left-[60%] w-[80%] h-0.5 bg-gradient-to-r from-emerald-500 to-transparent"></div>
                </div>
                
                <!-- Step 2 -->
                <div class="relative text-center">
                    <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center mx-auto mb-6 shadow-xl shadow-teal-500/30">
                        <span class="text-3xl font-bold text-white">2</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Buat Kategori</h3>
                    <p class="text-slate-500">
                        Atur kategori pemasukan dan pengeluaran sesuai kebutuhan Anda.
                    </p>
                    <!-- Connector (hidden on mobile) -->
                    <div class="hidden md:block absolute top-10 left-[60%] w-[80%] h-0.5 bg-gradient-to-r from-teal-500 to-transparent"></div>
                </div>
                
                <!-- Step 3 -->
                <div class="text-center">
                    <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-cyan-500 to-blue-600 flex items-center justify-center mx-auto mb-6 shadow-xl shadow-cyan-500/30">
                        <span class="text-3xl font-bold text-white">3</span>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800 mb-3">Catat & Pantau</h3>
                    <p class="text-slate-500">
                        Mulai catat transaksi harian dan pantau kondisi keuangan Anda melalui dashboard.
                    </p>
                </div>
            </div>
            
            <!-- CTA Button -->
            <div class="text-center mt-12">
                <a href="register.php" class="btn-primary inline-flex items-center gap-2 text-white font-semibold px-8 py-4 rounded-2xl shadow-xl shadow-emerald-500/30 text-lg">
                    <i class="bi bi-rocket-takeoff"></i>
                    Mulai Sekarang - Gratis!
                </a>
            </div>
        </div>
    </section>
    
    <!-- About Section -->
    <section id="about" class="py-24 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div>
                    <span class="inline-block px-4 py-2 bg-emerald-100 text-emerald-700 rounded-full text-sm font-semibold mb-4">
                        <i class="bi bi-info-circle mr-1"></i> Tentang Aplikasi
                    </span>
                    <h2 class="text-3xl md:text-4xl font-bold text-slate-800 mb-6">
                        Mengapa Memilih MyTabungan?
                    </h2>
                    <p class="text-lg text-slate-500 mb-6">
                        MyTabungan dibuat untuk membantu siapa saja mengelola keuangan pribadi dengan cara yang sederhana dan menyenangkan. Kami percaya bahwa mengelola uang tidak harus rumit.
                    </p>
                    
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="bi bi-check text-emerald-600"></i>
                            </div>
                            <span class="text-slate-600"><strong>100% Gratis</strong> - Semua fitur dapat diakses tanpa biaya apapun.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="bi bi-check text-emerald-600"></i>
                            </div>
                            <span class="text-slate-600"><strong>Tanpa Iklan</strong> - Tidak ada iklan yang mengganggu pengalaman Anda.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="bi bi-check text-emerald-600"></i>
                            </div>
                            <span class="text-slate-600"><strong>Privasi Terjamin</strong> - Data Anda adalah milik Anda, tidak dijual ke pihak ketiga.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                                <i class="bi bi-check text-emerald-600"></i>
                            </div>
                            <span class="text-slate-600"><strong>Responsive</strong> - Dapat diakses dari perangkat apapun, kapanpun.</span>
                        </li>
                    </ul>
                </div>
                
                <div class="relative">
                    <div class="glass-card rounded-3xl p-8 shadow-xl">
                        <div class="text-center">
                            <div class="w-24 h-24 rounded-3xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center mx-auto mb-6 shadow-xl shadow-emerald-500/30">
                                <i class="bi bi-wallet2 text-white text-4xl"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-slate-800 mb-2">MyTabungan</h3>
                            <p class="text-slate-500 mb-6">Personal Finance Management</p>
                            
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-slate-50 rounded-xl p-4">
                                    <i class="bi bi-phone text-2xl text-emerald-500 mb-2"></i>
                                    <p class="text-sm font-medium text-slate-700">Mobile Friendly</p>
                                </div>
                                <div class="bg-slate-50 rounded-xl p-4">
                                    <i class="bi bi-cloud-check text-2xl text-emerald-500 mb-2"></i>
                                    <p class="text-sm font-medium text-slate-700">Cloud Backup</p>
                                </div>
                                <div class="bg-slate-50 rounded-xl p-4">
                                    <i class="bi bi-palette text-2xl text-emerald-500 mb-2"></i>
                                    <p class="text-sm font-medium text-slate-700">UI Modern</p>
                                </div>
                                <div class="bg-slate-50 rounded-xl p-4">
                                    <i class="bi bi-speedometer2 text-2xl text-emerald-500 mb-2"></i>
                                    <p class="text-sm font-medium text-slate-700">Super Cepat</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Floating Elements -->
                    <div class="absolute -top-4 -left-4 w-12 h-12 bg-white rounded-xl shadow-lg flex items-center justify-center float-animation">
                        <i class="bi bi-currency-dollar text-xl text-emerald-500"></i>
                    </div>
                    <div class="absolute -bottom-4 -right-4 w-12 h-12 bg-white rounded-xl shadow-lg flex items-center justify-center float-animation" style="animation-delay: -2s;">
                        <i class="bi bi-graph-up text-xl text-teal-500"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section class="py-20 hero-gradient">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Siap Mengelola Keuangan dengan Lebih Baik?
            </h2>
            <p class="text-lg text-white/80 mb-8 max-w-2xl mx-auto">
                Bergabung sekarang dan mulai perjalanan menuju kebebasan finansial. 100% gratis, tanpa syarat!
            </p>
            <div class="flex flex-col sm:flex-row items-center gap-3 sm:gap-4 justify-center">
                <a href="register.php" class="bg-white text-emerald-600 font-bold px-6 py-3 sm:px-8 sm:py-4 rounded-xl sm:rounded-2xl shadow-xl hover:shadow-2xl hover:-translate-y-1 transition-all duration-300 flex items-center gap-2 w-full sm:w-auto justify-center text-sm sm:text-base">
                    <i class="bi bi-person-plus-fill"></i>
                    Daftar Gratis Sekarang
                </a>
                <a href="login.php" class="text-white font-semibold px-6 py-3 sm:px-8 sm:py-4 rounded-xl sm:rounded-2xl border-2 border-white/30 hover:bg-white/10 transition-all duration-300 flex items-center gap-2 w-full sm:w-auto justify-center text-sm sm:text-base">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Sudah Punya Akun? Masuk
                </a>
            </div>
        </div>
    </section>
    
    <!-- Footer -->
    <footer class="bg-slate-900 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-6">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center">
                        <i class="bi bi-wallet2 text-white text-lg"></i>
                    </div>
                    <span class="font-bold text-xl text-white">MyTabungan</span>
                </div>
                
                <p class="text-slate-400 text-center">
                    &copy; <?= date('Y') ?> MyTabungan. Kelola Keuangan dengan Mudah.
                </p>
                
                <div class="flex items-center gap-4">
                    <a href="https://github.com/ijatt19" class="w-10 h-10 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-white transition-all">
                        <i class="bi bi-github"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-white transition-all">
                        <i class="bi bi-instagram"></i>
                    </a>
                    <a href="#" class="w-10 h-10 rounded-lg bg-slate-800 flex items-center justify-center text-slate-400 hover:bg-emerald-500 hover:text-white transition-all">
                        <i class="bi bi-twitter-x"></i>
                    </a>
                </div>
            </div>
        </div>
    </footer>
    
    <!-- Smooth Scroll & Hide Navbar Script -->
    <script>
        // Smooth scroll for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Hide navbar on scroll down, show on scroll up
        (function() {
            const navbar = document.getElementById('landing-navbar');
            let lastScrollTop = 0;
            let ticking = false;
            
            window.addEventListener('scroll', function() {
                if (!ticking) {
                    window.requestAnimationFrame(function() {
                        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                        
                        if (scrollTop > lastScrollTop && scrollTop > 100) {
                            // Scrolling down & past 100px - hide navbar
                            navbar.style.transform = 'translateY(-100%)';
                        } else {
                            // Scrolling up - show navbar
                            navbar.style.transform = 'translateY(0)';
                        }
                        
                        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
                        ticking = false;
                    });
                    ticking = true;
                }
            });
        })();
    </script>
</body>
</html>
