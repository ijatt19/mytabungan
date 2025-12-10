<?php
/**
 * Login Page
 * MyTabungan - Personal Finance Management
 */

require_once __DIR__ . '/auth/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$flash = getFlashMessage();
$successMessage = ($flash && $flash['type'] === 'success') ? $flash['message'] : null;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    $result = loginUser($email, $password);
    
    if ($result['success']) {
        setFlashMessage('success', 'Selamat datang kembali, ' . getCurrentUserName() . '!');
        redirect('dashboard.php');
    } else {
        $error = $result['message'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MyTabungan</title>
    
    <!-- Compiled Tailwind CSS -->
    <link rel="stylesheet" href="assets/css/styles.css">
    
    <!-- Google Fonts - Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
</head>
<body class="bg-slate-50">
    
    <!-- Success Toast -->
    <?php if ($successMessage): ?>
    <div id="successToast" class="fixed top-4 right-4 z-50" style="animation: toastSlideIn 0.3s ease forwards;">
        <div class="bg-white border-l-4 border-emerald-500 shadow-lg rounded-lg p-4 flex items-start gap-3 max-w-sm">
            <div class="text-emerald-500">
                <i class="bi bi-check-circle-fill text-xl"></i>
            </div>
            <div class="flex-1">
                <p class="font-semibold text-slate-800 text-sm">Berhasil!</p>
                <p class="text-slate-500 text-sm"><?= htmlspecialchars($successMessage) ?></p>
            </div>
            <button onclick="closeSuccessToast()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x text-xl"></i>
            </button>
        </div>
    </div>
    <style>
        @keyframes toastSlideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes toastSlideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
    </style>
    <script>
        setTimeout(() => closeSuccessToast(), 5000);
        function closeSuccessToast() {
            const toast = document.getElementById('successToast');
            if (toast) {
                toast.style.animation = 'toastSlideOut 0.3s ease forwards';
                setTimeout(() => toast.remove(), 300);
            }
        }
    </script>
    <?php endif; ?>
    
    <div class="auth-container">
        
        <!-- Left Panel - Branding (Hidden on Mobile) -->
        <div class="hidden lg:flex hero-gradient relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0">
                <div class="absolute top-20 left-20 w-72 h-72 bg-white/10 rounded-full blur-3xl"></div>
                <div class="absolute bottom-20 right-20 w-96 h-96 bg-teal-400/20 rounded-full blur-3xl"></div>
                <div class="absolute top-1/2 left-1/3 w-64 h-64 bg-cyan-400/10 rounded-full blur-2xl"></div>
            </div>
            
            <!-- Content -->
            <div class="relative z-10 flex flex-col justify-center p-12 lg:p-16 text-white">
                <!-- Logo -->
                <div class="flex items-center gap-4 mb-12">
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-xl rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="bi bi-wallet2 text-3xl"></i>
                    </div>
                    <span class="text-3xl font-bold">MyTabungan</span>
                </div>
                
                <!-- Headline -->
                <h1 class="text-4xl lg:text-5xl font-bold leading-tight mb-6">
                    Kelola Keuangan<br>
                    <span class="text-emerald-200">Lebih Pintar</span>
                </h1>
                
                <p class="text-lg text-white/80 mb-10 max-w-md">
                    Pantau pemasukan, kendalikan pengeluaran, dan wujudkan impian finansial Anda.
                </p>
                
                <!-- Features -->
                <div class="space-y-4">
                    <div class="auth-feature-item flex items-center gap-4 bg-white/10 backdrop-blur rounded-xl p-4">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="bi bi-graph-up-arrow text-xl"></i>
                        </div>
                        <div>
                            <p class="font-semibold">Grafik Interaktif</p>
                            <p class="text-sm text-white/70">Visualisasi keuangan real-time</p>
                        </div>
                    </div>
                    
                    <div class="auth-feature-item flex items-center gap-4 bg-white/10 backdrop-blur rounded-xl p-4">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="bi bi-shield-check text-xl"></i>
                        </div>
                        <div>
                            <p class="font-semibold">Aman & Privat</p>
                            <p class="text-sm text-white/70">Data terenkripsi end-to-end</p>
                        </div>
                    </div>
                    
                    <div class="auth-feature-item flex items-center gap-4 bg-white/10 backdrop-blur rounded-xl p-4">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center">
                            <i class="bi bi-lightning-charge text-xl"></i>
                        </div>
                        <div>
                            <p class="font-semibold">Super Cepat</p>
                            <p class="text-sm text-white/70">Performa optimal di semua device</p>
                        </div>
                    </div>
                </div>
                
                <!-- Stats -->
                <div class="mt-12 pt-8 border-t border-white/20 flex gap-12">
                    <div>
                        <p class="text-3xl font-bold">100%</p>
                        <p class="text-sm text-white/70">Gratis Selamanya</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold">0</p>
                        <p class="text-sm text-white/70">Iklan Mengganggu</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Panel - Login Form -->
        <div class="flex flex-col justify-center p-6 sm:p-8 lg:p-16 relative">
            
            <!-- Background Decorations (Mobile) -->
            <div class="lg:hidden absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-emerald-200/40 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-teal-200/40 rounded-full blur-3xl"></div>
            </div>
            
            <div class="w-full max-w-md mx-auto relative z-10">
                
                <!-- Back to Home -->
                <a href="index.php" class="inline-flex items-center gap-2 text-slate-500 hover:text-emerald-600 mb-8 transition-colors group">
                    <i class="bi bi-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                    <span>Kembali ke Beranda</span>
                </a>
                
                <!-- Mobile Logo -->
                <div class="lg:hidden flex items-center gap-3 mb-8">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <i class="bi bi-wallet2 text-white text-2xl"></i>
                    </div>
                    <span class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">MyTabungan</span>
                </div>
                
                <!-- Header -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold text-slate-800 mb-2">Selamat Datang! 👋</h1>
                    <p class="text-slate-500">Masuk untuk mengelola keuangan Anda</p>
                </div>
                
                <!-- Error Message -->
                <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 flex items-start gap-3 animate-fade-in-up">
                    <i class="bi bi-exclamation-circle-fill text-lg mt-0.5"></i>
                    <div>
                        <p class="font-medium">Login Gagal</p>
                        <p class="text-sm text-red-600"><?= htmlspecialchars($error) ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Form -->
                <form method="POST" action="" class="space-y-5">
                    <!-- Email -->
                    <div class="input-focus-effect">
                        <label for="email" class="block text-sm font-medium text-slate-700 mb-2">
                            Email
                        </label>
                        <div class="relative">
                            <i class="bi bi-envelope absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input 
                                type="email" 
                                id="email" 
                                name="email" 
                                required
                                class="w-full pl-11 pr-4 py-3.5 rounded-xl border-2 border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-white"
                                placeholder="nama@email.com"
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            >
                        </div>
                    </div>
                    
                    <!-- Password -->
                    <div class="input-focus-effect">
                        <label for="password" class="block text-sm font-medium text-slate-700 mb-2">
                            Password
                        </label>
                        <div class="relative">
                            <i class="bi bi-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                class="w-full pl-11 pr-12 py-3.5 rounded-xl border-2 border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-white"
                                placeholder="Masukkan password"
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword('password', this)"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-emerald-600 transition-colors"
                            >
                                <i class="bi bi-eye text-lg"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Remember & Forgot -->
                    <div class="flex items-center justify-between text-sm">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" class="w-4 h-4 rounded border-slate-300 text-emerald-500 focus:ring-emerald-500">
                            <span class="text-slate-600">Ingat saya</span>
                        </label>
                        <a href="forgot_password.php" class="text-emerald-600 hover:text-emerald-700 font-medium">Lupa password?</a>
                    </div>
                    
                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="btn-shine w-full py-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/40 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2"
                    >
                        <span>Masuk ke Akun</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
                
                <!-- Divider -->
                <div class="relative my-8">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-slate-50 text-slate-400">atau</span>
                    </div>
                </div>
                
                <!-- Register Link -->
                <div class="text-center">
                    <p class="text-slate-500 mb-4">Belum punya akun?</p>
                    <a href="register.php" class="inline-flex items-center justify-center gap-2 w-full py-3.5 border-2 border-emerald-500 text-emerald-600 font-semibold rounded-xl hover:bg-emerald-50 transition-all duration-300">
                        <i class="bi bi-person-plus"></i>
                        Daftar Gratis Sekarang
                    </a>
                </div>
                
                <!-- Footer -->
                <p class="text-center text-slate-400 text-sm mt-8">
                    &copy; <?= date('Y') ?> MyTabungan. Kelola Keuangan dengan Mudah.
                </p>
            </div>
        </div>
    </div>
    
    <script>
        function togglePassword(inputId, button) {
            const input = document.getElementById(inputId);
            const icon = button.querySelector('i');
            
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash text-lg';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye text-lg';
            }
        }
    </script>
</body>
</html>
