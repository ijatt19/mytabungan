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

<div class="auth-page">
    <!-- Background Decorations -->
    <div class="auth-decoration auth-decoration-1"></div>
    <div class="auth-decoration auth-decoration-2"></div>
    
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100 py-5">
            <div class="col-md-6 col-lg-5 col-xl-4">
                <div class="card auth-card border-0 shadow-xl animate-fade-in-up">
                    <div class="card-body p-4 p-lg-5">
                        <!-- Logo & Title -->
                        <div class="text-center mb-4">
                            <div class="auth-logo mb-3">
                                <i class="bi bi-key-fill"></i>
                            </div>
                            <h1 class="h3 fw-bold gradient-text mb-2">Reset Password</h1>
                            <p class="text-muted mb-0">Buat password baru untuk akun Anda</p>
                        </div>

                        <?php if (isset($_SESSION['pesan_sukses'])): ?>
                            <div class="alert alert-success alert-modern animate-slide-in-right" role="alert">
                                <i class="bi bi-check-circle-fill me-2"></i>
                                <?php echo $_SESSION['pesan_sukses']; unset($_SESSION['pesan_sukses']); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (isset($_SESSION['pesan_error'])): ?>
                            <div class="alert alert-danger alert-modern animate-slide-in-right" role="alert">
                                <i class="bi bi-exclamation-circle-fill me-2"></i>
                                <?php echo $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="auth-form">
                            <div class="mb-3">
                                <label for="new_password" class="form-label fw-500">Password Baru</label>
                                <div class="input-group input-group-modern">
                                    <span class="input-group-text bg-transparent border-end-0">
                                        <i class="bi bi-lock text-muted"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control border-start-0 ps-0" 
                                           id="new_password" 
                                           name="new_password" 
                                           placeholder="Minimal 6 karakter"
                                           required 
                                           autofocus>
                                </div>
                                <small class="text-muted">Gunakan kombinasi huruf, angka, dan simbol</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label fw-500">Konfirmasi Password Baru</label>
                                <div class="input-group input-group-modern">
                                    <span class="input-group-text bg-transparent border-end-0">
                                        <i class="bi bi-shield-check text-muted"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control border-start-0 ps-0" 
                                           id="confirm_password" 
                                           name="confirm_password" 
                                           placeholder="Ulangi password baru"
                                           required>
                                </div>
                            </div>
                            
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-gradient btn-lg">
                                    <i class="bi bi-check-circle me-2"></i>Reset Password
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="card-footer text-center py-3 bg-light border-0">
                        <div class="small">
                            Sudah ingat password? 
                            <a href="login.php" class="fw-600 text-decoration-none gradient-text">Masuk di sini</a>
                        </div>
                    </div>
                </div>
                
                <!-- Back to Home Link -->
                <div class="text-center mt-4">
                    <a href="../index.php" class="text-muted text-decoration-none small">
                        <i class="bi bi-arrow-left me-1"></i>Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>