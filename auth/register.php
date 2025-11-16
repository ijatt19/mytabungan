<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../layout/header_auth.php';

// Jika sudah login, lempar ke index.php
if (isset($_SESSION['id_pengguna'])) {
    header("Location: /tabung/index.php");
    exit;
}

// Logika PHP untuk Pendaftaran

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $nama_lengkap = trim($_POST['nama_lengkap']);

    if (empty($email) || empty($password) || empty($nama_lengkap)) {
        $_SESSION['pesan_error'] = "Semua kolom wajib diisi.";
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


    <div class="row justify-content-center vh-100 align-items-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h1 class="h3 fw-bold text-primary">Buat Akun Baru</h1>
                        <p class="text-muted">Isi form di bawah untuk mendaftar.</p>
                    </div>                    

                    <form action="register.php" method="POST">
                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control form-control-modern" id="nama_lengkap" name="nama_lengkap" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Alamat Email</label>
                            <input type="email" class="form-control form-control-modern" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control form-control-modern" id="password" name="password" required>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">Daftar</button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-3">
                    <div class="small">Sudah punya akun? <a href="login.php">Masuk di sini</a></div>
                </div>
            </div>
        </div>
    </div>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
