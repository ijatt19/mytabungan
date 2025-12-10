<?php
/**
 * User Profile Page
 * MyTabungan - Personal Finance Management
 */

require_once __DIR__ . '/auth/auth.php';
require_once __DIR__ . '/config/database.php';

// Require login
requireLogin();

$pageTitle = 'Profil';
$userId = getCurrentUserId();
$pdo = getConnection();

// Get user data
$user = getUserById($userId);

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = $_POST['form_type'] ?? '';
    
    if ($formType === 'profile') {
        $nama = $_POST['nama'] ?? '';
        $email = $_POST['email'] ?? '';
        
        $result = updateUserProfile($userId, $nama, $email);
        setFlashMessage($result['success'] ? 'success' : 'error', $result['message']);
        redirect('profil.php');
        
    } elseif ($formType === 'password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if ($newPassword !== $confirmPassword) {
            setFlashMessage('error', 'Konfirmasi password tidak cocok.');
        } else {
            $result = updateUserPassword($userId, $currentPassword, $newPassword);
            setFlashMessage($result['success'] ? 'success' : 'error', $result['message']);
        }
        redirect('profil.php');
        
    } elseif ($formType === 'delete_account') {
        $password = $_POST['delete_password'] ?? '';
        
        $result = deleteUserAccount($userId, $password);
        
        if ($result['success']) {
            header('Location: index.php');
            exit;
        } else {
            setFlashMessage('error', $result['message']);
            redirect('profil.php');
        }
    }
}

// Get user stats
$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM transaksi WHERE id_pengguna = ?");
$stmt->execute([$userId]);
$totalTransactions = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM wishlist WHERE id_pengguna = ? AND status = 'Tercapai'");
$stmt->execute([$userId]);
$achievedGoals = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM kategori WHERE id_pengguna = ?");
$stmt->execute([$userId]);
$totalCategories = $stmt->fetch()['count'];

