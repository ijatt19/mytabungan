<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../layout/header_auth.php';

// Jika sudah login, lempar ke dashboard
if (isset($_SESSION['id_pengguna'])) {
    header("Location: ../dashboard.php");
    exit;
}

// Logika PHP untuk Pendaftaran
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $nama_lengkap = trim($_POST['nama_lengkap']);

    if (empty($email) || empty($password) || empty($password_confirm) || empty($nama_lengkap)) {
        $_SESSION['pesan_error'] = "Semua kolom wajib diisi.";
        header('Location: register.php');
        exit;
    } elseif ($password !== $password_confirm) {
        $_SESSION['pesan_error'] = "Password dan konfirmasi password tidak cocok.";
        header('Location: register.php');
        exit;
    } elseif (strlen($password) < 6) {
        $_SESSION['pesan_error'] = "Password minimal 6 karakter.";
        header('Location: register.php');
        exit;
    } else {
        try {
            $stmt_cek = $pdo->prepare("SELECT id_pengguna FROM pengguna WHERE email = ?");
            $stmt_cek->execute([$email]);
            
            if ($stmt_cek->fetch()) {
                $_SESSION['pesan_error'] = "Email '$email' sudah terdaftar.";
                header('Location: register.php');
                exit;
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt_insert = $pdo->prepare("INSERT INTO pengguna (email, password, nama_lengkap) VALUES (?, ?, ?)");
                $stmt_insert->execute([$email, $hashed_password, $nama_lengkap]);
                
                $_SESSION['pesan_sukses'] = 'Pendaftaran berhasil! Silakan login.';
                header('Location: login.php');
                exit;
            }
        } catch (PDOException $e) {
            $_SESSION['pesan_error'] = "Terjadi kesalahan: " . $e->getMessage();
            header('Location: register.php');
            exit;
        }
    }
}
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
                                <i class="bi bi-person-plus"></i>
                            </div>
                            <h1 class="h3 fw-bold gradient-text mb-2">Buat Akun Baru</h1>
                            <p class="text-muted mb-0">Daftar untuk memulai perjalanan finansialmu</p>
                        </div>

                        <?php if (isset($_SESSION['pesan_error'])): ?>
                            <div class="alert alert-danger alert-modern animate-slide-in-right" role="alert">
                                <i class="bi bi-exclamation-circle-fill me-2"></i>
                                <?php echo $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
                            </div>
                        <?php endif; ?>

                        <form  method="POST" class="auth-form">
                            <div class="mb-3">
                                <label for="nama_lengkap" class="form-label fw-500">Nama Lengkap</label>
                                <div class="input-group input-group-modern">
                                    <span class="input-group-text bg-transparent border-end-0">
                                        <i class="bi bi-person text-muted"></i>
                                    </span>
                                    <input type="text" 
                                           class="form-control border-start-0 ps-0" 
                                           id="nama_lengkap" 
                                           name="nama_lengkap" 
                                           placeholder="John Doe"
                                           required 
                                           autofocus>
                                </div>
                            </div>
                            
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
                                           required>
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
                                           placeholder="Minimal 6 karakter"
                                           required>
                                </div>
                                <small class="text-muted">Minimal 6 karakter</small>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password_confirm" class="form-label fw-500">Konfirmasi Password</label>
                                <div class="input-group input-group-modern">
                                    <span class="input-group-text bg-transparent border-end-0">
                                        <i class="bi bi-shield-check text-muted"></i>
                                    </span>
                                    <input type="password" 
                                           class="form-control border-start-0 ps-0" 
                                           id="password_confirm" 
                                           name="password_confirm" 
                                           placeholder="Ulangi password"
                                           required>
                                </div>
                            </div>
                            
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-gradient btn-lg">
                                    <i class="bi bi-rocket-takeoff me-2"></i>Daftar Sekarang
                                </button>
                            </div>
                        </form>
                    </div>
                    
                    <div class="card-footer text-center py-3 bg-light border-0">
                        <div class="small">
                            Sudah punya akun? 
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
