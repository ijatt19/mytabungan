<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyTabungan - Kendalikan Keuangan, Wujudkan Impian</title>
    <meta name="description" content="Aplikasi manajemen keuangan pribadi terbaik untuk mencatat pemasukan, pengeluaran, dan mencapai tujuan finansial Anda.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/output.css" rel="stylesheet">
    <link href="css/landing.css" rel="stylesheet">
</head>
<body class="font-sans antialiased text-slate-800 bg-white overflow-x-hidden">

    <!-- Navbar -->
    <nav class="fixed w-full z-50 top-0 transition-all duration-300 navbar-glass">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <a href="#" class="flex items-center gap-2 group">
                    <div class="w-10 h-10 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-500/20 group-hover:scale-110 transition-transform duration-300">
                        <i class="bi bi-wallet2 text-xl"></i>
                    </div>
                    <span class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-slate-800 to-slate-600">MyTabungan</span>
                </a>

                <!-- Desktop Menu -->
                <div class="hidden md:flex items-center gap-8">
                    <a href="#fitur" class="text-sm font-medium text-slate-600 hover:text-emerald-600 transition-colors">Fitur</a>
                    <a href="#cara-kerja" class="text-sm font-medium text-slate-600 hover:text-emerald-600 transition-colors">Cara Kerja</a>
                    <a href="#faq" class="text-sm font-medium text-slate-600 hover:text-emerald-600 transition-colors">FAQ</a>
                </div>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-3">
                    <a href="auth/login.php" class="hidden sm:inline-flex px-5 py-2.5 text-sm font-semibold text-slate-700 hover:text-emerald-600 transition-colors">
                        Masuk
                    </a>
                    <a href="auth/register.php" class="inline-flex items-center justify-center px-6 py-2.5 text-sm font-semibold text-white transition-all duration-200 bg-emerald-600 rounded-full hover:bg-emerald-700 hover:shadow-lg hover:shadow-emerald-500/30 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-600">
                        Daftar Gratis
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-full h-full z-0 pointer-events-none">
            <div class="absolute top-20 left-10 w-72 h-72 bg-emerald-300/20 rounded-full blur-3xl mix-blend-multiply animate-blob"></div>
            <div class="absolute top-20 right-10 w-72 h-72 bg-teal-300/20 rounded-full blur-3xl mix-blend-multiply animate-blob animation-delay-2000"></div>
            <div class="absolute -bottom-32 left-1/2 w-96 h-96 bg-indigo-300/20 rounded-full blur-3xl mix-blend-multiply animate-blob animation-delay-4000"></div>
        </div>

        <div class="container mx-auto px-4 relative z-10">
            <div class="text-center max-w-4xl mx-auto mb-16">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-emerald-50 border border-emerald-100 text-emerald-700 text-sm font-medium mb-8 animate-fade-in-up">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
                    </span>
                    Versi 2.0 Kini Tersedia
                </div>
                <h1 class="text-5xl md:text-7xl font-extrabold tracking-tight text-slate-900 mb-8 leading-tight animate-fade-in-up animation-delay-100">
                    Kendalikan Keuangan, <br>
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-emerald-600 to-teal-500">Wujudkan Impian.</span>
                </h1>
                
                <p class="text-xl text-slate-600 mb-10 max-w-2xl mx-auto leading-relaxed animate-fade-in-up animation-delay-200">
                    Platform manajemen keuangan cerdas yang membantu Anda mencatat, menganalisis, dan mengoptimalkan pengeluaran Anda dalam satu dashboard intuitif.
                </p>
                
                <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-fade-in-up animation-delay-300">
                    <a href="auth/register.php" class="w-full sm:w-auto px-8 py-4 text-lg font-bold text-white transition-all duration-200 bg-emerald-600 rounded-2xl hover:bg-emerald-700 hover:shadow-xl hover:shadow-emerald-500/30 hover:-translate-y-1">
                        Mulai Sekarang - Gratis
                    </a>
                    <button onclick="openDemoModal()" class="w-full sm:w-auto px-8 py-4 text-lg font-bold text-slate-700 transition-all duration-200 bg-white border border-slate-200 rounded-2xl hover:bg-slate-50 hover:border-slate-300 hover:text-slate-900 cursor-pointer">
                        <i class="bi bi-play-circle mr-2"></i> Lihat Demo
                    </button>
                </div>
            </div>

            <!-- Dashboard Preview / Visual -->
            <div class="relative max-w-5xl mx-auto mt-16 animate-fade-in-up animation-delay-500">
                <div class="relative rounded-2xl bg-slate-900 p-2 shadow-2xl ring-1 ring-slate-900/10">
                    <div class="absolute -top-px left-20 right-11 h-px bg-gradient-to-r from-sky-300/0 via-sky-300/70 to-sky-300/0"></div>
                    <div class="absolute -bottom-px left-11 right-20 h-px bg-gradient-to-r from-blue-400/0 via-blue-400/70 to-blue-400/0"></div>
                    
                    <!-- Mockup Content (CSS Only Representation) -->
                    <div class="rounded-xl bg-slate-50 overflow-hidden aspect-[16/9] relative group">
                        <!-- Sidebar -->
                        <div class="absolute left-0 top-0 bottom-0 w-64 bg-white border-r border-slate-200 hidden md:block p-6">
                            <div class="w-32 h-8 bg-slate-200 rounded-lg mb-8"></div>
                            <div class="space-y-4">
                                <div class="w-full h-10 bg-emerald-50 rounded-lg border border-emerald-100"></div>
                                <div class="w-full h-10 bg-white rounded-lg"></div>
                                <div class="w-full h-10 bg-white rounded-lg"></div>
                            </div>
                        </div>
                        <!-- Main Content -->
                        <div class="absolute top-0 right-0 bottom-0 left-0 md:left-64 bg-slate-50 p-8 overflow-hidden">
                            <!-- Header -->
                            <div class="flex justify-between mb-8">
                                <div class="w-48 h-8 bg-slate-200 rounded-lg"></div>
                                <div class="w-10 h-10 bg-slate-200 rounded-full"></div>
                            </div>
                            <!-- Cards -->
                            <div class="grid grid-cols-3 gap-6 mb-8">
                                <div class="h-32 bg-white rounded-xl shadow-sm p-4 border border-slate-100">
                                    <div class="w-8 h-8 bg-emerald-100 rounded-lg mb-4"></div>
                                    <div class="w-24 h-6 bg-slate-100 rounded mb-2"></div>
                                    <div class="w-32 h-8 bg-slate-200 rounded"></div>
                                </div>
                                <div class="h-32 bg-white rounded-xl shadow-sm p-4 border border-slate-100">
                                    <div class="w-8 h-8 bg-red-100 rounded-lg mb-4"></div>
                                    <div class="w-24 h-6 bg-slate-100 rounded mb-2"></div>
                                    <div class="w-32 h-8 bg-slate-200 rounded"></div>
                                </div>
                                <div class="h-32 bg-white rounded-xl shadow-sm p-4 border border-slate-100">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg mb-4"></div>
                                    <div class="w-24 h-6 bg-slate-100 rounded mb-2"></div>
                                    <div class="w-32 h-8 bg-slate-200 rounded"></div>
                                </div>
                            </div>
                            <!-- Chart Area -->
                            <div class="h-64 bg-white rounded-xl shadow-sm border border-slate-100 p-6 flex items-end justify-between gap-4">
                                <div class="w-full bg-emerald-500/20 rounded-t-lg h-[40%] relative group-hover:h-[60%] transition-all duration-1000"></div>
                                <div class="w-full bg-emerald-500/40 rounded-t-lg h-[60%] relative group-hover:h-[80%] transition-all duration-1000 delay-100"></div>
                                <div class="w-full bg-emerald-500/60 rounded-t-lg h-[30%] relative group-hover:h-[50%] transition-all duration-1000 delay-200"></div>
                                <div class="w-full bg-emerald-500/80 rounded-t-lg h-[80%] relative group-hover:h-[90%] transition-all duration-1000 delay-300"></div>
                                <div class="w-full bg-emerald-500 rounded-t-lg h-[50%] relative group-hover:h-[70%] transition-all duration-1000 delay-400"></div>
                            </div>
                        </div>
                        
                        <!-- Overlay Gradient -->
                        <div class="absolute inset-0 bg-gradient-to-t from-white via-transparent to-transparent opacity-20"></div>
                    </div>
                </div>
                
                <!-- Floating Badges -->
                <div class="absolute -right-8 top-20 bg-white p-4 rounded-xl shadow-xl border border-slate-100 animate-float hidden lg:block">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                            <i class="bi bi-arrow-down-left text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 font-medium">Pemasukan</p>
                            <p class="text-sm font-bold text-slate-900">+ Rp 5.000.000</p>
                        </div>
                    </div>
                </div>
                
                <div class="absolute -left-8 bottom-20 bg-white p-4 rounded-xl shadow-xl border border-slate-100 animate-float-reverse hidden lg:block">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center text-blue-600">
                            <i class="bi bi-check-lg text-xl"></i>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 font-medium">Target Tercapai</p>
                            <p class="text-sm font-bold text-slate-900">Liburan ke Jepang</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section (Bento Grid) -->
    <section id="fitur" class="py-24 bg-slate-50">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Semua Fitur yang Anda Butuhkan</h2>
                <p class="text-lg text-slate-600">Kami merancang setiap fitur dengan detail untuk memastikan pengalaman manajemen keuangan Anda berjalan mulus.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Feature 1: Large -->
                <div class="md:col-span-2 bg-white rounded-3xl p-8 shadow-sm border border-slate-100 hover:shadow-md transition-shadow relative overflow-hidden group">
                    <div class="relative z-10">
                        <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600 mb-6">
                            <i class="bi bi-pie-chart-fill text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900 mb-3">Analisis Visual Mendalam</h3>
                        <p class="text-slate-600 max-w-md">Pahami pola pengeluaran Anda dengan grafik interaktif yang mudah dimengerti. Ambil keputusan finansial berdasarkan data nyata, bukan asumsi.</p>
                    </div>
                    <div class="absolute right-0 bottom-0 w-1/2 h-full bg-gradient-to-l from-emerald-50 to-transparent opacity-50 group-hover:opacity-100 transition-opacity"></div>
                    <!-- Decorative Chart -->
                    <div class="absolute bottom-4 right-4 w-64 h-32 opacity-50 group-hover:opacity-100 transition-opacity transform group-hover:scale-105 duration-500">
                        <div class="flex items-end gap-2 h-full">
                            <div class="w-full bg-emerald-200 rounded-t h-[40%]"></div>
                            <div class="w-full bg-emerald-300 rounded-t h-[70%]"></div>
                            <div class="w-full bg-emerald-400 rounded-t h-[50%]"></div>
                            <div class="w-full bg-emerald-500 rounded-t h-[90%]"></div>
                            <div class="w-full bg-emerald-600 rounded-t h-[60%]"></div>
                        </div>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center text-blue-600 mb-6">
                        <i class="bi bi-wallet2 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Pencatatan Mudah</h3>
                    <p class="text-slate-600">Catat pemasukan dan pengeluaran dalam hitungan detik. Kategori yang fleksibel menyesuaikan kebutuhan Anda.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-slate-100 hover:shadow-md transition-shadow">
                    <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center text-purple-600 mb-6">
                        <i class="bi bi-stars text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Wishlist & Target</h3>
                    <p class="text-slate-600">Tetapkan tujuan finansial dan pantau progresnya. Kami bantu Anda tetap termotivasi hingga target tercapai.</p>
                </div>

                <!-- Feature 4: Large -->
                <div class="md:col-span-2 bg-slate-900 rounded-3xl p-8 shadow-sm border border-slate-800 hover:shadow-md transition-shadow relative overflow-hidden text-white">
                    <div class="relative z-10">
                        <div class="w-12 h-12 bg-white/10 rounded-xl flex items-center justify-center text-white mb-6 backdrop-blur-sm">
                            <i class="bi bi-shield-check text-2xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold mb-3">Keamanan Prioritas Utama</h3>
                        <p class="text-slate-300 max-w-md">Data Anda dienkripsi dengan standar industri. Kami menjaga privasi informasi keuangan Anda dengan serius.</p>
                    </div>
                    <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-500/20 rounded-full blur-3xl -mr-16 -mt-16"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="cara-kerja" class="py-24 bg-white">
        <div class="container mx-auto px-4">
            <div class="text-center mb-16 animate-fade-in-up">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Cara Kerja</h2>
                <p class="text-lg text-slate-600 max-w-2xl mx-auto">Mulai kelola keuangan Anda hanya dengan 3 langkah sederhana</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
                <!-- Connecting Line (Desktop) -->
                <div class="hidden md:block absolute top-12 left-[16%] right-[16%] h-0.5 bg-gradient-to-r from-emerald-100 via-teal-100 to-emerald-100 -z-10"></div>

                <!-- Step 1 -->
                <div class="text-center relative group">
                    <div class="w-24 h-24 mx-auto mb-6 bg-white border-4 border-emerald-50 rounded-full flex items-center justify-center relative z-10 group-hover:scale-110 transition-transform duration-300">
                        <div class="w-16 h-16 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg shadow-emerald-500/30">
                            1
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Daftar Akun</h3>
                    <p class="text-slate-600 leading-relaxed px-4">Buat akun gratis Anda dalam hitungan detik. Tanpa syarat ribet, langsung bisa dipakai.</p>
                </div>

                <!-- Step 2 -->
                <div class="text-center relative group">
                    <div class="w-24 h-24 mx-auto mb-6 bg-white border-4 border-emerald-50 rounded-full flex items-center justify-center relative z-10 group-hover:scale-110 transition-transform duration-300 delay-100">
                        <div class="w-16 h-16 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg shadow-emerald-500/30">
                            2
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Catat Transaksi</h3>
                    <p class="text-slate-600 leading-relaxed px-4">Input pemasukan dan pengeluaran harian Anda. Kategorikan untuk analisis yang lebih baik.</p>
                </div>

                <!-- Step 3 -->
                <div class="text-center relative group">
                    <div class="w-24 h-24 mx-auto mb-6 bg-white border-4 border-emerald-50 rounded-full flex items-center justify-center relative z-10 group-hover:scale-110 transition-transform duration-300 delay-200">
                        <div class="w-16 h-16 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg shadow-emerald-500/30">
                            3
                        </div>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-3">Pantau & Evaluasi</h3>
                    <p class="text-slate-600 leading-relaxed px-4">Lihat grafik keuangan Anda, pantau budget, dan capai tujuan finansial Anda.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="py-24 bg-slate-50">
        <div class="container mx-auto px-4 max-w-3xl">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Pertanyaan Umum</h2>
                <p class="text-lg text-slate-600">Jawaban untuk pertanyaan yang sering diajukan.</p>
            </div>

            <div class="space-y-4">
                <!-- FAQ Item 1 -->
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                    <button class="w-full px-6 py-4 text-left flex justify-between items-center focus:outline-none" onclick="toggleFaq(this)">
                        <span class="font-semibold text-slate-900">Apakah MyTabungan benar-benar gratis?</span>
                        <i class="bi bi-chevron-down text-slate-400 transition-transform duration-300"></i>
                    </button>
                    <div class="px-6 pb-4 hidden text-slate-600">
                        Ya, MyTabungan 100% gratis untuk digunakan dengan semua fitur utamanya. Kami berkomitmen untuk membantu literasi keuangan masyarakat.
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                    <button class="w-full px-6 py-4 text-left flex justify-between items-center focus:outline-none" onclick="toggleFaq(this)">
                        <span class="font-semibold text-slate-900">Apakah data saya aman?</span>
                        <i class="bi bi-chevron-down text-slate-400 transition-transform duration-300"></i>
                    </button>
                    <div class="px-6 pb-4 hidden text-slate-600">
                        Keamanan data adalah prioritas kami. Kami menggunakan enkripsi untuk melindungi kata sandi dan data sensitif Anda. Kami tidak membagikan data Anda ke pihak ketiga.
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                    <button class="w-full px-6 py-4 text-left flex justify-between items-center focus:outline-none" onclick="toggleFaq(this)">
                        <span class="font-semibold text-slate-900">Bisakah saya mengekspor laporan keuangan?</span>
                        <i class="bi bi-chevron-down text-slate-400 transition-transform duration-300"></i>
                    </button>
                    <div class="px-6 pb-4 hidden text-slate-600">
                        Tentu! Anda dapat melihat laporan bulanan dan membagikannya melalui link publik yang aman atau mencetaknya untuk arsip pribadi.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 bg-emerald-600 relative overflow-hidden">
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden">
            <div class="absolute -top-24 -right-24 w-96 h-96 rounded-full bg-emerald-500 opacity-50 blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-96 h-96 rounded-full bg-teal-500 opacity-50 blur-3xl"></div>
        </div>
        
        <div class="container mx-auto px-4 relative z-10 text-center">
            <h2 class="text-3xl md:text-5xl font-bold text-white mb-6">Siap Mengatur Keuangan Anda?</h2>
            <p class="text-emerald-100 text-lg mb-10 max-w-2xl mx-auto">Jangan biarkan uang mengatur Anda. Ambil kendali sekarang dan mulailah perjalanan menuju kebebasan finansial.</p>
            <a href="auth/register.php" class="inline-flex items-center justify-center px-8 py-4 text-lg font-bold text-emerald-600 transition-all duration-200 bg-white rounded-full hover:bg-emerald-50 hover:scale-105 hover:shadow-2xl">
                Buat Akun Gratis
                <i class="bi bi-arrow-right ml-2"></i>
            </a>
            <p class="mt-6 text-sm text-emerald-200">Tidak perlu kartu kredit • Setup dalam 1 menit</p>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-300 py-12 border-t border-slate-800">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-2">
                    <a href="#" class="flex items-center gap-2 mb-6">
                        <div class="w-8 h-8 bg-emerald-500 rounded-lg flex items-center justify-center text-white">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <span class="text-xl font-bold text-white">MyTabungan</span>
                    </a>
                    <p class="text-slate-400 mb-6 max-w-sm">
                        Platform manajemen keuangan pribadi yang didesain untuk membantu Anda mencapai tujuan finansial dengan lebih mudah dan menyenangkan.
                    </p>
                    <div class="flex gap-4">
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all">
                            <i class="bi bi-twitter"></i>
                        </a>
                        <a href="#" class="w-10 h-10 rounded-full bg-slate-800 flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-all">
                            <i class="bi bi-linkedin"></i>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h6 class="text-white font-bold mb-6">Produk</h6>
                    <ul class="space-y-4">
                        <li><a href="#fitur" class="hover:text-emerald-400 transition-colors">Fitur</a></li>
                        <li><a href="#cara-kerja" class="hover:text-emerald-400 transition-colors">Cara Kerja</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition-colors">Harga</a></li>
                        <li><a href="#faq" class="hover:text-emerald-400 transition-colors">FAQ</a></li>
                    </ul>
                </div>
                
                <div>
                    <h6 class="text-white font-bold mb-6">Perusahaan</h6>
                    <ul class="space-y-4">
                        <li><a href="#" class="hover:text-emerald-400 transition-colors">Tentang Kami</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition-colors">Blog</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition-colors">Karir</a></li>
                        <li><a href="#" class="hover:text-emerald-400 transition-colors">Kontak</a></li>
                    </ul>
                </div>
            </div>
            
            <div class="pt-8 border-t border-slate-800 text-center md:text-left flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm text-slate-500">
                    &copy; <?php echo date('Y'); ?> MyTabungan. All rights reserved.
                </p>
                <p class="text-sm text-slate-500">
                    Dibuat dengan <i class="bi bi-heart-fill text-red-500 mx-1"></i> oleh Izzat Fakhar Assyakur
                </p>
            </div>
        </div>
    </footer>

    <script>
        // Navbar Scroll Effect
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('nav');
            if (window.scrollY > 10) {
                navbar.classList.add('bg-white/90', 'backdrop-blur-md', 'shadow-sm');
            } else {
                navbar.classList.remove('bg-white/90', 'backdrop-blur-md', 'shadow-sm');
            }
        });

        // FAQ Toggle
        function toggleFaq(button) {
            const content = button.nextElementSibling;
            const icon = button.querySelector('i');
            
            content.classList.toggle('hidden');
            icon.classList.toggle('rotate-180');
        }
    </script>
    <!-- Video Demo Modal -->
    <?php include 'modal/video_demo_modal.php'; ?>
</body>
</html>