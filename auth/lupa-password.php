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
                header('Location: reset-password.php');
                exit;
            } else {
                $_SESSION['pesan_error'] = "Email tidak ditemukan.";
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

<div class="row justify-content-center vh-100 align-items-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm rounded-4 border-0">
            <div class="card-body p-4 p-lg-5">
                <div class="text-center mb-4">
                    <h1 class="h3 fw-bold">Lupa Password</h1>
                    <p class="text-muted">Masukkan alamat email Anda untuk melanjutkan.</p>
                </div>
                <form action="lupa-password.php" method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">Alamat Email</label>
                        <input type="email" class="form-control form-control-modern" id="email" name="email" required>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-success btn-lg">Cari Akun</button>
                    </div>
                </form>
            </div>
            <div class="card-footer text-center py-3 bg-light border-0">
                <div class="small"><a href="login.php">Kembali ke Login</a></div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>