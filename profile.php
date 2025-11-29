<?php
require_once __DIR__ . '/auth/cek_masuk.php';

$id_pengguna = $_SESSION['id_pengguna'];
$nama_lengkap = '';
$email = '';
$joined_at = '';
$total_transaksi = 0;
$total_wishlist = 0;

// --- Form Handling (Must be before any HTML output) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update Profile
    if (isset($_POST['update_profile'])) {
        $new_nama_lengkap = trim($_POST['nama_lengkap']);

        if (empty($new_nama_lengkap)) {
             $_SESSION['pesan_error'] = "Nama lengkap tidak boleh kosong.";
        } elseif ($nama_lengkap !== $new_nama_lengkap) {
            try {
                $stmt = $pdo->prepare("UPDATE pengguna SET nama_lengkap = ? WHERE id_pengguna = ?");
                $stmt->execute([$new_nama_lengkap, $id_pengguna]);
                $_SESSION['nama_lengkap'] = $new_nama_lengkap;
                $_SESSION['pesan_sukses'] = 'Profil berhasil diperbarui.';
                header('Location: profile.php');
                exit;
            } catch (PDOException $e) {
                $_SESSION['pesan_error'] = "Gagal memperbarui profil: " . $e->getMessage();
            }
        } else {
             // No changes
             header('Location: profile.php');
             exit;
        }
    }

    // Update Password
    if (isset($_POST['update_password'])) {
        $old_password = $_POST['old_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if (empty($old_password) || empty($new_password) || empty($confirm_password)) {
            $_SESSION['pesan_error'] = "Semua kolom password wajib diisi.";
        } elseif ($new_password !== $confirm_password) {
            $_SESSION['pesan_error'] = "Password baru dan konfirmasi tidak cocok.";
        } elseif ($old_password === $new_password) {
            $_SESSION['pesan_error'] = "Password baru tidak boleh sama dengan password lama.";
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

try {
    // 1. User Details (Safe Query)
    $stmt = $pdo->prepare("SELECT nama_lengkap FROM pengguna WHERE id_pengguna = ?");
    $stmt->execute([$id_pengguna]);
    $user = $stmt->fetch();

    if ($user) {
        $nama_lengkap = $user['nama_lengkap'];
    }
} catch (PDOException $e) {
    // Ignore user fetch error
}

try {
    // 2. Stats: Total Transaksi
    $stmt_trans = $pdo->prepare("SELECT COUNT(*) FROM transaksi WHERE id_pengguna = ?");
    $stmt_trans->execute([$id_pengguna]);
    $total_transaksi = $stmt_trans->fetchColumn();
} catch (PDOException $e) { $total_transaksi = 0; }

try {
    // 3. Stats: Total Wishlist
    $stmt_wish = $pdo->prepare("SELECT COUNT(*) FROM wishlist WHERE id_pengguna = ?");
    $stmt_wish->execute([$id_pengguna]);
    $total_wishlist = $stmt_wish->fetchColumn();
} catch (PDOException $e) { $total_wishlist = 0; }

require_once __DIR__ . '/layout/header.php';
require_once __DIR__ . '/layout/sidebar.php';
?>

<main class="min-h-screen md:ml-64 px-4 sm:px-6 pt-20 pb-10 bg-gray-50">
    
    <!-- Profile Header Card -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-green-600 to-emerald-800 text-white shadow-lg mb-8">
        <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white opacity-10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-yellow-400 opacity-10 rounded-full blur-2xl"></div>
        
        <div class="relative z-10 p-8 flex flex-col md:flex-row items-center gap-6">
            <!-- Avatar -->
            <div class="w-24 h-24 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center border-4 border-white/30 shadow-xl">
                <i class="bi bi-person-fill text-5xl text-white"></i>
            </div>
            
            <!-- Info -->
            <div class="text-center md:text-left flex-1">
                <h1 class="text-3xl font-bold mb-1"><?php echo htmlspecialchars($nama_lengkap); ?></h1>
                <p class="text-green-100 opacity-90 mb-4 flex items-center justify-center md:justify-start gap-2">
                    <i class="bi bi-shield-check"></i> Akun Terverifikasi
                </p>
                <!-- Removed Join Date as it is unavailable -->
            </div>

            <!-- Stats -->
            <div class="flex gap-4 md:gap-8 border-t md:border-t-0 md:border-l border-white/20 pt-4 md:pt-0 md:pl-8 mt-4 md:mt-0 w-full md:w-auto justify-center md:justify-start">
                <div class="text-center">
                    <span class="block text-2xl font-bold"><?php echo $total_transaksi; ?></span>
                    <span class="text-xs text-green-100 uppercase tracking-wider">Transaksi</span>
                </div>
                <div class="text-center">
                    <span class="block text-2xl font-bold"><?php echo $total_wishlist; ?></span>
                    <span class="text-xs text-green-100 uppercase tracking-wider">Impian</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Edit Profile -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Edit Profile Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h5 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="bi bi-person-gear text-green-600"></i> Edit Informasi Pribadi
                    </h5>
                </div>
                <div class="p-6">
                    <form action="profile.php" method="POST">
                        <div class="mb-6">
                            <label for="nama_lengkap" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap</label>
                            <div class="relative">
                                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400">
                                    <i class="bi bi-person"></i>
                                </span>
                                <input type="text" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($nama_lengkap); ?>" required>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">Nama ini akan ditampilkan di dashboard dan header.</p>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" name="update_profile" class="inline-flex items-center gap-2 px-6 py-2.5 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                                <i class="bi bi-check-lg"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Security Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 bg-gray-50/50">
                    <h5 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <i class="bi bi-shield-lock text-yellow-600"></i> Keamanan Akun
                    </h5>
                </div>
                <div class="p-6">
                    <form action="profile.php" method="POST">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                            <div class="md:col-span-2">
                                <label for="old_password" class="block text-sm font-medium text-gray-700 mb-2">Password Saat Ini</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="bi bi-key"></i></span>
                                    <input type="password" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all" id="old_password" name="old_password" required>
                                </div>
                            </div>
                            <div>
                                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">Password Baru</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="bi bi-lock"></i></span>
                                    <input type="password" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all" id="new_password" name="new_password" required>
                                </div>
                            </div>
                            <div>
                                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Konfirmasi Password</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"><i class="bi bi-lock-fill"></i></span>
                                    <input type="password" class="w-full pl-10 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:border-transparent transition-all" id="confirm_password" name="confirm_password" required>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" name="update_password" class="inline-flex items-center gap-2 px-6 py-2.5 bg-yellow-500 text-white font-medium rounded-lg hover:bg-yellow-600 hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                                <i class="bi bi-arrow-repeat"></i> Update Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column: Account Actions -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6">
                    <h5 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Aksi Akun</h5>
                    <a href="auth/logout.php" class="w-full flex items-center justify-between px-4 py-3 bg-red-50 text-red-700 rounded-lg hover:bg-red-100 transition-colors group" onclick="return confirm('Yakin ingin keluar?')">
                        <span class="font-medium">Keluar dari Aplikasi</span>
                        <i class="bi bi-box-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </a>
                </div>
            </div>

            <!-- Info Card -->
            <div class="bg-blue-50 rounded-xl border border-blue-100 p-6">
                <div class="flex items-start gap-3">
                    <i class="bi bi-info-circle-fill text-blue-600 text-xl mt-0.5"></i>
                    <div>
                        <h6 class="font-bold text-blue-900 mb-1">Butuh Bantuan?</h6>
                        <p class="text-sm text-blue-800 opacity-80">
                            Jika Anda mengalami masalah dengan akun Anda, silakan hubungi administrator sistem.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
require_once __DIR__ . '/layout/footer.php';
?>