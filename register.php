<?php
/**
 * Registration Page
 * MyTabungan - Personal Finance Management
 */

require_once __DIR__ . '/auth/auth.php';

// Redirect if already logged in
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$error = '';
$errorType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = $_POST['nama'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email tidak valid.';
        $errorType = 'email_invalid';
    }
    // Validate password confirmation
    elseif ($password !== $password_confirm) {
        $error = 'Konfirmasi password tidak cocok.';
        $errorType = 'password_mismatch';
    }
    // Validate password length
    elseif (strlen($password) < 6) {
        $error = 'Password minimal 6 karakter.';
        $errorType = 'password_short';
    }
    // Validate name
    elseif (empty(trim($nama))) {
        $error = 'Nama tidak boleh kosong.';
        $errorType = 'name_empty';
    }
    else {
        $result = registerUser($nama, $email, $password);
        
        if ($result['success']) {
            setFlashMessage('success', $result['message']);
            redirect('login.php');
        } else {
            $error = $result['message'];
            $errorType = 'register_failed';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar - MyTabungan</title>
    
    <!-- Compiled Tailwind CSS -->
    <link rel="stylesheet" href="assets/css/styles.css">
    
    <!-- Google Fonts - Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <style>
        @keyframes toastSlideIn { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
        @keyframes toastSlideOut { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
        .toast-enter { animation: toastSlideIn 0.3s ease forwards; }
        .toast-exit { animation: toastSlideOut 0.3s ease forwards; }
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
                        case 'email_invalid': echo 'Email Tidak Valid'; break;
                        case 'password_mismatch': echo 'Password Tidak Cocok'; break;
                        case 'password_short': echo 'Password Terlalu Pendek'; break;
                        case 'name_empty': echo 'Nama Kosong'; break;
                        case 'register_failed': echo 'Pendaftaran Gagal'; break;
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
    <script>
        setTimeout(() => closeToast(), 5000);
        function closeToast() {
            const toast = document.getElementById('toast');
            if (toast) {
                toast.classList.remove('toast-enter');
                toast.classList.add('toast-exit');
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
                    Mulai Perjalanan<br>
                    <span class="text-emerald-200">Finansialmu</span>
                </h1>
                
                <p class="text-lg text-white/80 mb-10 max-w-md">
                    Daftar gratis dan mulai kelola keuanganmu dengan lebih baik. Hanya butuh 30 detik!
                </p>
                
                <!-- Steps -->
                <div class="space-y-3">
                    <div class="auth-feature-item form-step">
                        <div class="step-number">1</div>
                        <div>
                            <p class="font-semibold">Isi Data Diri</p>
                            <p class="text-sm text-white/70">Nama, email, dan password</p>
                        </div>
                    </div>
                    
                    <div class="auth-feature-item form-step">
                        <div class="step-number">2</div>
                        <div>
                            <p class="font-semibold">Buat Kategori</p>
                            <p class="text-sm text-white/70">Sesuaikan dengan kebutuhanmu</p>
                        </div>
                    </div>
                    
                    <div class="auth-feature-item form-step">
                        <div class="step-number">3</div>
                        <div>
                            <p class="font-semibold">Mulai Catat!</p>
                            <p class="text-sm text-white/70">Pantau keuanganmu setiap hari</p>
                        </div>
                    </div>
                </div>
                
                <!-- Benefits -->
                <div class="mt-12 pt-8 border-t border-white/20">
                    <p class="text-sm text-white/70 mb-4">Yang kamu dapatkan:</p>
                    <div class="flex flex-wrap gap-3">
                        <span class="px-3 py-1.5 bg-white/10 backdrop-blur rounded-full text-sm">
                            <i class="bi bi-check-lg mr-1"></i> 100% Gratis
                        </span>
                        <span class="px-3 py-1.5 bg-white/10 backdrop-blur rounded-full text-sm">
                            <i class="bi bi-check-lg mr-1"></i> Tanpa Iklan
                        </span>
                        <span class="px-3 py-1.5 bg-white/10 backdrop-blur rounded-full text-sm">
                            <i class="bi bi-check-lg mr-1"></i> Data Aman
                        </span>
                        <span class="px-3 py-1.5 bg-white/10 backdrop-blur rounded-full text-sm">
                            <i class="bi bi-check-lg mr-1"></i> Grafik Interaktif
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Right Panel - Register Form -->
        <div class="flex flex-col justify-center p-6 sm:p-8 lg:p-12 relative overflow-y-auto">
            
            <!-- Background Decorations (Mobile) -->
            <div class="lg:hidden absolute inset-0 overflow-hidden pointer-events-none">
                <div class="absolute -top-20 -right-20 w-64 h-64 bg-emerald-200/40 rounded-full blur-3xl"></div>
                <div class="absolute -bottom-20 -left-20 w-64 h-64 bg-teal-200/40 rounded-full blur-3xl"></div>
            </div>
            
            <div class="w-full max-w-md mx-auto relative z-10 py-8">
                
                <!-- Back to Home -->
                <a href="index.php" class="inline-flex items-center gap-2 text-slate-500 hover:text-emerald-600 mb-6 transition-colors group">
                    <i class="bi bi-arrow-left group-hover:-translate-x-1 transition-transform"></i>
                    <span>Kembali ke Beranda</span>
                </a>
                
                <!-- Mobile Logo -->
                <div class="lg:hidden flex items-center gap-3 mb-6">
                    <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center shadow-lg shadow-emerald-500/30">
                        <i class="bi bi-wallet2 text-white text-2xl"></i>
                    </div>
                    <span class="text-2xl font-bold bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">MyTabungan</span>
                </div>
                
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-3xl font-bold text-slate-800 mb-2">Buat Akun Baru ✨</h1>
                    <p class="text-slate-500">Daftar gratis dan mulai kelola keuanganmu</p>
                </div>
                
                <!-- Error Message -->
                <?php if ($error): ?>
                <div class="bg-red-50 border border-red-200 text-red-700 p-4 rounded-xl mb-6 flex items-start gap-3 animate-fade-in-up">
                    <i class="bi bi-exclamation-circle-fill text-lg mt-0.5"></i>
                    <div>
                        <p class="font-medium">Pendaftaran Gagal</p>
                        <p class="text-sm text-red-600"><?= htmlspecialchars($error) ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Form -->
                <form method="POST" action="" class="space-y-4" id="registerForm">
                    <!-- Name -->
                    <div>
                        <label for="nama" class="block text-sm font-medium text-slate-700 mb-2">
                            Nama Lengkap
                        </label>
                        <div class="relative">
                            <i class="bi bi-person absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input 
                                type="text" 
                                id="nama" 
                                name="nama" 
                                required
                                class="w-full pl-11 pr-4 py-3 rounded-xl border-2 border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-white"
                                placeholder="Masukkan nama lengkap"
                                value="<?= htmlspecialchars($_POST['nama'] ?? '') ?>"
                            >
                        </div>
                    </div>
                    
                    <!-- Email -->
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
                                class="w-full pl-11 pr-4 py-3 rounded-xl border-2 border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-white"
                                placeholder="nama@email.com"
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                            >
                        </div>
                    </div>
                    
                    <!-- Password -->
                    <div>
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
                                minlength="6"
                                class="w-full pl-11 pr-12 py-3 rounded-xl border-2 border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-white"
                                placeholder="Minimal 6 karakter"
                                oninput="checkPasswordStrength(this.value)"
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword('password', this)"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-emerald-600 transition-colors"
                            >
                                <i class="bi bi-eye text-lg"></i>
                            </button>
                        </div>
                        <!-- Password Strength Indicator -->
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
                        <label for="password_confirm" class="block text-sm font-medium text-slate-700 mb-2">
                            Konfirmasi Password
                        </label>
                        <div class="relative">
                            <i class="bi bi-lock-fill absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>
                            <input 
                                type="password" 
                                id="password_confirm" 
                                name="password_confirm" 
                                required
                                class="w-full pl-11 pr-12 py-3 rounded-xl border-2 border-slate-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-white"
                                placeholder="Ulangi password"
                                oninput="checkPasswordMatch()"
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword('password_confirm', this)"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-emerald-600 transition-colors"
                            >
                                <i class="bi bi-eye text-lg"></i>
                            </button>
                        </div>
                        <p id="password-match" class="text-xs mt-1 hidden"></p>
                    </div>
                    
                    <!-- Terms -->
                    <div class="flex items-start gap-3 bg-slate-50 p-4 rounded-xl border border-slate-100">
                        <input 
                            type="checkbox" 
                            id="terms" 
                            required
                            class="mt-0.5 w-5 h-5 text-emerald-500 border-slate-300 rounded focus:ring-emerald-500"
                        >
                        <label for="terms" class="text-sm text-slate-600">
                            Saya menyetujui <button type="button" onclick="openModal('termsModal')" class="text-emerald-600 hover:underline font-medium">Syarat & Ketentuan</button> 
                            serta <button type="button" onclick="openModal('privacyModal')" class="text-emerald-600 hover:underline font-medium">Kebijakan Privasi</button>
                        </label>
                    </div>
                    
                    <!-- Submit Button -->
                    <button 
                        type="submit" 
                        class="btn-shine w-full py-4 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold rounded-xl shadow-lg shadow-emerald-500/30 hover:shadow-xl hover:shadow-emerald-500/40 hover:-translate-y-0.5 transition-all duration-300 flex items-center justify-center gap-2"
                    >
                        <span>Daftar Sekarang</span>
                        <i class="bi bi-arrow-right"></i>
                    </button>
                </form>
                
                <!-- Divider -->
                <div class="relative my-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-slate-200"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-4 bg-slate-50 text-slate-400">atau</span>
                    </div>
                </div>
                
                <!-- Login Link -->
                <div class="text-center">
                    <p class="text-slate-500 mb-4">Sudah punya akun?</p>
                    <a href="login.php" class="inline-flex items-center justify-center gap-2 w-full py-3 border-2 border-emerald-500 text-emerald-600 font-semibold rounded-xl hover:bg-emerald-50 transition-all duration-300">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Masuk ke Akun
                    </a>
                </div>
                
                <!-- Footer -->
                <p class="text-center text-slate-400 text-sm mt-6">
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
        
        function checkPasswordStrength(password) {
            const bars = [
                document.getElementById('strength-1'),
                document.getElementById('strength-2'),
                document.getElementById('strength-3'),
                document.getElementById('strength-4')
            ];
            const text = document.getElementById('strength-text');
            
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
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('password_confirm').value;
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
        
        // Modal functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.remove('hidden');
            document.body.style.overflow = 'hidden';
        }
        
        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
            document.body.style.overflow = '';
        }
        
        // Close modal on backdrop click
        document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
            backdrop.addEventListener('click', function(e) {
                if (e.target === this) {
                    this.classList.add('hidden');
                    document.body.style.overflow = '';
                }
            });
        });
        
        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-backdrop').forEach(modal => {
                    modal.classList.add('hidden');
                });
                document.body.style.overflow = '';
            }
        });
    </script>
    
    <!-- Terms Modal -->
    <div id="termsModal" class="modal-backdrop hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] overflow-hidden flex flex-col">
            <div class="flex items-center justify-between p-4 border-b border-slate-100">
                <h2 class="text-xl font-bold text-slate-800">Syarat & Ketentuan</h2>
                <button onclick="closeModal('termsModal')" class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto prose prose-sm max-w-none">
                <p class="text-slate-500 text-sm mb-4">Terakhir diperbarui: <?= date('d F Y') ?></p>
                
                <p class="text-slate-600">Dengan menggunakan aplikasi MyTabungan, Anda menyetujui ketentuan berikut:</p>
                
                <h3 class="text-lg font-semibold text-slate-800 mt-4 mb-2">1. Penggunaan Layanan</h3>
                <p class="text-slate-600">MyTabungan adalah aplikasi pencatatan keuangan pribadi. Anda bertanggung jawab untuk menjaga kerahasiaan akun dan semua aktivitas yang terjadi melalui akun Anda.</p>
                
                <h3 class="text-lg font-semibold text-slate-800 mt-4 mb-2">2. Data Pengguna</h3>
                <p class="text-slate-600">Kami mengumpulkan data yang Anda masukkan untuk menyediakan layanan. Data keuangan Anda hanya dapat diakses oleh Anda.</p>
                
                <h3 class="text-lg font-semibold text-slate-800 mt-4 mb-2">3. Pembatasan</h3>
                <p class="text-slate-600">Anda dilarang menggunakan layanan untuk tujuan ilegal, mengganggu sistem, atau mengakses akun pengguna lain.</p>
                
                <h3 class="text-lg font-semibold text-slate-800 mt-4 mb-2">4. Batasan Tanggung Jawab</h3>
                <p class="text-slate-600">MyTabungan disediakan "sebagaimana adanya". Kami tidak bertanggung jawab atas kerugian finansial akibat keputusan berdasarkan data di aplikasi.</p>
                
                <h3 class="text-lg font-semibold text-slate-800 mt-4 mb-2">5. Perubahan Ketentuan</h3>
                <p class="text-slate-600">Kami dapat memperbarui ketentuan ini sewaktu-waktu. Penggunaan berkelanjutan berarti Anda menyetujui perubahan tersebut.</p>
            </div>
            <div class="p-4 border-t border-slate-100">
                <button onclick="closeModal('termsModal')" class="w-full py-2.5 bg-emerald-500 text-white font-semibold rounded-xl hover:bg-emerald-600 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
    
    <!-- Privacy Modal -->
    <div id="privacyModal" class="modal-backdrop hidden fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[80vh] overflow-hidden flex flex-col">
            <div class="flex items-center justify-between p-4 border-b border-slate-100">
                <h2 class="text-xl font-bold text-slate-800">Kebijakan Privasi</h2>
                <button onclick="closeModal('privacyModal')" class="w-8 h-8 rounded-lg hover:bg-slate-100 flex items-center justify-center text-slate-400 hover:text-slate-600">
                    <i class="bi bi-x-lg"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto prose prose-sm max-w-none">
                <p class="text-slate-500 text-sm mb-4">Terakhir diperbarui: <?= date('d F Y') ?></p>
                
                <p class="text-slate-600">MyTabungan berkomitmen melindungi privasi Anda.</p>
                
                <h3 class="text-lg font-semibold text-slate-800 mt-4 mb-2">1. Data yang Dikumpulkan</h3>
                <p class="text-slate-600">Kami mengumpulkan: nama, email, password (terenkripsi), dan data transaksi yang Anda catat.</p>
                
                <h3 class="text-lg font-semibold text-slate-800 mt-4 mb-2">2. Penggunaan Data</h3>
                <p class="text-slate-600">Data digunakan untuk menyediakan layanan, menampilkan statistik keuangan, dan mengamankan akun Anda.</p>
                
                <h3 class="text-lg font-semibold text-slate-800 mt-4 mb-2">3. Keamanan</h3>
                <p class="text-slate-600">Password di-hash dengan bcrypt. Hanya Anda yang dapat mengakses data keuangan Anda.</p>
                
                <h3 class="text-lg font-semibold text-slate-800 mt-4 mb-2">4. Pembagian Data</h3>
                <p class="text-slate-600"><strong>Kami TIDAK menjual data Anda.</strong> Data hanya dibagikan jika Anda menggunakan fitur "Bagikan" atau diwajibkan oleh hukum.</p>
                
                <h3 class="text-lg font-semibold text-slate-800 mt-4 mb-2">5. Hak Anda</h3>
                <p class="text-slate-600">Anda dapat mengakses, memperbarui, atau menghapus data Anda kapan saja melalui menu Profil.</p>
                
                <div class="mt-4 p-3 bg-emerald-50 rounded-lg border border-emerald-100">
                    <p class="text-emerald-700 text-sm flex items-start gap-2 mb-0">
                        <i class="bi bi-shield-check"></i>
                        <span>Data keuangan Anda adalah milik Anda. Kami berkomitmen menjaganya dengan standar keamanan tertinggi.</span>
                    </p>
                </div>
            </div>
            <div class="p-4 border-t border-slate-100">
                <button onclick="closeModal('privacyModal')" class="w-full py-2.5 bg-emerald-500 text-white font-semibold rounded-xl hover:bg-emerald-600 transition-colors">
                    Tutup
                </button>
            </div>
        </div>
    </div>
</body>
</html>
