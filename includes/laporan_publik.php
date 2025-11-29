<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/functions.php';

// Ambil token dari URL
$token = $_GET['token'] ?? '';

if (empty($token)) {
    http_response_code(400);
    die("Token tidak ditemukan.");
}

$id_pengguna = null;
$nama_pengguna = 'Pengguna';

try {
    // Cari id_pengguna berdasarkan token
    $sql_token = "SELECT id_pengguna FROM share_links WHERE token = ?";
    $stmt_token = $pdo->prepare($sql_token);
    $stmt_token->execute([$token]);
    $result = $stmt_token->fetch();

    if (!$result) {
        http_response_code(404);
        die("Link tidak valid atau sudah tidak berlaku.");
    }
    $id_pengguna = $result['id_pengguna'];

    // Ambil nama pengguna
    $sql_user = "SELECT nama_lengkap FROM pengguna WHERE id_pengguna = ?";
    $stmt_user = $pdo->prepare($sql_user);
    $stmt_user->execute([$id_pengguna]);
    $nama_pengguna = $stmt_user->fetch()['nama_lengkap'] ?? 'Pengguna';

    // Ambil data keuangan
    $sql_pemasukan = "SELECT SUM(jumlah) AS total FROM transaksi t JOIN kategori k ON t.id_kategori = k.id_kategori WHERE t.id_pengguna = ? AND k.tipe = 'Pemasukan'";
    $stmt_pemasukan = $pdo->prepare($sql_pemasukan);
    $stmt_pemasukan->execute([$id_pengguna]);
    $total_pemasukan = $stmt_pemasukan->fetch()['total'] ?? 0;

    $sql_pengeluaran = "SELECT SUM(jumlah) AS total FROM transaksi t JOIN kategori k ON t.id_kategori = k.id_kategori WHERE t.id_pengguna = ? AND k.tipe = 'Pengeluaran'";
    $stmt_pengeluaran = $pdo->prepare($sql_pengeluaran);
    $stmt_pengeluaran->execute([$id_pengguna]);
    $total_pengeluaran = $stmt_pengeluaran->fetch()['total'] ?? 0;

    $saldo_akhir = $total_pemasukan - $total_pengeluaran;

    // Pagination Logic
    $limit = 10;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $page = max(1, $page); // Ensure page is at least 1
    $offset = ($page - 1) * $limit;

    // Hitung total transaksi
    $sql_count = "SELECT COUNT(*) FROM transaksi WHERE id_pengguna = ?";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute([$id_pengguna]);
    $total_items = $stmt_count->fetchColumn();
    $total_pages = ceil($total_items / $limit);

    // Ambil transaksi dengan pagination
    $sql_recent = "SELECT t.tanggal_transaksi, t.keterangan, k.nama_kategori, k.tipe, t.jumlah 
                   FROM transaksi t
                   JOIN kategori k ON t.id_kategori = k.id_kategori 
                   WHERE t.id_pengguna = ? 
                   ORDER BY t.tanggal_transaksi DESC, t.id_transaksi DESC
                   LIMIT ? OFFSET ?";
    $stmt_recent = $pdo->prepare($sql_recent);
    // Bind parameters manually for LIMIT and OFFSET as they need to be integers
    $stmt_recent->bindValue(1, $id_pengguna, PDO::PARAM_INT);
    $stmt_recent->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt_recent->bindValue(3, $offset, PDO::PARAM_INT);
    $stmt_recent->execute();
    $recent_transactions = $stmt_recent->fetchAll();

    // Ambil data chart
    require_once __DIR__ . '/chart_data.php';

} catch (PDOException $e) {
    // Jangan tampilkan error detail di production
    http_response_code(500);
    die("Terjadi kesalahan pada server.");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - <?php echo htmlspecialchars($nama_pengguna); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/output.css">
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-slate-50 font-sans text-slate-800 antialiased min-h-screen py-10 px-4">
    
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <header class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-2xl shadow-lg shadow-emerald-200 mb-4 text-white">
                <i class="bi bi-wallet2 text-3xl"></i>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Laporan Keuangan</h1>
            <div class="flex items-center justify-center gap-3 text-sm text-gray-500">
                <span class="flex items-center gap-1">
                    <i class="bi bi-person-circle text-emerald-600"></i>
                    Milik: <strong class="text-gray-900"><?php echo htmlspecialchars($nama_pengguna); ?></strong>
                </span>
                <span class="text-gray-300">|</span>
                <span class="flex items-center gap-1">
                    <i class="bi bi-calendar-check text-emerald-600"></i>
                    <?php echo date('d M Y'); ?>
                </span>
            </div>
        </header>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10">
            <!-- Pemasukan -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-emerald-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total Pemasukan</p>
                <h3 class="text-2xl font-bold text-emerald-600"><?php echo formatRupiah($total_pemasukan); ?></h3>
                <div class="mt-4 flex items-center text-xs font-medium text-emerald-600 bg-emerald-50 w-fit px-2 py-1 rounded-lg">
                    <i class="bi bi-arrow-down-left mr-1"></i> Pemasukan
                </div>
            </div>

            <!-- Pengeluaran -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-red-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                <p class="text-sm font-medium text-gray-500 mb-1">Total Pengeluaran</p>
                <h3 class="text-2xl font-bold text-red-600"><?php echo formatRupiah($total_pengeluaran); ?></h3>
                <div class="mt-4 flex items-center text-xs font-medium text-red-600 bg-red-50 w-fit px-2 py-1 rounded-lg">
                    <i class="bi bi-arrow-up-right mr-1"></i> Pengeluaran
                </div>
            </div>

            <!-- Saldo -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 relative overflow-hidden group hover:shadow-md transition-all">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-blue-50 rounded-full opacity-50 group-hover:scale-110 transition-transform"></div>
                <p class="text-sm font-medium text-gray-500 mb-1">Saldo Akhir</p>
                <h3 class="text-2xl font-bold <?php echo $saldo_akhir >= 0 ? 'text-blue-600' : 'text-orange-600'; ?>">
                    <?php echo formatRupiah($saldo_akhir); ?>
                </h3>
                <div class="mt-4 flex items-center text-xs font-medium text-blue-600 bg-blue-50 w-fit px-2 py-1 rounded-lg">
                    <i class="bi bi-wallet2 mr-1"></i> Cashflow
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-10">
            <div class="flex justify-between items-center mb-6">
                <h5 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                    <span class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <i class="bi bi-graph-up"></i>
                    </span>
                    Analisis Keuangan (Bulan Ini)
                </h5>
            </div>
            <div class="h-72 w-full">
                <canvas id="publicChart"></canvas>
            </div>
        </div>

        <!-- Transaction List -->
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-10">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 flex items-center justify-center text-emerald-600">
                        <i class="bi bi-clock-history"></i>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">Riwayat Transaksi</h2>
                </div>
                <div class="text-sm text-gray-500">
                    Halaman <?php echo $page; ?> dari <?php echo $total_pages; ?>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        <?php if (empty($recent_transactions)): ?>
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="bi bi-inbox text-3xl text-gray-400"></i>
                                        </div>
                                        <p class="text-lg font-medium text-gray-900">Belum ada data transaksi</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_transactions as $trx): ?>
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col">
                                            <span class="text-sm font-bold text-gray-900"><?php echo date('d', strtotime($trx['tanggal_transaksi'])); ?></span>
                                            <span class="text-xs text-gray-500"><?php echo date('M Y', strtotime($trx['tanggal_transaksi'])); ?></span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                        <?php echo htmlspecialchars($trx['keterangan']); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo ($trx['tipe'] == 'Pemasukan') ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo htmlspecialchars($trx['nama_kategori']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold <?php echo ($trx['tipe'] == 'Pemasukan') ? 'text-emerald-600' : 'text-red-600'; ?>">
                                        <?php echo ($trx['tipe'] == 'Pemasukan' ? '+' : '-') . formatRupiah($trx['jumlah']); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="px-6 py-4 border-t border-gray-100 flex items-center justify-between bg-gray-50/30">
                <div class="flex-1 flex justify-between sm:hidden">
                    <?php if ($page > 1): ?>
                        <a href="?token=<?php echo urlencode($token); ?>&page=<?php echo $page - 1; ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Previous
                        </a>
                    <?php else: ?>
                        <span class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-gray-50 cursor-not-allowed">
                            Previous
                        </span>
                    <?php endif; ?>

                    <?php if ($page < $total_pages): ?>
                        <a href="?token=<?php echo urlencode($token); ?>&page=<?php echo $page + 1; ?>" class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Next
                        </a>
                    <?php else: ?>
                        <span class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-300 bg-gray-50 cursor-not-allowed">
                            Next
                        </span>
                    <?php endif; ?>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Menampilkan
                            <span class="font-medium"><?php echo $offset + 1; ?></span>
                            sampai
                            <span class="font-medium"><?php echo min($offset + $limit, $total_items); ?></span>
                            dari
                            <span class="font-medium"><?php echo $total_items; ?></span>
                            data
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <!-- Previous Page -->
                            <?php if ($page > 1): ?>
                                <a href="?token=<?php echo urlencode($token); ?>&page=<?php echo $page - 1; ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Previous</span>
                                    <i class="bi bi-chevron-left"></i>
                                </a>
                            <?php else: ?>
                                <span class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-gray-50 text-sm font-medium text-gray-300 cursor-not-allowed">
                                    <span class="sr-only">Previous</span>
                                    <i class="bi bi-chevron-left"></i>
                                </span>
                            <?php endif; ?>

                            <!-- Page Numbers -->
                            <?php
                            $start_page = max(1, $page - 2);
                            $end_page = min($total_pages, $page + 2);
                            
                            if ($start_page > 1) {
                                echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
                            }

                            for ($i = $start_page; $i <= $end_page; $i++):
                                $activeClass = ($i == $page) ? 'z-10 bg-emerald-50 border-emerald-500 text-emerald-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50';
                            ?>
                                <a href="?token=<?php echo urlencode($token); ?>&page=<?php echo $i; ?>" aria-current="page" class="relative inline-flex items-center px-4 py-2 border text-sm font-medium <?php echo $activeClass; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>

                            <?php
                            if ($end_page < $total_pages) {
                                echo '<span class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium text-gray-700">...</span>';
                            }
                            ?>

                            <!-- Next Page -->
                            <?php if ($page < $total_pages): ?>
                                <a href="?token=<?php echo urlencode($token); ?>&page=<?php echo $page + 1; ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50">
                                    <span class="sr-only">Next</span>
                                    <i class="bi bi-chevron-right"></i>
                                </a>
                            <?php else: ?>
                                <span class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-gray-50 text-sm font-medium text-gray-300 cursor-not-allowed">
                                    <span class="sr-only">Next</span>
                                    <i class="bi bi-chevron-right"></i>
                                </span>
                            <?php endif; ?>
                        </nav>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- CTA Footer -->
        <div class="bg-gradient-to-br from-gray-900 to-gray-800 rounded-2xl p-8 text-center text-white shadow-xl">
            <h3 class="text-2xl font-bold mb-2">Kelola Keuanganmu Sendiri</h3>
            <p class="text-gray-300 mb-6 max-w-lg mx-auto">Bergabunglah dengan MyTabungan dan capai kebebasan finansialmu sekarang. Gratis dan mudah digunakan.</p>
            <a href="../index.php" class="inline-flex items-center px-6 py-3 bg-emerald-500 text-white font-bold rounded-xl hover:bg-emerald-600 transition-all shadow-lg hover:shadow-emerald-500/30 transform hover:-translate-y-1">
                Mulai Gratis Sekarang <i class="bi bi-arrow-right ml-2"></i>
            </a>
            <div class="mt-8 pt-8 border-t border-gray-700 text-sm text-gray-400">
                <p>&copy; <?php echo date('Y'); ?> MyTabungan. All rights reserved.</p>
                <p class="text-xs mt-1">Generated securely by MyTabungan System</p>
            </div>
        </div>

    </div>

    <script>
        const ctx = document.getElementById('publicChart');
        const labels = <?php echo json_encode($labels); ?>;
        const pemasukanData = <?php echo json_encode($pemasukan_data); ?>;
        const pengeluaranData = <?php echo json_encode($pengeluaran_data); ?>;

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: pemasukanData,
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    },
                    {
                        label: 'Pengeluaran',
                        data: pengeluaranData,
                        borderColor: '#ef4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'Rp ' + value.toLocaleString('id-ID');
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                                return label;
                            }
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    </script>
</body>
</html>
