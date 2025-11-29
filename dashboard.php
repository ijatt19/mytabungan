<?php
require_once __DIR__ . '/auth/cek_masuk.php';
require_once __DIR__ . '/layout/header.php';

$id_pengguna = $_SESSION['id_pengguna'];
$nama_pengguna = $_SESSION['nama_lengkap'] ?? 'Pengguna';

// --- Modal Configuration ---
$transaksi_action_url = 'transaksi/tambah.php';
$wishlist_action_url = 'wishlist/tambah.php';
$redirect_to = '../dashboard.php';

// --- Data Fetching Logic ---
$total_pemasukan = 0;
$total_pengeluaran = 0;
$saldo_akhir = 0;
$recent_transactions = [];

try {
    // 1. Total Pemasukan
    $sql_pemasukan = "SELECT SUM(jumlah) AS total FROM transaksi t JOIN kategori k ON t.id_kategori = k.id_kategori WHERE t.id_pengguna = ? AND k.tipe = 'Pemasukan'";
    $stmt_pemasukan = $pdo->prepare($sql_pemasukan);
    $stmt_pemasukan->execute([$id_pengguna]);
    $total_pemasukan = $stmt_pemasukan->fetch()['total'] ?? 0;

    // 2. Total Pengeluaran
    $sql_pengeluaran = "SELECT SUM(jumlah) AS total FROM transaksi t JOIN kategori k ON t.id_kategori = k.id_kategori WHERE t.id_pengguna = ? AND k.tipe = 'Pengeluaran'";
    $stmt_pengeluaran = $pdo->prepare($sql_pengeluaran);
    $stmt_pengeluaran->execute([$id_pengguna]);
    $total_pengeluaran = $stmt_pengeluaran->fetch()['total'] ?? 0;

    $saldo_akhir = $total_pemasukan - $total_pengeluaran;

    // 3. Recent Transactions (Limit 5)
    $sql_recent = "SELECT t.tanggal_transaksi, t.keterangan, k.nama_kategori, k.tipe, t.jumlah 
                   FROM transaksi t
                   JOIN kategori k ON t.id_kategori = k.id_kategori 
                   WHERE t.id_pengguna = ? 
                   ORDER BY t.tanggal_transaksi DESC, t.id_transaksi DESC
                   LIMIT 5";
    $stmt_recent = $pdo->prepare($sql_recent);
    $stmt_recent->execute([$id_pengguna]);
    $recent_transactions = $stmt_recent->fetchAll();

    // 4. Chart Data
    require_once __DIR__ . '/includes/chart_data.php';

    // 5. Daftar Kategori for Modal (Re-fetch to ensure availability)
    $stmt_kategori = $pdo->prepare("SELECT * FROM kategori WHERE id_pengguna = ? ORDER BY tipe, nama_kategori");
    $stmt_kategori->execute([$id_pengguna]);
    $daftar_kategori = $stmt_kategori->fetchAll();

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// --- Dynamic Greeting Logic ---
$hour = date('H');
if ($hour < 11) {
    $greeting = 'Selamat Pagi';
    $greeting_icon = 'bi-sun';
} elseif ($hour < 15) {
    $greeting = 'Selamat Siang';
    $greeting_icon = 'bi-brightness-high';
} elseif ($hour < 18) {
    $greeting = 'Selamat Sore';
    $greeting_icon = 'bi-sunset';
} else {
    $greeting = 'Selamat Malam';
    $greeting_icon = 'bi-moon-stars';
}

// --- Financial Health Logic ---
$health_percent = 0;
if ($total_pemasukan > 0) {
    $health_percent = ($total_pengeluaran / $total_pemasukan) * 100;
}
$health_status = '';
$health_color = '';
$health_bg_color = '';
$health_bar_color = '';

if ($health_percent < 50) {
    $health_status = 'Sehat';
    $health_color = 'text-emerald-600';
    $health_bg_color = 'bg-emerald-100';
    $health_bar_color = 'bg-emerald-500';
} elseif ($health_percent < 80) {
    $health_status = 'Waspada';
    $health_color = 'text-yellow-600';
    $health_bg_color = 'bg-yellow-100';
    $health_bar_color = 'bg-yellow-500';
} else {
    $health_status = 'Bahaya';
    $health_color = 'text-red-600';
    $health_bg_color = 'bg-red-100';
    $health_bar_color = 'bg-red-500';
}
?>

<?php require_once __DIR__ . '/layout/sidebar.php'; ?>

<main class="min-h-screen md:ml-64 px-4 sm:px-6 py-8 bg-slate-50">

    <!-- Welcome Section & Quick Actions -->
    <div class="flex flex-col xl:flex-row justify-between items-start xl:items-center mb-8 gap-6">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 flex items-center gap-3">
                <span class="p-2 bg-white rounded-xl shadow-sm border border-slate-100">
                    <i class="bi <?php echo $greeting_icon; ?> text-yellow-500"></i>
                </span>
                <span><?php echo $greeting; ?>, <?php echo htmlspecialchars($nama_pengguna); ?>!</span>
            </h1>
            <p class="text-slate-500 mt-2 ml-1">Pantau keuanganmu, wujudkan impianmu.</p>
        </div>
        
        <div class="flex flex-wrap gap-3 w-full xl:w-auto">
            <button onclick="generateShareLink()" class="flex-1 xl:flex-none inline-flex justify-center items-center px-4 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50 hover:text-emerald-600 hover:border-emerald-200 transition-all shadow-sm">
                <i class="bi bi-share mr-2"></i> Bagikan
            </button>
            <button onclick="openQuickAction('Pemasukan')" class="flex-1 xl:flex-none inline-flex justify-center items-center px-4 py-2.5 bg-emerald-600 text-white text-sm font-semibold rounded-xl hover:bg-emerald-700 hover:shadow-lg hover:shadow-emerald-200 transition-all transform hover:-translate-y-0.5">
                <i class="bi bi-plus-lg mr-2"></i> Pemasukan
            </button>
            <button onclick="openQuickAction('Pengeluaran')" class="flex-1 xl:flex-none inline-flex justify-center items-center px-4 py-2.5 bg-red-600 text-white text-sm font-semibold rounded-xl hover:bg-red-700 hover:shadow-lg hover:shadow-red-200 transition-all transform hover:-translate-y-0.5">
                <i class="bi bi-dash-lg mr-2"></i> Pengeluaran
            </button>
            <button onclick="document.getElementById('tambahWishlistModal').classList.remove('hidden')" class="flex-1 xl:flex-none inline-flex justify-center items-center px-4 py-2.5 bg-purple-600 text-white text-sm font-semibold rounded-xl hover:bg-purple-700 hover:shadow-lg hover:shadow-purple-200 transition-all transform hover:-translate-y-0.5">
                <i class="bi bi-stars mr-2"></i> Tambah Impian
            </button>
        </div>
    </div>

    <!-- Summary Cards (Gradient) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Pemasukan -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-100 hover:shadow-xl hover:shadow-emerald-200 transition-all duration-300 group">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-24 h-24 bg-black opacity-5 rounded-full blur-xl"></div>
            
            <div class="relative z-10 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm border border-white/10">
                        <i class="bi bi-arrow-down-left text-xl"></i>
                    </div>
                    <span class="font-medium text-emerald-50 tracking-wide text-sm uppercase">Pemasukan</span>
                </div>
                <h3 class="text-3xl font-bold tracking-tight"><?php echo formatRupiah($total_pemasukan); ?></h3>
                <p class="text-sm text-emerald-100 mt-2 opacity-90 font-medium">Bulan ini</p>
            </div>
        </div>

        <!-- Pengeluaran -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-rose-500 to-red-600 text-white shadow-lg shadow-rose-100 hover:shadow-xl hover:shadow-rose-200 transition-all duration-300 group">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-24 h-24 bg-black opacity-5 rounded-full blur-xl"></div>
            
            <div class="relative z-10 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm border border-white/10">
                        <i class="bi bi-arrow-up-right text-xl"></i>
                    </div>
                    <span class="font-medium text-rose-50 tracking-wide text-sm uppercase">Pengeluaran</span>
                </div>
                <h3 class="text-3xl font-bold tracking-tight"><?php echo formatRupiah($total_pengeluaran); ?></h3>
                <p class="text-sm text-rose-100 mt-2 opacity-90 font-medium">Bulan ini</p>
            </div>
        </div>

        <!-- Saldo -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500 to-indigo-600 text-white shadow-lg shadow-blue-100 hover:shadow-xl hover:shadow-blue-200 transition-all duration-300 group">
            <div class="absolute top-0 right-0 -mt-8 -mr-8 w-32 h-32 bg-white opacity-10 rounded-full blur-2xl group-hover:scale-150 transition-transform duration-500"></div>
            <div class="absolute bottom-0 left-0 -mb-8 -ml-8 w-24 h-24 bg-black opacity-5 rounded-full blur-xl"></div>
            
            <div class="relative z-10 p-6">
                <div class="flex items-center gap-3 mb-4">
                    <div class="p-2 bg-white/20 rounded-lg backdrop-blur-sm border border-white/10">
                        <i class="bi bi-wallet2 text-xl"></i>
                    </div>
                    <span class="font-medium text-blue-50 tracking-wide text-sm uppercase">Saldo Akhir</span>
                </div>
                <h3 class="text-3xl font-bold tracking-tight"><?php echo formatRupiah($saldo_akhir); ?></h3>
                <p class="text-sm text-blue-100 mt-2 opacity-90 font-medium">Total Aset</p>
            </div>
        </div>
    </div>

    <!-- Financial Overview (Chart & Health) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Chart -->
        <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
            <div class="flex justify-between items-center mb-6">
                <h5 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <i class="bi bi-graph-up"></i>
                    </span>
                    Analisis Keuangan
                </h5>
                <select class="text-sm border-none bg-slate-50 rounded-lg px-3 py-1.5 font-medium text-slate-600 focus:ring-0 cursor-pointer hover:bg-slate-100 transition-colors">
                    <option>Bulan Ini</option>
                    <option>Tahun Ini</option>
                </select>
            </div>
            <div class="h-72 w-full">
                <canvas id="myChart"></canvas>
            </div>
        </div>

        <!-- Financial Health Widget -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex flex-col">
            <h5 class="text-lg font-bold text-slate-900 mb-2 flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-rose-100 flex items-center justify-center text-rose-600">
                    <i class="bi bi-heart-pulse"></i>
                </span>
                Kesehatan
            </h5>
            <p class="text-slate-500 text-sm mb-8">Indikator kesehatan berdasarkan rasio pengeluaran vs pemasukan.</p>
            
            <div class="flex-1 flex flex-col justify-center items-center mb-6">
                <div class="relative w-40 h-40 flex items-center justify-center">
                    <!-- Circular Progress Background -->
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="80" cy="80" r="70" stroke="currentColor" stroke-width="12" fill="transparent" class="text-slate-100" />
                        <circle cx="80" cy="80" r="70" stroke="currentColor" stroke-width="12" fill="transparent" stroke-dasharray="440" stroke-dashoffset="<?php echo 440 - (440 * min($health_percent, 100) / 100); ?>" class="<?php echo str_replace('text-', 'text-', $health_color); ?> transition-all duration-1000 ease-out" stroke-linecap="round" />
                    </svg>
                    <div class="absolute flex flex-col items-center">
                        <span class="text-3xl font-bold text-slate-800"><?php echo number_format($health_percent, 1); ?>%</span>
                        <span class="text-xs text-slate-400 font-medium uppercase tracking-wide">Ratio</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-between items-center p-4 bg-slate-50 rounded-xl border border-slate-100">
                <span class="text-sm font-medium text-slate-600">Status Keuangan</span>
                <span class="px-3 py-1 text-xs font-bold uppercase rounded-lg <?php echo $health_color . ' ' . $health_bg_color; ?>">
                    <?php echo $health_status; ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Wishlist Tracker Widget -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 mb-8 overflow-hidden">
        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row justify-between items-center gap-4">
            <h5 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-purple-100 flex items-center justify-center text-purple-600">
                    <i class="bi bi-stars"></i>
                </span>
                Target Impian
            </h5>
            
            <div class="flex p-1 bg-slate-100 rounded-xl">
                <button onclick="switchWishlistTab('Tinggi')" id="tab-Tinggi" class="px-4 py-1.5 text-sm font-medium rounded-lg transition-all shadow-sm bg-white text-slate-900">Tinggi</button>
                <button onclick="switchWishlistTab('Sedang')" id="tab-Sedang" class="px-4 py-1.5 text-sm font-medium rounded-lg transition-all text-slate-500 hover:text-slate-900">Sedang</button>
                <button onclick="switchWishlistTab('Rendah')" id="tab-Rendah" class="px-4 py-1.5 text-sm font-medium rounded-lg transition-all text-slate-500 hover:text-slate-900">Rendah</button>
            </div>
        </div>

        <div class="p-6 bg-slate-50/50">
            <?php require_once __DIR__ . '/dashboard/wishlist_carousel.php'; ?>
            
            <div class="mt-6 text-center">
                <a href="wishlist/index.php" class="inline-flex items-center gap-2 px-5 py-2.5 bg-purple-600 text-white font-semibold rounded-xl hover:bg-purple-700 hover:shadow-lg hover:shadow-purple-200 transition-all transform hover:-translate-y-0.5 text-sm">
                    Lihat Selengkapnya <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-6 py-5 border-b border-slate-100 flex justify-between items-center">
            <h5 class="text-lg font-bold text-slate-900 flex items-center gap-2">
                <span class="w-8 h-8 rounded-lg bg-blue-100 flex items-center justify-center text-blue-600">
                    <i class="bi bi-clock-history"></i>
                </span>
                Transaksi Terakhir
            </h5>
            <a href="transaksi/index.php" class="inline-flex items-center text-sm font-semibold text-emerald-600 hover:text-emerald-700 group">
                Lihat Semua 
                <i class="bi bi-arrow-right ml-1 transform group-hover:translate-x-1 transition-transform"></i>
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100">
                <thead class="bg-slate-50/80">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-slate-500 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-slate-500 uppercase tracking-wider">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-slate-100">
                    <?php if (empty($recent_transactions)): ?>
                        <tr>
                            <td colspan="4" class="px-6 py-16 text-center text-slate-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-4 text-slate-400">
                                        <i class="bi bi-inbox text-3xl"></i>
                                    </div>
                                    <p class="font-medium text-slate-900">Belum ada transaksi</p>
                                    <p class="text-sm mt-1">Mulai catat keuanganmu hari ini!</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recent_transactions as $t): 
                            $is_pemasukan = $t['tipe'] === 'Pemasukan';
                            $amount_color = $is_pemasukan ? 'text-emerald-600' : 'text-rose-600';
                            $icon_bg = $is_pemasukan ? 'bg-emerald-100 text-emerald-600' : 'bg-rose-100 text-rose-600';
                            $icon = $is_pemasukan ? 'bi-arrow-down-left' : 'bi-arrow-up-right';
                        ?>
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-slate-900"><?php echo date('d', strtotime($t['tanggal_transaksi'])); ?></span>
                                    <span class="text-xs text-slate-500"><?php echo date('M Y', strtotime($t['tanggal_transaksi'])); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full <?php echo $icon_bg; ?> flex items-center justify-center text-sm ring-4 ring-white">
                                        <i class="bi <?php echo $icon; ?>"></i>
                                    </div>
                                    <span class="text-sm font-medium text-slate-700"><?php echo htmlspecialchars($t['nama_kategori']); ?></span>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 font-medium">
                                <?php echo htmlspecialchars($t['keterangan'] ?: '-'); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold <?php echo $amount_color; ?>">
                                <?php echo $is_pemasukan ? '+' : '-'; ?> <?php echo formatRupiah($t['jumlah']); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>