// Get financial summary
$stmt = $pdo->prepare("SELECT 
    COALESCE(SUM(CASE WHEN k.tipe = 'Pemasukan' THEN t.jumlah ELSE 0 END), 0) as total_income,
    COALESCE(SUM(CASE WHEN k.tipe = 'Pengeluaran' THEN t.jumlah ELSE 0 END), 0) as total_expense
    FROM transaksi t 
    JOIN kategori k ON t.id_kategori = k.id_kategori 
    WHERE t.id_pengguna = ?");
$stmt->execute([$userId]);
$financial = $stmt->fetch();
$totalBalance = $financial['total_income'] - $financial['total_expense'];

// Calculate days since joined
$joinDate = new DateTime($user['created_at']);
$now = new DateTime();
$daysSinceJoined = $joinDate->diff($now)->days;

// Generate motivational quote
function getMotivationalQuote($balance, $transactions, $goals, $days) {
    if ($balance >= 10000000) {
        return ["Wow! Tabungan sudah " . formatRupiah($balance) . "! Luar biasa! 🚀", "text-emerald-600"];
    } elseif ($balance >= 5000000) {
        return ["Mantap! Sudah menabung " . formatRupiah($balance) . "! Keep going! 💪", "text-emerald-600"];
    } elseif ($balance >= 1000000) {
        return ["Keren! Tabungan sudah " . formatRupiah($balance) . "! Terus semangat! 🔥", "text-teal-600"];
    } elseif ($balance > 0) {
        return ["Sudah mulai menabung " . formatRupiah($balance) . "! Good start! ✨", "text-sky-600"];
    } elseif ($transactions > 0) {
        return ["Sudah mencatat " . $transactions . " transaksi! Terus pantau keuanganmu! 📊", "text-sky-600"];
    } elseif ($days > 7) {
        return ["Sudah " . $days . " hari bergabung! Yuk mulai catat transaksi! 📝", "text-amber-600"];
    } else {
        return ["Selamat datang! Yuk mulai kelola keuanganmu! 🎉", "text-purple-600"];
    }
}
$quote = getMotivationalQuote($totalBalance, $totalTransactions, $achievedGoals, $daysSinceJoined);

// Include layout
include __DIR__ . '/layout/header.php';
include __DIR__ . '/layout/sidebar.php';
?>

<!-- Profile Page Content -->
<div class="max-w-4xl mx-auto space-y-6">
    
    <!-- Profile Hero Card -->
    <div class="card overflow-hidden animate-fade-in">
        <!-- Gradient Banner with Pattern -->
        <div class="h-32 sm:h-40 bg-gradient-to-br from-emerald-500 via-teal-500 to-cyan-500 relative">
            <!-- Decorative circles -->
            <div class="absolute top-4 right-4 w-24 h-24 bg-white/10 rounded-full blur-xl"></div>
            <div class="absolute bottom-0 left-1/4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
            <div class="absolute top-1/2 right-1/3 w-16 h-16 bg-white/5 rounded-full"></div>
        </div>
        
        <!-- Profile Info Section -->
        <div class="px-6 pb-6 -mt-16 sm:-mt-20 relative">
            <div class="flex flex-col sm:flex-row sm:items-end gap-4">
                <!-- Avatar -->
                <div class="w-28 h-28 sm:w-32 sm:h-32 rounded-2xl bg-white p-1.5 shadow-xl ring-4 ring-white">
                    <div class="w-full h-full rounded-xl bg-gradient-to-br from-emerald-400 via-teal-500 to-cyan-500 flex items-center justify-center text-white text-4xl sm:text-5xl font-bold shadow-inner">
                        <?= strtoupper(substr($user['nama'], 0, 1)) ?>
                    </div>
                </div>
                
                <!-- Name & Info -->
                <div class="flex-1 pb-2">
                    <h1 class="text-2xl sm:text-3xl font-bold text-slate-800"><?= htmlspecialchars($user['nama']) ?></h1>
                    
                    <!-- Motivational Quote -->
                    <p class="mt-1 text-sm font-medium <?= $quote[1] ?> bg-white/80 backdrop-blur-sm px-3 py-1.5 rounded-full inline-block shadow-sm">
                        <?= $quote[0] ?>
                    </p>
                    
                    <div class="flex flex-wrap items-center gap-3 mt-3 text-sm text-slate-500">
                        <span class="flex items-center gap-1">
                            <i class="bi bi-envelope"></i>
                            <?= htmlspecialchars($user['email']) ?>
                        </span>
                        <span class="hidden sm:inline text-slate-300">•</span>
                        <span class="flex items-center gap-1">
                            <i class="bi bi-calendar3"></i>
                            Bergabung <?= formatTanggal($user['created_at'], 'short') ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Stats Grid -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Transactions -->
        <div class="card p-4 group hover:border-emerald-200 transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform">
                    <i class="bi bi-receipt text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800"><?= number_format($totalTransactions) ?></p>
                    <p class="text-xs text-slate-500">Transaksi</p>
                </div>
            </div>
        </div>
        
        <!-- Categories -->
        <div class="card p-4 group hover:border-sky-200 transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-sky-400 to-sky-600 flex items-center justify-center text-white shadow-lg shadow-sky-200 group-hover:scale-110 transition-transform">
                    <i class="bi bi-tags text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800"><?= number_format($totalCategories) ?></p>
                    <p class="text-xs text-slate-500">Kategori</p>
                </div>
            </div>
        </div>
        
        <!-- Goals Achieved -->
        <div class="card p-4 group hover:border-amber-200 transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white shadow-lg shadow-amber-200 group-hover:scale-110 transition-transform">
                    <i class="bi bi-trophy text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800"><?= number_format($achievedGoals) ?></p>
                    <p class="text-xs text-slate-500">Goal Tercapai</p>
                </div>
            </div>
        </div>
        
        <!-- Days Active -->
        <div class="card p-4 group hover:border-purple-200 transition-all">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-purple-400 to-purple-600 flex items-center justify-center text-white shadow-lg shadow-purple-200 group-hover:scale-110 transition-transform">
                    <i class="bi bi-clock-history text-xl"></i>
                </div>
                <div>
                    <p class="text-2xl font-bold text-slate-800"><?= number_format($daysSinceJoined) ?></p>
                    <p class="text-xs text-slate-500">Hari Aktif</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Financial Summary -->
    <div class="card p-5">
        <h3 class="font-semibold text-slate-800 mb-4 flex items-center gap-2">
            <i class="bi bi-graph-up-arrow text-emerald-500"></i>
            Ringkasan Keuangan
        </h3>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="p-4 rounded-xl bg-gradient-to-br from-emerald-50 to-teal-50 border border-emerald-100">
                <p class="text-sm text-emerald-600 mb-1">Total Pemasukan</p>
                <p class="text-xl font-bold text-emerald-700"><?= formatRupiah($financial['total_income']) ?></p>
            </div>
            <div class="p-4 rounded-xl bg-gradient-to-br from-red-50 to-rose-50 border border-red-100">
                <p class="text-sm text-red-600 mb-1">Total Pengeluaran</p>
                <p class="text-xl font-bold text-red-700"><?= formatRupiah($financial['total_expense']) ?></p>
            </div>
            <div class="p-4 rounded-xl bg-gradient-to-br from-sky-50 to-blue-50 border border-sky-100">
                <p class="text-sm text-sky-600 mb-1">Saldo Bersih</p>
                <p class="text-xl font-bold <?= $totalBalance >= 0 ? 'text-sky-700' : 'text-red-700' ?>"><?= formatRupiah($totalBalance) ?></p>
            </div>
        </div>
    </div>
    
    <!-- Settings Card -->
    <div class="card overflow-hidden">
        <!-- Tab Navigation -->
        <div class="px-6 pt-5 flex gap-1 border-b border-slate-100 bg-slate-50/50">
            <button onclick="showTab('profile')" id="tabProfile" class="px-4 py-3 text-sm font-medium rounded-t-xl border-b-2 border-emerald-500 text-emerald-600 bg-white -mb-px">
                <i class="bi bi-person-gear mr-2"></i>Edit Profil
            </button>
            <button onclick="showTab('password')" id="tabPassword" class="px-4 py-3 text-sm font-medium rounded-t-xl border-b-2 border-transparent text-slate-400 hover:text-slate-600 hover:bg-white/50 transition-all -mb-px">
                <i class="bi bi-shield-lock mr-2"></i>Keamanan
            </button>
            <button onclick="showTab('danger')" id="tabDanger" class="px-4 py-3 text-sm font-medium rounded-t-xl border-b-2 border-transparent text-slate-400 hover:text-red-500 hover:bg-red-50/50 transition-all -mb-px">
                <i class="bi bi-exclamation-triangle mr-2"></i>Zona Bahaya
            </button>
        </div>
        
        <!-- Tab Contents -->
        <div class="p-6">
            
            <!-- Profile Tab -->
            <div id="contentProfile" class="tab-content">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 flex items-center justify-center">
                        <i class="bi bi-person-vcard text-emerald-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-slate-800">Informasi Pribadi</h4>
                        <p class="text-sm text-slate-500">Update nama dan email Anda</p>
                    </div>
                </div>
                
                <form method="POST" class="space-y-5">
                    <input type="hidden" name="form_type" value="profile">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="form-label text-sm flex items-center gap-2">
                                <i class="bi bi-person text-slate-400"></i>
                                Nama Lengkap
                            </label>
                            <input type="text" name="nama" required 
                                   class="form-input" 
                                   value="<?= htmlspecialchars($user['nama']) ?>">
                        </div>
                        
                        <div>
                            <label class="form-label text-sm flex items-center gap-2">
                                <i class="bi bi-envelope text-slate-400"></i>
                                Email
                            </label>
                            <input type="email" name="email" required 
                                   class="form-input" 
                                   value="<?= htmlspecialchars($user['email']) ?>">
                        </div>
                    </div>
                    
                    <div class="flex justify-end pt-4 border-t border-slate-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-lg"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Password Tab -->
            <div id="contentPassword" class="tab-content hidden">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-100">
                    <div class="w-10 h-10 rounded-xl bg-sky-100 flex items-center justify-center">
                        <i class="bi bi-shield-check text-sky-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-slate-800">Keamanan Akun</h4>
                        <p class="text-sm text-slate-500">Ubah password untuk keamanan lebih baik</p>
                    </div>
                </div>
                
                <form method="POST" class="space-y-5">
                    <input type="hidden" name="form_type" value="password">
                    
                    <div>
                        <label class="form-label text-sm flex items-center gap-2">
                            <i class="bi bi-key text-slate-400"></i>
                            Password Saat Ini
                        </label>
                        <input type="password" name="current_password" required 
                               class="form-input" placeholder="Masukkan password saat ini">
                    </div>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="form-label text-sm flex items-center gap-2">
                                <i class="bi bi-lock text-slate-400"></i>
                                Password Baru
                            </label>
                            <input type="password" name="new_password" required minlength="6"
                                   class="form-input" placeholder="Minimal 6 karakter">
                        </div>
                        
                        <div>
                            <label class="form-label text-sm flex items-center gap-2">
                                <i class="bi bi-lock-fill text-slate-400"></i>
                                Konfirmasi Password
                            </label>
                            <input type="password" name="confirm_password" required 
                                   class="form-input" placeholder="Ulangi password baru">
                        </div>
                    </div>
                    
                    <div class="p-4 rounded-xl bg-amber-50 border border-amber-200">
                        <p class="text-sm text-amber-700 flex items-start gap-2">
                            <i class="bi bi-info-circle mt-0.5"></i>
                            <span>Gunakan kombinasi huruf besar, kecil, angka, dan simbol untuk keamanan optimal.</span>
                        </p>
                    </div>
                    
                    <div class="flex justify-end pt-4 border-t border-slate-100">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-key"></i>
                            Ubah Password
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Danger Tab -->
            <div id="contentDanger" class="tab-content hidden">
                <div class="flex items-center gap-3 mb-6 pb-4 border-b border-red-100">
                    <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center">
                        <i class="bi bi-exclamation-octagon text-red-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-red-700">Zona Bahaya</h4>
                        <p class="text-sm text-red-500">Tindakan di sini tidak dapat dibatalkan</p>
                    </div>
                </div>
                
                <div class="p-5 bg-gradient-to-br from-red-50 to-rose-50 rounded-xl border-2 border-red-200 border-dashed">
                    <div class="flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-red-100 flex items-center justify-center flex-shrink-0">
                            <i class="bi bi-trash3 text-red-500 text-xl"></i>
                        </div>
                        <div class="flex-1">
                            <h5 class="font-semibold text-red-700 text-lg">Hapus Akun Permanen</h5>
                            <p class="text-sm text-red-600/80 mt-1 mb-4">
                                Menghapus akun akan menghilangkan semua data Anda secara permanen termasuk:
                            </p>
                            <ul class="text-sm text-red-600/70 space-y-1 mb-4">
                                <li class="flex items-center gap-2">
                                    <i class="bi bi-x-circle text-red-400"></i>
                                    <?= number_format($totalTransactions) ?> transaksi
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="bi bi-x-circle text-red-400"></i>
                                    <?= number_format($totalCategories) ?> kategori
                                </li>
                                <li class="flex items-center gap-2">
                                    <i class="bi bi-x-circle text-red-400"></i>
                                    Semua wishlist dan share links
                                </li>
                            </ul>
                            
                            <form action="profil.php" method="POST" class="mt-4 pt-4 border-t border-red-200">
                                <input type="hidden" name="form_type" value="delete_account">
                                
                                <div class="mb-4">
                                    <label class="text-sm font-medium text-red-700 mb-2 block">
                                        Ketik password Anda untuk konfirmasi:
                                    </label>
                                    <input type="password" name="delete_password" required 
                                           class="form-input border-red-200 focus:border-red-400 focus:ring-red-100 bg-white">
                                </div>
                                
                                <button type="submit" class="btn btn-danger">
                                    <i class="bi bi-trash3"></i>
                                    Hapus Akun Saya Sekarang
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
function showTab(tab) {
    // Hide all contents
    document.querySelectorAll('.tab-content').forEach(c => c.classList.add('hidden'));
    
    // Reset all tabs
    document.querySelectorAll('[id^="tab"]').forEach(t => {
        t.classList.remove('border-emerald-500', 'text-emerald-600', 'bg-white');
        t.classList.add('border-transparent', 'text-slate-400');
    });
    
    // Show selected content
    document.getElementById('content' + tab.charAt(0).toUpperCase() + tab.slice(1)).classList.remove('hidden');
    
    // Activate tab
    const tabBtn = document.getElementById('tab' + tab.charAt(0).toUpperCase() + tab.slice(1));
    tabBtn.classList.remove('border-transparent', 'text-slate-400');
    tabBtn.classList.add('border-emerald-500', 'text-emerald-600', 'bg-white');
}
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>
