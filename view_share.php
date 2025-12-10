<?php
/**
 * View Shared Financial Report
 * MyTabungan - Personal Finance Management
 */

require_once __DIR__ . '/auth/auth.php';
require_once __DIR__ . '/config/database.php';

// Require login to view shared content
requireLogin();

$pdo = getConnection();
$token = $_GET['token'] ?? '';
$currentUserId = getCurrentUserId();

if (empty($token)) {
    setFlashMessage('error', 'Link share tidak valid.');
    redirect('dashboard.php');
}

// Get share token data
$stmt = $pdo->prepare("
    SELECT st.*, p.nama as owner_name, p.email as owner_email
    FROM share_token st
    JOIN pengguna p ON st.id_pengguna = p.id_pengguna
    WHERE st.token = ?
");
$stmt->execute([$token]);
$share = $stmt->fetch();

if (!$share) {
    setFlashMessage('error', 'Link share tidak ditemukan atau sudah dihapus.');
    redirect('dashboard.php');
}

// Check if expired
if ($share['expires_at'] && strtotime($share['expires_at']) < time()) {
    setFlashMessage('error', 'Link share sudah kadaluarsa.');
    redirect('dashboard.php');
}

$ownerId = $share['id_pengguna'];
$isOwner = $currentUserId == $ownerId;

// Update view count and track viewer (only if viewer is not the owner)
if (!$isOwner) {
    // Update view count
    $stmt = $pdo->prepare("UPDATE share_token SET view_count = view_count + 1, last_viewed_at = NOW() WHERE id_share = ?");
    $stmt->execute([$share['id_share']]);
    
    // Track who viewed (insert or update viewed_at)
    try {
        $stmt = $pdo->prepare("
            INSERT INTO share_viewers (id_share, id_pengguna, viewed_at) 
            VALUES (?, ?, NOW())
            ON DUPLICATE KEY UPDATE viewed_at = NOW()
        ");
        $stmt->execute([$share['id_share'], $currentUserId]);
    } catch (PDOException $e) {
        // Table might not exist yet, ignore error
        error_log("Share viewer tracking error: " . $e->getMessage());
    }
}

// =====================================================
// Get Owner's Financial Data
// =====================================================

// Get current month/year data
$month = date('m');
$year = date('Y');

// Summary - All time
$stmt = $pdo->prepare("
    SELECT 
        SUM(CASE WHEN k.tipe = 'Pemasukan' THEN t.jumlah ELSE 0 END) as total_income,
        SUM(CASE WHEN k.tipe = 'Pengeluaran' THEN t.jumlah ELSE 0 END) as total_expense
    FROM transaksi t
    JOIN kategori k ON t.id_kategori = k.id_kategori
    WHERE t.id_pengguna = ?
");
$stmt->execute([$ownerId]);
$summary = $stmt->fetch();

$totalIncome = $summary['total_income'] ?? 0;
$totalExpense = $summary['total_expense'] ?? 0;
$balance = $totalIncome - $totalExpense;

// This month summary
$stmt = $pdo->prepare("
    SELECT 
        SUM(CASE WHEN k.tipe = 'Pemasukan' THEN t.jumlah ELSE 0 END) as month_income,
        SUM(CASE WHEN k.tipe = 'Pengeluaran' THEN t.jumlah ELSE 0 END) as month_expense
    FROM transaksi t
    JOIN kategori k ON t.id_kategori = k.id_kategori
    WHERE t.id_pengguna = ? AND MONTH(t.tanggal) = ? AND YEAR(t.tanggal) = ?
");
$stmt->execute([$ownerId, $month, $year]);
$monthSummary = $stmt->fetch();

$monthIncome = $monthSummary['month_income'] ?? 0;
$monthExpense = $monthSummary['month_expense'] ?? 0;

// Recent transactions (last 20)
$stmt = $pdo->prepare("
    SELECT t.*, k.nama_kategori, k.tipe, k.icon, k.warna
    FROM transaksi t
    JOIN kategori k ON t.id_kategori = k.id_kategori
    WHERE t.id_pengguna = ?
    ORDER BY t.tanggal DESC, t.created_at DESC
    LIMIT 20
");
$stmt->execute([$ownerId]);
$transactions = $stmt->fetchAll();

// Monthly trend (last 6 months)
$stmt = $pdo->prepare("
    SELECT 
        DATE_FORMAT(t.tanggal, '%Y-%m') as month,
        SUM(CASE WHEN k.tipe = 'Pemasukan' THEN t.jumlah ELSE 0 END) as income,
        SUM(CASE WHEN k.tipe = 'Pengeluaran' THEN t.jumlah ELSE 0 END) as expense
    FROM transaksi t
    JOIN kategori k ON t.id_kategori = k.id_kategori
    WHERE t.id_pengguna = ? AND t.tanggal >= DATE_SUB(CURDATE(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(t.tanggal, '%Y-%m')
    ORDER BY month ASC
");
$stmt->execute([$ownerId]);
$monthlyTrend = $stmt->fetchAll();

// Category breakdown (expenses)
$stmt = $pdo->prepare("
    SELECT k.nama_kategori, k.warna, SUM(t.jumlah) as total
    FROM transaksi t
    JOIN kategori k ON t.id_kategori = k.id_kategori
    WHERE t.id_pengguna = ? AND k.tipe = 'Pengeluaran'
    GROUP BY k.id_kategori
    ORDER BY total DESC
    LIMIT 6
");
$stmt->execute([$ownerId]);
$categoryBreakdown = $stmt->fetchAll();

$pageTitle = htmlspecialchars($share['title']) . ' - ' . htmlspecialchars($share['owner_name']);

// Include header only (no sidebar for shared view)
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> | MyTabungan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="assets/css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-slate-50 min-h-screen">

<!-- Shared Report Header -->
<div class="bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 text-white py-8">
    <div class="max-w-5xl mx-auto px-4">
        <div class="flex items-center gap-2 text-emerald-100 text-sm mb-2">
            <i class="bi bi-share"></i>
            <span>Laporan Dibagikan</span>
        </div>
        <h1 class="text-2xl font-bold"><?= htmlspecialchars($share['title']) ?></h1>
        <p class="text-emerald-100 mt-1">
            <i class="bi bi-person-circle mr-1"></i>
            Oleh: <?= htmlspecialchars($share['owner_name']) ?>
        </p>
        <p class="text-xs text-emerald-200 mt-2">
            <i class="bi bi-clock mr-1"></i>
            Dibuat: <?= formatTanggal($share['created_at'], 'long') ?>
            <?php if ($share['expires_at']): ?>
            • Kadaluarsa: <?= formatTanggal($share['expires_at'], 'long') ?>
            <?php endif; ?>
        </p>
    </div>
</div>

<!-- Main Content -->
<div class="max-w-5xl mx-auto px-4 py-8 space-y-6">
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="card p-4">
            <p class="text-sm text-slate-500">Total Pemasukan</p>
            <p class="text-xl font-bold text-emerald-600"><?= formatRupiah($totalIncome) ?></p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-slate-500">Total Pengeluaran</p>
            <p class="text-xl font-bold text-red-600"><?= formatRupiah($totalExpense) ?></p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-slate-500">Saldo</p>
            <p class="text-xl font-bold <?= $balance >= 0 ? 'text-sky-600' : 'text-red-600' ?>"><?= formatRupiah($balance) ?></p>
        </div>
        <div class="card p-4">
            <p class="text-sm text-slate-500">Bulan Ini</p>
            <p class="text-xl font-bold text-slate-700"><?= formatRupiah($monthIncome - $monthExpense) ?></p>
        </div>
    </div>
    
    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Monthly Trend -->
        <div class="card p-5">
            <h3 class="font-semibold text-slate-800 mb-4">
                <i class="bi bi-graph-up text-emerald-500 mr-2"></i>
                Tren 6 Bulan Terakhir
            </h3>
            <canvas id="trendChart" height="200"></canvas>
        </div>
        
        <!-- Category Breakdown -->
        <div class="card p-5">
            <h3 class="font-semibold text-slate-800 mb-4">
                <i class="bi bi-pie-chart text-emerald-500 mr-2"></i>
                Pengeluaran per Kategori
            </h3>
            <?php if (empty($categoryBreakdown)): ?>
            <div class="text-center py-8 text-slate-400">
                <i class="bi bi-pie-chart text-3xl"></i>
                <p class="mt-2">Belum ada data</p>
            </div>
            <?php else: ?>
            <canvas id="categoryChart" height="200"></canvas>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Recent Transactions -->
    <div class="card overflow-hidden">
        <div class="p-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-800">
                <i class="bi bi-receipt text-emerald-500 mr-2"></i>
                Transaksi Terbaru
            </h3>
        </div>
        
        <?php if (empty($transactions)): ?>
        <div class="text-center py-12 text-slate-400">
            <i class="bi bi-receipt text-4xl"></i>
            <p class="mt-2">Belum ada transaksi</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th class="hidden md:table-cell">Keterangan</th>
                        <th class="text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $trx): ?>
                    <tr>
                        <td class="text-sm text-slate-600"><?= formatTanggal($trx['tanggal'], 'short') ?></td>
                        <td>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: <?= $trx['warna'] ?>20">
                                    <i class="bi <?= $trx['icon'] ?>" style="color: <?= $trx['warna'] ?>"></i>
                                </div>
                                <span class="text-sm font-medium text-slate-700"><?= htmlspecialchars($trx['nama_kategori']) ?></span>
                            </div>
                        </td>
                        <td class="hidden md:table-cell text-sm text-slate-500"><?= htmlspecialchars($trx['keterangan'] ?: '-') ?></td>
                        <td class="text-right">
                            <span class="font-semibold <?= $trx['tipe'] === 'Pemasukan' ? 'text-emerald-600' : 'text-red-600' ?>">
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
    
    <!-- Footer -->
    <div class="text-center py-6 text-slate-400 text-sm">
        <p>Dibagikan melalui <strong class="text-emerald-600">MyTabungan</strong></p>
        <a href="dashboard.php" class="text-emerald-600 hover:underline mt-2 inline-block">
            <i class="bi bi-arrow-left mr-1"></i>Kembali ke Dashboard
        </a>
    </div>
</div>

<script>
// Monthly Trend Chart
const trendCtx = document.getElementById('trendChart');
if (trendCtx) {
    const trendData = <?= json_encode($monthlyTrend) ?>;
    new Chart(trendCtx, {
        type: 'bar',
        data: {
            labels: trendData.map(d => {
                const [y, m] = d.month.split('-');
                const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                return months[parseInt(m) - 1];
            }),
            datasets: [
                {
                    label: 'Pemasukan',
                    data: trendData.map(d => d.income),
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: '#10b981',
                    borderWidth: 1,
                    borderRadius: 6
                },
                {
                    label: 'Pengeluaran',
                    data: trendData.map(d => d.expense),
                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                    borderColor: '#ef4444',
                    borderWidth: 1,
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
            scales: {
                y: { beginAtZero: true, ticks: { callback: v => 'Rp' + (v/1000000).toFixed(0) + 'jt' } }
            }
        }
    });
}

// Category Chart
<?php if (!empty($categoryBreakdown)): ?>
const catCtx = document.getElementById('categoryChart');
if (catCtx) {
    const catData = <?= json_encode($categoryBreakdown) ?>;
    new Chart(catCtx, {
        type: 'doughnut',
        data: {
            labels: catData.map(d => d.nama_kategori),
            datasets: [{
                data: catData.map(d => d.total),
                backgroundColor: catData.map(d => d.warna),
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } }
        }
    });
}
<?php endif; ?>
</script>

</body>
</html>