<!-- Modals -->
<?php require_once __DIR__ . '/modal/transaksi_modal.php'; ?>
<?php require_once __DIR__ . '/modal/wishlist_modal.php'; ?>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart Data
    const chartData = {
        labels: <?php echo json_encode($labels); ?>,
        pemasukan: <?php echo json_encode($pemasukan_data); ?>,
        pengeluaran: <?php echo json_encode($pengeluaran_data); ?>
    };

    // Quick Action Handler
    function openQuickAction(type) {
        const modal = document.getElementById('tambahTransaksiModal');
        const title = document.getElementById('tambahTransaksiModalLabel');
        const kategoriSelect = document.getElementById('id_kategori_tambah');
        
        // Update Title
        title.textContent = 'Tambah ' + type;
        
        // Filter Categories
        const optgroups = kategoriSelect.getElementsByTagName('optgroup');
        for (let group of optgroups) {
            if (group.label === type) {
                group.style.display = '';
                // Select first option in this group
                const firstOption = group.querySelector('option');
                if (firstOption) firstOption.selected = true;
            } else {
                group.style.display = 'none';
            }
        }
        
        // Open Modal
        modal.classList.remove('hidden');
    }
</script>
<script src="js/dashboard.js?v=<?php echo time(); ?>"></script>

<?php require_once __DIR__ . '/layout/footer.php'; ?>