<?php
/**
 * Forgot Password Page
 * MyTabungan - Personal Finance Management
 */

require_once __DIR__ . '/auth/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$step = 'email'; // email or reset
$error = '';
$success = '';
$toastType = ''; // error or success
$errorType = ''; // email_not_found, email_invalid, password_mismatch, session_expired, etc
$email = '';
$userName = '';

// Check if we have a pending reset FIRST
if (isset($_SESSION['reset_email'])) {
    $step = 'reset';
    $email = $_SESSION['reset_email'];
    $userName = $_SESSION['reset_user'] ?? '';
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // Step 1: Check email
    if (isset($_POST['check_email'])) {
        $email = $_POST['email'] ?? '';
        
        // Validate email format first
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Format email tidak valid.';
            $errorType = 'email_invalid';
            $toastType = 'error';
            $step = 'email';
        } else {
            $emailCheck = checkEmailExists($email);
            
            if ($emailCheck['exists']) {
                $step = 'reset';
                $userName = $emailCheck['nama'];
                // Store email in session for security
                $_SESSION['reset_email'] = $email;
                $_SESSION['reset_user'] = $userName;
            } else {
                $error = 'Email tidak ditemukan dalam sistem kami.';
                $errorType = 'email_not_found';
                $toastType = 'error';
                $step = 'email';
            }
        }
    }
    
    // Step 2: Reset password
    if (isset($_POST['reset_password'])) {
        $email = $_SESSION['reset_email'] ?? '';
        $userName = $_SESSION['reset_user'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        // Keep in reset step
        $step = 'reset';
        
        if (empty($email)) {
            $error = 'Sesi telah berakhir. Silakan mulai ulang.';
            $errorType = 'session_expired';
            $toastType = 'error';
            $step = 'email';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'Konfirmasi password tidak cocok.';
            $errorType = 'password_mismatch';
            $toastType = 'error';
        } elseif (strlen($newPassword) < 6) {
            $error = 'Password minimal 6 karakter.';
            $errorType = 'password_short';
            $toastType = 'error';
        } else {
            $result = resetPasswordByEmail($email, $newPassword);
            
            if ($result['success']) {
                // Clear session
                unset($_SESSION['reset_email']);
                unset($_SESSION['reset_user']);
                
                setFlashMessage('success', 'Password berhasil direset! Silakan login dengan password baru.');
                redirect('login.php');
            } else {
                $error = $result['message'];
                $errorType = 'reset_failed';
                $toastType = 'error';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - MyTabungan</title>
    
    <!-- Compiled Tailwind CSS -->
    <link rel="stylesheet" href="assets/css/styles.css">
    
    <!-- Google Fonts - Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <style>
        /* Toast Animation */
        @keyframes toastSlideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
        
        @keyframes toastSlideOut {
            from { transform: translateX(0); opacity: 1; }
            to { transform: translateX(100%); opacity: 0; }
        }
        
        .toast-enter {
            animation: toastSlideIn 0.3s ease forwards;
        }
        
        .toast-exit {
            animation: toastSlideOut 0.3s ease forwards;
        }
    </style>
</head>
<body class="bg-slate-50">
    
    <!-- Toast Notification -->
    <?php if ($error): ?>
    <div id="toast" class="fixed top-4 right-4 z-50 toast-enter">
        <div class="bg-white border-l-4 <?= $errorType === 'password_mismatch' ? 'border-orange-500' : 'border-red-500' ?> shadow-lg rounded-lg p-4 flex items-start gap-3 max-w-sm">
            <div class="<?= $errorType === 'password_mismatch' ? 'text-orange-500' : 'text-red-500' ?>">
                <i class="bi <?= $errorType === 'password_mismatch' ? 'bi-exclamation-triangle-fill' : 'bi-x-circle-fill' ?> text-xl"></i>
            </div>
            <div class="flex-1">
                <p class="font-semibold text-slate-800 text-sm">
                    <?php
                    switch($errorType) {
                        case 'email_not_found': echo 'Email Tidak Ditemukan'; break;
                        case 'email_invalid': echo 'Email Tidak Valid'; break;
                        case 'password_mismatch': echo 'Password Tidak Cocok'; break;
                        case 'password_short': echo 'Password Terlalu Pendek'; break;
                        case 'session_expired': echo 'Sesi Berakhir'; break;
                        default: echo 'Terjadi Kesalahan';
                    }
                    ?>
                </p>
                <p class="text-slate-500 text-sm"><?= htmlspecialchars($error) ?></p>
            </div>
            <button onclick="closeToast()" class="text-slate-400 hover:text-slate-600 transition-colors">
                <i class="bi bi-x text-xl"></i>
            </button>
        </div>
    </div>
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
                    Lupa Password?<br>
                    <span class="text-emerald-200">Tenang Saja!</span>
                </h1>
                
                <p class="text-lg text-white/80 mb-10 max-w-md">
                    Kami akan membantu Anda mengatur ulang password akun MyTabungan Anda dengan mudah.
                </p>
                
                <!-- Steps -->
                <div class="space-y-4">
                    <div class="auth-feature-item flex items-center gap-4 bg-white/10 backdrop-blur rounded-xl p-4">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center font-bold">1</div>
                        <div>
                            <p class="font-semibold">Masukkan Email</p>
                            <p class="text-sm text-white/70">Email yang terdaftar di akun Anda</p>
                        </div>
                    </div>
                    
                    <div class="auth-feature-item flex items-center gap-4 bg-white/10 backdrop-blur rounded-xl p-4">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center font-bold">2</div>
                        <div>
                            <p class="font-semibold">Buat Password Baru</p>
                            <p class="text-sm text-white/70">Minimal 6 karakter</p>
                        </div>
                    </div>
                    
                    <div class="auth-feature-item flex items-center gap-4 bg-white/10 backdrop-blur rounded-xl p-4">
                        <div class="w-10 h-10 bg-white/20 rounded-lg flex items-center justify-center font-bold">3</div>
                        <div>
                            <p class="font-semibold">Login Kembali</p>
                            <p class="text-sm text-white/70">Gunakan password baru</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Panel - Form -->
        <div class="flex flex-col justify-center p-6 sm:p-8 lg:p-16 relative">
            
            <!-- Background Decorations (Mobile) -->
            <div class="lg:hidden absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-emerald-200/40 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-teal-200/40 rounded-full blur-3xl"></div>
            </div>
            
            <div class="w-full max-w-md mx-auto relative z-10">
                
                <!-- Back to Login -->
                <a href="login.php" class="inline-flex items-center gap-2 text-slate-500 hover:text-emerald-600 mb-8 transition-colors group">
                    <i class="bi bi-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                    <span>Kembali ke Login</span>
                </a>
                
                <!-- Mobile Logo -->
                <div class="lg:hidden flex items-center gap-3 mb-8">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <i class="bi bi-wallet2 text-white text-2xl"></i>
                    </div>
                    <span class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">MyTabungan</span>
                </div>
                
                <!-- Step Indicator -->
                <div class="flex items-center gap-3 mb-8">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full <?= $step === 'email' ? 'bg-emerald-500 text-white' : 'bg-emerald-100 text-emerald-600' ?> flex items-center justify-center font-semibold text-sm">1</div>
                        <span class="text-sm <?= $step === 'email' ? 'text-emerald-600 font-medium' : 'text-slate-400' ?>">Email</span>
                    </div>
                    <div class="flex-1 h-0.5 <?= $step === 'reset' ? 'bg-emerald-500' : 'bg-slate-200' ?>"></div>
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full <?= $step === 'reset' ? 'bg-emerald-500 text-white' : 'bg-slate-200 text-slate-400' ?> flex items-center justify-center font-semibold text-sm">2</div>
                        <span class="text-sm <?= $step === 'reset' ? 'text-emerald-600 font-medium' : 'text-slate-400' ?>">Reset</span>
                    </div>
                </div>
                
                <?php if ($step === 'email'): ?>
                <!-- Step 1: Enter Email -->
                <div>
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-slate-800 mb-2">Lupa Password? 🔐</h1>
                        <p class="text-slate-500">Masukkan email yang terdaftar di akun Anda</p>
                    </div>
                    
                    <form method="POST" action="" class="space-y-5">
                        <div>
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
                                    autofocus
                                >
                            </div>
                        </div>
                        
                        <button 
                            type="submit"
                            name="check_email"
                            class="btn-shine w-full py-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/40 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2"
                        >
                            <span>Lanjutkan</span>
                            <i class="bi bi-arrow-right"></i>
                        </button>
                    </form>
                </div>
                
                <?php else: ?>
                <!-- Step 2: Reset Password -->
                <div>
                    <div class="mb-8">
                        <h1 class="text-3xl font-bold text-slate-800 mb-2">Buat Password Baru 🔑</h1>
                        <p class="text-slate-500">Halo <span class="font-medium text-emerald-600"><?= htmlspecialchars($userName) ?></span>, silakan buat password baru</p>
                    </div>
                    
                    <form method="POST" action="" class="space-y-5">
                        <!-- Email Display -->
                        <div class="bg-slate-50 border border-slate-200 rounded-xl p-4 flex items-center gap-3">
                            <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
                                <i class="bi bi-envelope-check text-emerald-600 text-lg"></i>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400">Email Terverifikasi</p>
                                <p class="text-sm font-medium text-slate-700"><?= htmlspecialchars($email) ?></p>
                            </div>
                        </div>
                        
                        <!-- New Password -->
                        <div>
                            <label for="new_password" class="block text-sm font-medium text-slate-700 mb-2">
                                Password Baru
                            </label>
                            <div class="relative">
                                <i class="bi bi-lock absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input 
                                    type="password" 
                                    id="new_password" 
                                    name="new_password" 
                                    required
                                    minlength="6"
                                    class="w-full pl-11 pr-12 py-3.5 rounded-xl border-2 border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-white"
                                    placeholder="Minimal 6 karakter"
                                    oninput="checkPasswordStrength(this.value)"
                                >
                                <button 
                                    type="button" 
                                    onclick="togglePassword('new_password', this)"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-emerald-600 transition-colors"
                                >
                                    <i class="bi bi-eye text-lg"></i>
                                </button>
                            </div>
                            <div class="mt-2">
                                <div class="flex gap-1">
                                    <div id="strength-1" class="password-strength flex-1 bg-slate-200"></div>
                                    <div id="strength-2" class="password-strength flex-1 bg-slate-200"></div>
                                    <div id="strength-3" class="password-strength flex-1 bg-slate-200"></div>
                                    <div id="strength-4" class="password-strength flex-1 bg-slate-200"></div>
                                </div>
                                <p id="strength-text" class="text-xs text-slate-400 mt-1"></p>
                            </div>
                        </div>
                        
                        <!-- Confirm Password -->
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-slate-700 mb-2">
                                Konfirmasi Password
                            </label>
                            <div class="relative">
                                <i class="bi bi-lock-fill absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                                <input 
                                    type="password" 
                                    id="confirm_password" 
                                    name="confirm_password" 
                                    required
                                    class="w-full pl-11 pr-12 py-3.5 rounded-xl border-2 border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-white"
                                    placeholder="Ulangi password baru"
                                    oninput="checkPasswordMatch()"
                                >
                                <button 
                                    type="button" 
                                    onclick="togglePassword('confirm_password', this)"
                                    class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-emerald-600 transition-colors"
                                >
                                    <i class="bi bi-eye text-lg"></i>
                                </button>
                            </div>
                            <p id="password-match" class="text-xs mt-1 hidden"></p>
                        </div>
                        
                        <button 
                            type="submit"
                            name="reset_password"
                            class="btn-shine w-full py-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/40 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2"
                        >
                            <span>Reset Password</span>
                            <i class="bi bi-check-lg"></i>
                        </button>
                    </form>
                    
                    <!-- Start Over -->
                    <div class="mt-6 text-center">
                        <a href="forgot_password.php?restart=1" class="text-slate-500 hover:text-emerald-600 text-sm">
                            <i class="bi bi-arrow-counterclockwise mr-1"></i>
                            Mulai ulang dengan email lain
                        </a>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Footer -->
                <p class="text-center text-slate-400 text-sm mt-8">
                    &copy; <?= date('Y') ?> MyTabungan. Kelola Keuangan dengan Mudah.
                </p>
            </div>
        </div>
    </div>
    
    <script>
        // Auto-hide toast after 4 seconds
        const toast = document.getElementById('toast');
        if (toast) {
            setTimeout(() => {
                closeToast();
            }, 5000);
        }
        
        function closeToast() {
            const toast = document.getElementById('toast');
            if (toast) {
                toast.classList.remove('toast-enter');
                toast.classList.add('toast-exit');
                setTimeout(() => {
                    toast.remove();
                }, 300);
            }
        }
        
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
        
        function checkPasswordStrength(password) {
            const bars = [
                document.getElementById('strength-1'),
                document.getElementById('strength-2'),
                document.getElementById('strength-3'),
                document.getElementById('strength-4')
            ];
            const text = document.getElementById('strength-text');
            
            if (!bars[0]) return;
            
            let strength = 0;
            
            if (password.length >= 6) strength++;
            if (password.length >= 8) strength++;
            if (/[A-Z]/.test(password) && /[a-z]/.test(password)) strength++;
            if (/[0-9]/.test(password) || /[^A-Za-z0-9]/.test(password)) strength++;
            
            const colors = ['bg-red-500', 'bg-orange-500', 'bg-yellow-500', 'bg-emerald-500'];
            const texts = ['Sangat Lemah', 'Lemah', 'Sedang', 'Kuat'];
            const textColors = ['text-red-500', 'text-orange-500', 'text-yellow-500', 'text-emerald-500'];
            
            bars.forEach((bar, index) => {
                bar.className = 'password-strength flex-1 ' + (index < strength ? colors[strength - 1] : 'bg-slate-200');
            });
            
            if (password.length > 0) {
                text.textContent = texts[strength - 1] || 'Sangat Lemah';
                text.className = 'text-xs mt-1 ' + (textColors[strength - 1] || 'text-red-500');
            } else {
                text.textContent = '';
            }
        }
        
        function checkPasswordMatch() {
            const password = document.getElementById('new_password').value;
            const confirm = document.getElementById('confirm_password').value;
            const text = document.getElementById('password-match');
            
            if (confirm.length > 0) {
                text.classList.remove('hidden');
                if (password === confirm) {
                    text.textContent = '✓ Password cocok';
                    text.className = 'text-xs mt-1 text-emerald-500';
                } else {
                    text.textContent = '✗ Password tidak cocok';
                    text.className = 'text-xs mt-1 text-red-500';
                }
            } else {
                text.classList.add('hidden');
            }
        }
    </script>
    
    <?php
    // Handle restart
    if (isset($_GET['restart'])) {
        unset($_SESSION['reset_email']);
        unset($_SESSION['reset_user']);
        echo '<script>window.location.href = "forgot_password.php";</script>';
    }
    ?>
</body>
</html>
