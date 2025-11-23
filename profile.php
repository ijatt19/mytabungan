<?php
require_once __DIR__ . '/layout/header.php';
require_once __DIR__ . '/layout/sidebar.php';
require_once __DIR__ . '/auth/cek_masuk.php';


$id_pengguna = $_SESSION['id_pengguna'];
$nama_lengkap = '';

try {
    $stmt = $pdo->prepare("SELECT nama_lengkap FROM pengguna WHERE id_pengguna = ?");
    $stmt->execute([$id_pengguna]);
    $user = $stmt->fetch();

    if ($user) {
        $nama_lengkap = $user['nama_lengkap'];
    }
} catch (PDOException $e) {
    $_SESSION['pesan_error'] = "Error mengambil data pengguna: " . $e->getMessage();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Cek tombol submit mana yang ditekan
    if (isset($_POST['update_profile'])) {
    $new_nama_lengkap = $_POST['nama_lengkap'];

    // Update nama lengkap
        if ($nama_lengkap !== $new_nama_lengkap) {
        try {
            $stmt = $pdo->prepare("UPDATE pengguna SET nama_lengkap = ? WHERE id_pengguna = ?");
            $stmt->execute([$new_nama_lengkap, $id_pengguna]);
            $_SESSION['nama_lengkap'] = $new_nama_lengkap; // Update session
            $_SESSION['pesan_sukses'] = 'Nama lengkap berhasil diperbarui.';
            header('Location: profile.php');
            exit;
        } catch (PDOException $e) {
            $_SESSION['pesan_error'] = "Gagal memperbarui nama lengkap: " . $e->getMessage();
        }
    }
    }

    // Update password
    if (isset($_POST['update_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
            $_SESSION['pesan_error'] = "Untuk mengubah password, semua kolom password harus diisi.";
        } elseif ($new_password !== $confirm_password) {
            $_SESSION['pesan_error'] = "Password baru dan konfirmasi password tidak cocok.";
        } else {
            try {
                $stmt = $pdo->prepare("SELECT password FROM pengguna WHERE id_pengguna = ?");
                $stmt->execute([$id_pengguna]);
                $user_password = $stmt->fetchColumn();

                if (password_verify($old_password, $user_password)) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE pengguna SET password = ? WHERE id_pengguna = ?");
                    $stmt->execute([$hashed_password, $id_pengguna]);
                    $_SESSION['pesan_sukses'] = 'Password berhasil diperbarui.';
                    header('Location: profile.php');
                    exit;
                } else {
                    $_SESSION['pesan_error'] = "Password lama salah.";
                }
            } catch (PDOException $e) {
                $_SESSION['pesan_error'] = "Gagal memperbarui password: " . $e->getMessage();
            }
        }
        header('Location: profile.php');
        exit;
    }
}
?>

<main class="px-4 py-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
        <h1 class="fs-4 fw-bold" style="color: #1e293b;">Profil Pengguna</h1>
    </div>

    <div class="row g-4">
        <!-- Card Update Profil -->
        <div class="col-12 col-lg-6">
            <div class="card card-interactive shadow-sm rounded-4 border-0 h-100">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4">Update Profil</h5>
                    <form action="profile.php" method="POST" data-confirm="true">
                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control form-control-modern" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($nama_lengkap); ?>" required>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" name="update_profile" class="btn btn-primary rounded-3 px-4 shadow-sm btn-interactive"><i class="bi bi-person-check me-2"></i>Update Profil</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Card Ubah Password -->
        <div class="col-12 col-lg-6">
            <div class="card card-interactive shadow-sm rounded-4 border-0 h-100">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-4">Ubah Password</h5>
                    <form action="profile.php" method="POST" data-confirm="true">
                        <div class="mb-3">
                            <label for="old_password" class="form-label">Password Lama</label>
                            <input type="password" class="form-control form-control-modern" id="old_password" name="old_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="new_password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control form-control-modern" id="new_password" name="new_password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control form-control-modern" id="confirm_password" name="confirm_password" required>
                        </div>
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" name="update_password" class="btn btn-warning rounded-3 px-4 shadow-sm btn-interactive"><i class="bi bi-key me-2"></i>Ubah Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once __DIR__ . '/layout/footer.php';
?>