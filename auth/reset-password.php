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

<div class="row justify-content-center vh-100 align-items-center">
    <div class="col-md-6 col-lg-4">
        <div class="card shadow-sm rounded-4 border-0">
            <div class="card-body p-4 p-lg-5">
                <div class="text-center mb-4">
                    <h1 class="h3 fw-bold">Reset Password</h1>
                    <p class="text-muted">Masukkan password baru Anda.</p>
                </div>
                <form action="reset-password.php" method="POST">
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Password Baru</label>
                        <input type="password" class="form-control form-control-modern" id="new_password" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control form-control-modern" id="confirm_password" name="confirm_password" required>
                    </div>
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-success btn-lg">Reset Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../layout/footer.php'; ?>