<?php
/**
 * Dashboard Page
 * MyTabungan - Personal Finance Management
 */

require_once __DIR__ . '/auth/auth.php';
require_once __DIR__ . '/config/database.php';

// Require login
requireLogin();

$pageTitle = 'Dashboard';
$userId = getCurrentUserId();
$pdo = getConnection();

// Get current month and year
$currentMonth = date('m');
$currentYear = date('Y');

// =====================================================
// Calculate Summary Statistics
// =====================================================

// Total Income (current month)
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(t.jumlah), 0) as total 
    FROM transaksi t 
    JOIN kategori k ON t.id_kategori = k.id_kategori 
    WHERE t.id_pengguna = ? 
    AND k.tipe = 'Pemasukan'
    AND MONTH(t.tanggal) = ? 
    AND YEAR(t.tanggal) = ?
");
$stmt->execute([$userId, $currentMonth, $currentYear]);
$totalIncome = $stmt->fetch()['total'];

// Total Expense (current month)
$stmt = $pdo->prepare("
    SELECT COALESCE(SUM(t.jumlah), 0) as total 
    FROM transaksi t 
    JOIN kategori k ON t.id_kategori = k.id_kategori 
    WHERE t.id_pengguna = ? 
    AND k.tipe = 'Pengeluaran'
    AND MONTH(t.tanggal) = ? 
    AND YEAR(t.tanggal) = ?
");
$stmt->execute([$userId, $currentMonth, $currentYear]);
$totalExpense = $stmt->fetch()['total'];

// Current Balance (all time)
$stmt = $pdo->prepare("
    SELECT 
        COALESCE(SUM(CASE WHEN k.tipe = 'Pemasukan' THEN t.jumlah ELSE 0 END), 0) -
        COALESCE(SUM(CASE WHEN k.tipe = 'Pengeluaran' THEN t.jumlah ELSE 0 END), 0) as balance
    FROM transaksi t 
    JOIN kategori k ON t.id_kategori = k.id_kategori 
    WHERE t.id_pengguna = ?
");
$stmt->execute([$userId]);
$balance = $stmt->fetch()['balance'];

// Financial Health (hybrid: monthly ratio + total balance)
$healthStatus = getFinancialHealth($totalIncome, $totalExpense, $balance);

// =====================================================
// Get Recent Transactions
// =====================================================
$stmt = $pdo->prepare("
    SELECT t.*, k.nama_kategori, k.tipe, k.icon, k.warna
    FROM transaksi t 
    JOIN kategori k ON t.id_kategori = k.id_kategori 
    WHERE t.id_pengguna = ?
    ORDER BY t.tanggal DESC, t.created_at DESC
    LIMIT 5
");
$stmt->execute([$userId]);
$recentTransactions = $stmt->fetchAll();

// =====================================================
// Get Monthly Data for Chart (Last 6 months)
// =====================================================
$chartLabels = [];
$chartIncome = [];
$chartExpense = [];

for ($i = 5; $i >= 0; $i--) {
    $month = date('m', strtotime("-$i months"));
    $year = date('Y', strtotime("-$i months"));
    $chartLabels[] = getMonthName((int)$month);
    
    // Income for this month
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(t.jumlah), 0) as total 
        FROM transaksi t 
        JOIN kategori k ON t.id_kategori = k.id_kategori 
        WHERE t.id_pengguna = ? 
        AND k.tipe = 'Pemasukan'
        AND MONTH(t.tanggal) = ? 
        AND YEAR(t.tanggal) = ?
    ");
    $stmt->execute([$userId, $month, $year]);
    $chartIncome[] = (float)$stmt->fetch()['total'];
    
    // Expense for this month
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(t.jumlah), 0) as total 
        FROM transaksi t 
        JOIN kategori k ON t.id_kategori = k.id_kategori 
        WHERE t.id_pengguna = ? 
        AND k.tipe = 'Pengeluaran'
        AND MONTH(t.tanggal) = ? 
        AND YEAR(t.tanggal) = ?
    ");
    $stmt->execute([$userId, $month, $year]);
    $chartExpense[] = (float)$stmt->fetch()['total'];
}

// =====================================================
// Get Expense by Category (current month)
// =====================================================
$stmt = $pdo->prepare("
    SELECT k.nama_kategori, k.warna, COALESCE(SUM(t.jumlah), 0) as total 
    FROM transaksi t 
    JOIN kategori k ON t.id_kategori = k.id_kategori 
    WHERE t.id_pengguna = ? 
    AND k.tipe = 'Pengeluaran'
    AND MONTH(t.tanggal) = ? 
    AND YEAR(t.tanggal) = ?
    GROUP BY k.id_kategori
    ORDER BY total DESC
    LIMIT 5
");
$stmt->execute([$userId, $currentMonth, $currentYear]);
$expenseByCategory = $stmt->fetchAll();

// Include header
include __DIR__ . '/layout/header.php';
include __DIR__ . '/layout/sidebar.php';
?>

<!-- Dashboard Content -->
<div class="space-y-6">
    
    <!-- Welcome Section -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Selamat <?= date('H') < 12 ? 'Pagi' : (date('H') < 15 ? 'Siang' : (date('H') < 18 ? 'Sore' : 'Malam')) ?>, 
                <?= htmlspecialchars(explode(' ', getCurrentUserName())[0]) ?>! 👋
            </h2>
            <p class="text-slate-500 mt-1">Ini ringkasan keuangan Anda bulan <?= getMonthName((int)$currentMonth) ?> <?= $currentYear ?></p>
        </div>
        <a href="transaksi.php?action=add" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah Transaksi</span>
        </a>
    </div>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- Total Income Card -->
        <div class="card p-6 animate-fade-in-up">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl gradient-income flex items-center justify-center">
                    <i class="bi bi-arrow-down-circle text-white text-xl"></i>
                </div>
                <span class="badge badge-success">
                    <i class="bi bi-graph-up-arrow"></i>
                    Bulan Ini
                </span>
            </div>
            <p class="text-sm text-slate-500 mb-1">Total Pemasukan</p>
            <p class="text-2xl font-bold text-emerald-600"><?= formatRupiah($totalIncome) ?></p>
        </div>
        
        <!-- Total Expense Card -->
        <div class="card p-6 animate-fade-in-up delay-100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl gradient-expense flex items-center justify-center">
                    <i class="bi bi-arrow-up-circle text-white text-xl"></i>
                </div>
                <span class="badge badge-danger">
                    <i class="bi bi-graph-down-arrow"></i>
                    Bulan Ini
                </span>
            </div>
            <p class="text-sm text-slate-500 mb-1">Total Pengeluaran</p>
            <p class="text-2xl font-bold text-red-600"><?= formatRupiah($totalExpense) ?></p>
        </div>
        
        <!-- Balance Card -->
        <div class="card p-6 animate-fade-in-up delay-200">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl gradient-balance flex items-center justify-center">
                    <i class="bi bi-wallet2 text-white text-xl"></i>
                </div>
                <span class="badge badge-info">
                    <i class="bi bi-bank"></i>
                    Total
                </span>
            </div>
            <p class="text-sm text-slate-500 mb-1">Saldo Saat Ini</p>
            <p class="text-2xl font-bold <?= $balance >= 0 ? 'text-sky-600' : 'text-red-600' ?>">
                <?= formatRupiah(abs($balance)) ?>
            </p>
        </div>
        
        <!-- Financial Health Card -->
        <div class="card p-6 animate-fade-in-up delay-300">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-xl bg-<?= $healthStatus['color'] ?>-500 flex items-center justify-center">
                    <i class="bi <?= $healthStatus['icon'] ?> text-white text-xl"></i>
                </div>
                <span class="badge bg-<?= $healthStatus['color'] ?>-100 text-<?= $healthStatus['color'] ?>-700">
                    <?= $healthStatus['status'] ?>
                </span>
            </div>
            <p class="text-sm text-slate-500 mb-1">Kondisi Keuangan</p>
            <p class="text-sm text-slate-600"><?= $healthStatus['message'] ?></p>
        </div>
    </div>
    
    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Monthly Overview Chart -->
        <div class="lg:col-span-2 card p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-slate-800">
                    <i class="bi bi-bar-chart-fill text-emerald-500 mr-2"></i>
                    Grafik Bulanan
                </h3>
                <span class="text-sm text-slate-400">6 bulan terakhir</span>
            </div>
            <div class="h-64">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
        
        <!-- Expense by Category -->
        <div class="card p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-slate-800">
                    <i class="bi bi-pie-chart-fill text-emerald-500 mr-2"></i>
                    Pengeluaran
                </h3>
                <span class="text-sm text-slate-400">Bulan ini</span>
            </div>
            
            <?php if (empty($expenseByCategory)): ?>
            <div class="empty-state py-8">
                <i class="bi bi-pie-chart text-4xl"></i>
                <p class="text-sm">Belum ada pengeluaran</p>
            </div>
            <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($expenseByCategory as $cat): ?>
                <?php $percentage = $totalExpense > 0 ? ($cat['total'] / $totalExpense) * 100 : 0; ?>
                <div>
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-sm font-medium text-slate-700"><?= htmlspecialchars($cat['nama_kategori']) ?></span>
                        <span class="text-sm text-slate-500"><?= formatShortCurrency($cat['total']) ?></span>
                    </div>
                    <div class="progress-bar">
                        <div class="progress-bar-fill" style="width: <?= $percentage ?>%; background-color: <?= $cat['warna'] ?>"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Recent Transactions -->
    <div class="card p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-slate-800">
                <i class="bi bi-clock-history text-emerald-500 mr-2"></i>
                Transaksi Terbaru
            </h3>
            <a href="transaksi.php" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                Lihat Semua <i class="bi bi-arrow-right"></i>
            </a>
        </div>
        
        <?php if (empty($recentTransactions)): ?>
        <div class="empty-state py-12">
            <i class="bi bi-receipt"></i>
            <p class="font-medium text-slate-600 mb-1">Belum ada transaksi</p>
            <p class="text-sm mb-4">Mulai catat transaksi keuangan Anda</p>
            <a href="transaksi.php?action=add" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i>
                Tambah Transaksi
            </a>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="text-left text-sm text-slate-500 border-b border-slate-100">
                        <th class="pb-3 font-medium">Tanggal</th>
                        <th class="pb-3 font-medium">Kategori</th>
                        <th class="pb-3 font-medium hidden md:table-cell">Keterangan</th>
                        <th class="pb-3 font-medium text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($recentTransactions as $trx): ?>
                    <tr class="hover:bg-slate-50 transition-colors">
                        <td class="py-3">
                            <span class="text-sm text-slate-600"><?= formatTanggal($trx['tanggal'], 'relative') ?></span>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: <?= $trx['warna'] ?>20">
                                    <i class="bi <?= $trx['icon'] ?>" style="color: <?= $trx['warna'] ?>"></i>
                                </div>
                                <span class="text-sm font-medium text-slate-700"><?= htmlspecialchars($trx['nama_kategori']) ?></span>
                            </div>
                        </td>
                        <td class="py-3 hidden md:table-cell">
                            <span class="text-sm text-slate-500"><?= htmlspecialchars($trx['keterangan'] ?: '-') ?></span>
                        </td>
                        <td class="py-3 text-right">
                            <span class="text-sm font-semibold <?= $trx['tipe'] === 'Pemasukan' ? 'text-emerald-600' : 'text-red-600' ?>">
                                <?= $trx['tipe'] === 'Pemasukan' ? '+' : '-' ?><?= formatRupiah($trx['jumlah']) ?>
                            </span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Chart.js Initialization -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const labels = <?= json_encode($chartLabels) ?>;
    const incomeData = <?= json_encode($chartIncome) ?>;
    const expenseData = <?= json_encode($chartExpense) ?>;
    
    MyTabungan.initMonthlyChart('monthlyChart', labels, incomeData, expenseData);
});
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>
