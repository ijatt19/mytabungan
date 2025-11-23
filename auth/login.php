<?php
require_once __DIR__ . '/../config/koneksi.php'; // Session sudah dimulai di sini

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
                                <i class="bi bi-wallet2"></i>
                            </div>
                            <h1 class="h3 fw-bold gradient-text mb-2">MyTabungan</h1>
                            <p class="text-muted mb-0">Masuk untuk melanjutkan</p>
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
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label fw-500">Password</label>
                                <div class="input-group input-group-modern">
                                    <span class="input-group-text bg-transparent border-end-0">
                                        <i class="bi bi-lock text-muted"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control border-start-0 ps-0" 
                                           id="password" 
                                           name="password" 
                                           placeholder="••••••••"
                                           required>
                                </div>
                            </div>
                            
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-gradient btn-lg">
                                    <i class="bi bi-box-arrow-in-right me-2"></i>Masuk
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="card-footer text-center py-3 bg-light border-0">
                        <div class="small mb-2">
                            <a href="lupa-password.php" class="text-decoration-none">
                                <i class="bi bi-question-circle me-1"></i>Lupa Password?
                            </a>
                        </div>
                        <div class="small">
                            Belum punya akun? 
                            <a href="register.php" class="fw-600 text-decoration-none gradient-text">Daftar di sini</a>
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
