<?php
require_once __DIR__ . '/../config/koneksi.php'; // Session sud ah dimulai di sini

// Jika sudah login, lempar ke dashboard.php
if (isset($_SESSION['id_pengguna'])) {
    header("Location: ../dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT id_pengguna, password, nama_lengkap, email FROM pengguna WHERE email = ?");
        $stmt->execute([$email]);
        $pengguna = $stmt->fetch();

        if ($pengguna && password_verify($password, $pengguna['password'])) {
            $_SESSION['id_pengguna'] = $pengguna['id_pengguna'];
            $_SESSION['nama_lengkap'] = $pengguna['nama_lengkap']; // Simpan nama lengkap ke session
            $_SESSION['email'] = $pengguna['email']; // Simpan email juga
            header('Location: ../dashboard.php');
            exit;
        } else {
            $_SESSION['pesan_error'] = 'Email atau password salah. Coba lagi.';
        }
    } catch (PDOException $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
    header('Location: login.php');
    exit;
}
?>

<?php require_once __DIR__ . '/../layout/header_auth.php'; ?>

<div class="auth-page min-h-screen flex items-center justify-center relative overflow-hidden py-12 px-4">
    <!-- Background Decorations -->
    <div class="auth-decoration auth-decoration-1 absolute w-96 h-96 bg-green-100/30 rounded-full blur-3xl -top-20 -right-20"></div>
    <div class="auth-decoration auth-decoration-2 absolute w-80 h-80 bg-emerald-100/30 rounded-full blur-3xl -bottom-20 -left-20"></div>
    
    <div class="w-full max-w-md relative z-10">
        <div class="auth-card bg-white rounded-2xl shadow-2xl border-0 animate-fade-in-up overflow-hidden">
            <div class="p-8 lg:p-12">
                <!-- Logo & Title -->
                <div class="text-center mb-8">
                    <div class="auth-logo inline-flex items-center justify-center w-16 h-16 mb-4 bg-gradient-to-r from-green-600 via-emerald-500 to-teal-500 rounded-2xl text-white text-3xl">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <h1 class="text-3xl font-bold gradient-text mb-2">MyTabungan</h1>
                    <p class="text-gray-600">Masuk untuk melanjutkan</p>
                </div>

                <?php if (isset($_SESSION['pesan_sukses'])): ?>
                    <div class="alert-modern bg-green-50 border border-green-200 text-green-800 rounded-lg p-4 mb-6 flex items-center gap-3 animate-slide-in-right" role="alert">
                        <i class="bi bi-check-circle-fill text-green-600"></i>
                        <span><?php echo $_SESSION['pesan_sukses']; unset($_SESSION['pesan_sukses']); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['pesan_error'])): ?>
                    <div class="alert-modern bg-red-50 border border-red-200 text-red-800 rounded-lg p-4 mb-6 flex items-center gap-3 animate-slide-in-right" role="alert">
                        <i class="bi bi-exclamation-circle-fill text-red-600"></i>
                        <span><?php echo $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?></span>
                    </div>
                <?php endif; ?>

                <form method="POST" class="auth-form space-y-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Alamat Email</label>
                        <div class="input-group-modern flex items-center border border-gray-200 rounded-lg overflow-hidden focus-within:border-green-600 focus-within:ring-2 focus-within:ring-green-100 transition-all">
                            <span class="flex items-center justify-center px-4 bg-transparent border-r-0">
                                <i class="bi bi-envelope text-gray-400"></i>
                            </span>
                            <input type="email" 
                                   class="flex-1 px-4 py-3 border-0 focus:outline-none focus:ring-0" 
                                   id="email" 
                                   name="email" 
                                   placeholder="nama@email.com"
                                   required 
                                   autofocus>
                        </div>
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                        <div class="input-group-modern flex items-center border border-gray-200 rounded-lg overflow-hidden focus-within:border-green-600 focus-within:ring-2 focus-within:ring-green-100 transition-all">
                            <span class="flex items-center justify-center px-4 bg-transparent border-r-0">
                                <i class="bi bi-lock text-gray-400"></i>
                            </span>
                            <input type="password" 
                                   class="flex-1 px-4 py-3 border-0 focus:outline-none focus:ring-0" 
                                   id="password" 
                                   name="password" 
                                   placeholder="••••••••"
                                   required>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="btn-gradient w-full py-3 px-6 bg-gradient-to-r from-green-600 via-emerald-500 to-teal-500 text-white font-semibold rounded-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5 relative overflow-hidden group">
                            <span class="relative z-10 flex items-center justify-center gap-2">
                                <i class="bi bi-box-arrow-in-right"></i> Masuk
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-500"></div>
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="bg-gray-50 border-t border-gray-100 px-8 py-4 text-center space-y-2">
                <div class="text-sm">
                    <a href="lupa-password.php" class="text-gray-600 hover:text-green-600 transition-colors no-underline inline-flex items-center gap-1">
                        <i class="bi bi-question-circle"></i> Lupa Password?
                    </a>
                </div>
                <div class="text-sm">
                    Belum punya akun? 
                    <a href="register.php" class="font-semibold gradient-text no-underline hover:underline">Daftar di sini</a>
                </div>
            </div>
        </div>
        
        <!-- Back to Home Link -->
        <div class="text-center mt-6">
            <a href="../index.php" class="text-gray-600 hover:text-gray-900 no-underline text-sm inline-flex items-center gap-1 transition-colors">
                <i class="bi bi-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
