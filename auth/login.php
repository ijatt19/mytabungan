<?php
require_once __DIR__ . '/../config/koneksi.php'; // Session sudah dimulai di sini

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    try {
        $stmt = $pdo->prepare("SELECT id_pengguna, password, nama_lengkap FROM pengguna WHERE username = ?");
        $stmt->execute([$username]);
        $pengguna = $stmt->fetch();

        if ($pengguna && password_verify($password, $pengguna['password'])) {
            $_SESSION['id_pengguna'] = $pengguna['id_pengguna'];
            $_SESSION['nama_lengkap'] = $pengguna['nama_lengkap']; // Simpan nama lengkap ke session
            header('Location: ../dashboard.php');
            exit;
        } else {
            $_SESSION['pesan_error'] = 'Username atau password salah.';
        }
    } catch (PDOException $e) {
        $_SESSION['pesan_error'] = "Error: " . $e->getMessage();
    }
}
?>

<?php require_once __DIR__ . '/../layout/header.php'; ?>

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

                    <?php if (isset($_SESSION['pesan_error'])): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $_SESSION['pesan_error']; unset($_SESSION['pesan_error']); ?>
                        </div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['pesan_sukses'])): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $_SESSION['pesan_sukses']; unset($_SESSION['pesan_sukses']); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control form-control-modern" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control form-control-modern" id="password" name="password" required>
                        </div>
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">Masuk</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3 bg-light border-0">
                    <div class="small">Belum punya akun? <a href="register.php">Daftar di sini</a></div>
                </div>
            </div>
        </div>
    </div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>
