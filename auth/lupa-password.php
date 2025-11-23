<?php
require_once __DIR__ . '/../config/koneksi.php';

if (isset($_SESSION['id_pengguna'])) {
    header("Location: ../dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    if (empty($email)) {
        $_SESSION['pesan_error'] = "Email wajib diisi.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id_pengguna FROM pengguna WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user) {
                // Untuk aplikasi sederhana ini, kita langsung arahkan ke halaman reset.
                // Dalam aplikasi production, di sini seharusnya ada proses kirim email dengan token.
                $_SESSION['reset_user_id'] = $user['id_pengguna'];
                $_SESSION['pesan_sukses'] = "Akun ditemukan! Silakan reset password Anda.";
                header('Location: reset-password.php');
                exit;
            } else {
                $_SESSION['pesan_error'] = "Email tidak ditemukan di sistem kami.";
            }
        } catch (PDOException $e) {
            $_SESSION['pesan_error'] = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
    header('Location: lupa-password.php');
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
                                <i class="bi bi-shield-lock"></i>
                            </div>
                            <h1 class="h3 fw-bold gradient-text mb-2">Lupa Password?</h1>
                            <p class="text-muted mb-0">Masukkan email Anda untuk reset password</p>
                        </div>

                        <?php if (isset($_SESSION['pesan_error'])): ?>
                            <div class="alert alert-danger alert-modern animate-slide-in-right" role="alert">
                                <i class="bi bi-exclamation-circle-fill me-2"></i>
                                <?php echo $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST" class="auth-form">
                            <div class="mb-3">
                                <label for="email" class="form-label fw-500">Alamat Email</label>
                                <div class="input-group input-group-modern">
                                    <span class="input-group-text bg-transparent border-end-0">
                                        <i class="bi bi-envelope text-muted"></i>
                                    </span>
                                    <input type="email" 
                                           class="form-control border-start-0 ps-0" 
                                           id="email" 
                                           name="email" 
                                           placeholder="nama@email.com"
                                           required 
                                           autofocus>
                                </div>
                                <small class="text-muted">Kami akan membantu Anda mereset password</small>
                            </div>
                            
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-gradient btn-lg">
                                    <i class="bi bi-search me-2"></i>Cari Akun Saya
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