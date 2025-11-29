<?php
require_once __DIR__ . '/../config/koneksi.php';

// Pastikan ada user ID di session untuk di-reset
if (!isset($_SESSION['reset_user_id'])) {
    $_SESSION['pesan_error'] = "Sesi reset password tidak valid. Silakan ulangi dari awal.";
    header('Location: lupa-password.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_pengguna = $_SESSION['reset_user_id'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($new_password) || empty($confirm_password)) {
        $_SESSION['pesan_error'] = "Semua kolom wajib diisi.";
    } elseif (strlen($new_password) < 6) {
        $_SESSION['pesan_error'] = "Password minimal 6 karakter.";
    } elseif ($new_password !== $confirm_password) {
        $_SESSION['pesan_error'] = "Password baru dan konfirmasi tidak cocok.";
    } else {
        try {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE pengguna SET password = ? WHERE id_pengguna = ?");
            $stmt->execute([$hashed_password, $id_pengguna]);

            // Hapus session reset dan beri pesan sukses
            unset($_SESSION['reset_user_id']);
            $_SESSION['pesan_sukses'] = 'Password berhasil direset! Silakan login dengan password baru Anda.';
            header('Location: login.php');
            exit;
        } catch (PDOException $e) {
            $_SESSION['pesan_error'] = "Gagal mereset password: " . $e->getMessage();
        }
    }
    header('Location: reset-password.php');
    exit;
}

require_once __DIR__ . '/../layout/header_auth.php';
?>

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
                        <i class="bi bi-key-fill"></i>
                    </div>
                    <h1 class="text-3xl font-bold gradient-text mb-2">Reset Password</h1>
                    <p class="text-gray-600">Buat password baru untuk akun Anda</p>
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
                        <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                        <div class="input-group-modern flex items-center border border-gray-200 rounded-lg overflow-hidden focus-within:border-green-600 focus-within:ring-2 focus-within:ring-green-100 transition-all">
                            <span class="flex items-center justify-center px-4 bg-transparent border-r-0">
                                <i class="bi bi-lock text-gray-400"></i>
                            </span>
                            <input type="password" 
                                   class="flex-1 px-4 py-3 border-0 focus:outline-none focus:ring-0" 
                                   id="new_password" 
                                   name="new_password" 
                                   placeholder="Minimal 6 karakter"
                                   required 
                                   autofocus>
                        </div>
                        <p class="mt-2 text-xs text-gray-500">Gunakan kombinasi huruf, angka, dan simbol.</p>
                    </div>
                    
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password Baru</label>
                        <div class="input-group-modern flex items-center border border-gray-200 rounded-lg overflow-hidden focus-within:border-green-600 focus-within:ring-2 focus-within:ring-green-100 transition-all">
                            <span class="flex items-center justify-center px-4 bg-transparent border-r-0">
                                <i class="bi bi-shield-check text-gray-400"></i>
                            </span>
                            <input type="password" 
                                   class="flex-1 px-4 py-3 border-0 focus:outline-none focus:ring-0" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   placeholder="Ulangi password baru"
                                   required>
                        </div>
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" class="btn-gradient w-full py-3 px-6 bg-gradient-to-r from-green-600 via-emerald-500 to-teal-500 text-white font-semibold rounded-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-0.5 relative overflow-hidden group">
                            <span class="relative z-10 flex items-center justify-center gap-2">
                                <i class="bi bi-check-circle"></i> Reset Password
                            </span>
                            <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent -translate-x-full group-hover:translate-x-full transition-transform duration-500"></div>
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="bg-gray-50 border-t border-gray-100 px-8 py-4 text-center space-y-2">
                <div class="text-sm">
                    Sudah ingat password? 
                    <a href="login.php" class="font-semibold gradient-text no-underline hover:underline">Masuk di sini</a>
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