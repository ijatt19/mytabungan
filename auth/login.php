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
            $_SESSION['email'] = $pengguna['email']; // Simpan email juga, mungkin berguna nanti
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

<div class="container">
    <div class="row justify-content-center align-items-center vh-100">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm rounded-4 border-0">
                <div class="card-body p-4 p-lg-5">
                    <div class="text-center mb-4">
                        <i class="bi bi-wallet2 fs-1 text-success"></i>
                        <h1 class="h3 fw-bold mt-2">MyTabungan</h1>
                        <p class="text-muted">Masuk untuk melanjutkan</p>
                    </div>

                    <?php if (isset($_SESSION['pesan_sukses'])): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $_SESSION['pesan_sukses']; unset($_SESSION['pesan_sukses']); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Alamat Email</label>
                            <input type="email" class="form-control form-control-modern" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control form-control-modern" id="password" name="password" required>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-success btn-lg">Masuk</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3 bg-light border-0">
                    <div class="small mb-2"><a href="lupa-password.php">Lupa Password?</a></div>
                    <div class="small">Belum punya akun? <a href="register.php">Daftar di sini</a></div>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
