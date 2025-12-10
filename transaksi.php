<?php
/**
 * Transaction Management Page
 * MyTabungan - Personal Finance Management
 */

require_once __DIR__ . '/auth/auth.php';
require_once __DIR__ . '/config/database.php';

// Require login
requireLogin();

$pageTitle = 'Transaksi';
$userId = getCurrentUserId();
$pdo = getConnection();

// =====================================================
// Handle Actions
// =====================================================
$action = $_GET['action'] ?? '';
$editId = $_GET['id'] ?? null;
$editData = null;

// Get transaction for editing
if ($action === 'edit' && $editId) {
    $stmt = $pdo->prepare("SELECT * FROM transaksi WHERE id_transaksi = ? AND id_pengguna = ?");
    $stmt->execute([$editId, $userId]);
    $editData = $stmt->fetch();
    
    if (!$editData) {
        setFlashMessage('error', 'Transaksi tidak ditemukan.');
        redirect('transaksi.php');
    }
}

// Handle DELETE
if ($action === 'delete' && $editId) {
    $stmt = $pdo->prepare("DELETE FROM transaksi WHERE id_transaksi = ? AND id_pengguna = ?");
    $stmt->execute([$editId, $userId]);
    
    if ($stmt->rowCount() > 0) {
        setFlashMessage('success', 'Transaksi berhasil dihapus.');
    } else {
        setFlashMessage('error', 'Gagal menghapus transaksi.');
    }
    redirect('transaksi.php');
}

// Handle POST (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_kategori = $_POST['id_kategori'] ?? '';
    $jumlah = str_replace(['.', ','], '', $_POST['jumlah'] ?? '');
    $tanggal = $_POST['tanggal'] ?? '';
    $keterangan = sanitize($_POST['keterangan'] ?? '');
    $transactionId = $_POST['id_transaksi'] ?? null;
    
    if (empty($id_kategori) || empty($jumlah) || empty($tanggal)) {
        setFlashMessage('error', 'Semua field wajib diisi.');
    } else {
        try {
            if ($transactionId) {
                // Update existing transaction
                $stmt = $pdo->prepare("
                    UPDATE transaksi 
                    SET id_kategori = ?, jumlah = ?, tanggal = ?, keterangan = ?
                    WHERE id_transaksi = ? AND id_pengguna = ?
                ");
                $stmt->execute([$id_kategori, $jumlah, $tanggal, $keterangan, $transactionId, $userId]);
                setFlashMessage('success', 'Transaksi berhasil diperbarui.');
            } else {
                // Insert new transaction
                $stmt = $pdo->prepare("
                    INSERT INTO transaksi (id_pengguna, id_kategori, jumlah, tanggal, keterangan)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$userId, $id_kategori, $jumlah, $tanggal, $keterangan]);
                setFlashMessage('success', 'Transaksi berhasil ditambahkan.');
            }
            redirect('transaksi.php');
        } catch (PDOException $e) {
            error_log("Transaction Error: " . $e->getMessage());
            setFlashMessage('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }
}

// =====================================================
// Get Categories
// =====================================================
$stmt = $pdo->prepare("
    SELECT * FROM kategori 
    WHERE id_pengguna IS NULL OR id_pengguna = ?
    ORDER BY tipe, nama_kategori
");
$stmt->execute([$userId]);
$categories = $stmt->fetchAll();

// Group categories by type
$incomeCategories = array_filter($categories, fn($c) => $c['tipe'] === 'Pemasukan');
$expenseCategories = array_filter($categories, fn($c) => $c['tipe'] === 'Pengeluaran');

// =====================================================
// Get Transactions with Filters
// =====================================================
$filterMonth = $_GET['month'] ?? '';
$filterYear = $_GET['year'] ?? '';
$filterType = $_GET['type'] ?? '';
$filterDateFrom = $_GET['date_from'] ?? '';
$filterDateTo = $_GET['date_to'] ?? '';
$currentPage = (int) ($_GET['page'] ?? 1);
$perPage = 10;

// Check if any filter is active
$hasFilter = $filterMonth || $filterYear || $filterType || $filterDateFrom || $filterDateTo;

// Build base WHERE clause
$whereClause = "WHERE t.id_pengguna = ?";
$params = [$userId];

// Date range filter takes priority
if ($filterDateFrom && $filterDateTo) {
    $whereClause .= " AND t.tanggal BETWEEN ? AND ?";
    $params[] = $filterDateFrom;
    $params[] = $filterDateTo;
} else {
    // Month/Year filter
    if ($filterMonth) {
        $whereClause .= " AND MONTH(t.tanggal) = ?";
        $params[] = $filterMonth;
    }
    if ($filterYear) {
        $whereClause .= " AND YEAR(t.tanggal) = ?";
        $params[] = $filterYear;
    }
}

// Type filter
if ($filterType) {
    $whereClause .= " AND k.tipe = ?";
    $params[] = $filterType;
}

// Count total for pagination
$countSql = "SELECT COUNT(*) as total FROM transaksi t JOIN kategori k ON t.id_kategori = k.id_kategori $whereClause";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$totalItems = $stmt->fetch()['total'];

// Get pagination data
$pagination = getPagination($totalItems, $perPage, $currentPage);

// Get paginated transactions
$sql = "
    SELECT t.*, k.nama_kategori, k.tipe, k.icon, k.warna
    FROM transaksi t 
    JOIN kategori k ON t.id_kategori = k.id_kategori 
    $whereClause
    ORDER BY t.tanggal DESC, t.created_at DESC
    LIMIT {$pagination['limit']} OFFSET {$pagination['offset']}
";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$transactions = $stmt->fetchAll();

// Get all transactions for totals (without pagination)
$totalSql = "
    SELECT t.jumlah, k.tipe
    FROM transaksi t 
    JOIN kategori k ON t.id_kategori = k.id_kategori 
    $whereClause
";
$stmt = $pdo->prepare($totalSql);
$stmt->execute($params);
$allTransactions = $stmt->fetchAll();

// Calculate totals
$totalIncome = 0;
$totalExpense = 0;
foreach ($allTransactions as $trx) {
    if ($trx['tipe'] === 'Pemasukan') {
        $totalIncome += $trx['jumlah'];
    } else {
        $totalExpense += $trx['jumlah'];
    }
}

// Build base URL for pagination
$baseUrlParams = [];
if ($filterMonth) $baseUrlParams[] = 'month=' . $filterMonth;
if ($filterYear) $baseUrlParams[] = 'year=' . $filterYear;
if ($filterType) $baseUrlParams[] = 'type=' . $filterType;
if ($filterDateFrom) $baseUrlParams[] = 'date_from=' . $filterDateFrom;
if ($filterDateTo) $baseUrlParams[] = 'date_to=' . $filterDateTo;
$baseUrl = 'transaksi.php' . ($baseUrlParams ? '?' . implode('&', $baseUrlParams) : '');

// Include layout
include __DIR__ . '/layout/header.php';
include __DIR__ . '/layout/sidebar.php';
?>

<!-- Transaction Page Content -->
<div class="space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 print:hidden">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Transaksi</h2>
            <p class="text-slate-500 mt-1">Kelola semua transaksi keuangan Anda</p>
        </div>
        <div class="flex gap-2">
            <button onclick="printTransactions()" class="btn btn-secondary">
                <i class="bi bi-printer"></i>
                <span class="hidden sm:inline">Cetak</span>
            </button>
            <button onclick="openTransactionModal()" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Transaksi</span>
            </button>
        </div>
    </div>
    
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="card p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl gradient-income flex items-center justify-center">
                <i class="bi bi-arrow-down-circle text-white text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500">Pemasukan</p>
                <p class="text-xl font-bold text-emerald-600"><?= formatRupiah($totalIncome) ?></p>
            </div>
        </div>
        
        <div class="card p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl gradient-expense flex items-center justify-center">
                <i class="bi bi-arrow-up-circle text-white text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500">Pengeluaran</p>
                <p class="text-xl font-bold text-red-600"><?= formatRupiah($totalExpense) ?></p>
            </div>
        </div>
        
        <div class="card p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl gradient-balance flex items-center justify-center">
                <i class="bi bi-wallet2 text-white text-xl"></i>
            </div>
            <div>
                <p class="text-sm text-slate-500">Selisih</p>
                <p class="text-xl font-bold <?= ($totalIncome - $totalExpense) >= 0 ? 'text-sky-600' : 'text-red-600' ?>">
                    <?= formatRupiah($totalIncome - $totalExpense) ?>
                </p>
            </div>
        </div>
    </div>
    <!-- Filters -->
    <div class="card p-5">
        <form id="filterForm" method="GET" class="space-y-5">
            <!-- Quick Filters Row -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                <div>
                    <label class="form-label text-xs text-slate-400 mb-1.5">Bulan</label>
                    <select name="month" class="form-input py-2.5 text-sm" onchange="this.form.submit()">
                        <option value="">Semua Bulan</option>
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= sprintf('%02d', $m) ?>" <?= $filterMonth == sprintf('%02d', $m) ? 'selected' : '' ?>>
                            <?= getMonthName($m) ?>
                        </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div>
                    <label class="form-label text-xs text-slate-400 mb-1.5">Tahun</label>
                    <select name="year" class="form-input py-2.5 text-sm" onchange="this.form.submit()">
                        <option value="">Semua Tahun</option>
                        <?php for ($y = date('Y'); $y >= date('Y') - 5; $y--): ?>
                        <option value="<?= $y ?>" <?= $filterYear == $y ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div>
                    <label class="form-label text-xs text-slate-400 mb-1.5">Tipe</label>
                    <select name="type" class="form-input py-2.5 text-sm" onchange="this.form.submit()">
                        <option value="">Semua Tipe</option>
                        <option value="Pemasukan" <?= $filterType === 'Pemasukan' ? 'selected' : '' ?>>Pemasukan</option>
                        <option value="Pengeluaran" <?= $filterType === 'Pengeluaran' ? 'selected' : '' ?>>Pengeluaran</option>
                    </select>
                </div>
                
                <div>
                    <label class="form-label text-xs text-slate-400 mb-1.5">Dari Tanggal</label>
                    <input type="date" name="date_from" class="form-input py-2.5 text-sm" 
                           value="<?= $filterDateFrom ?>" onchange="this.form.submit()">
                </div>
                
                <div>
                    <label class="form-label text-xs text-slate-400 mb-1.5">Sampai Tanggal</label>
                    <input type="date" name="date_to" class="form-input py-2.5 text-sm" 
                           value="<?= $filterDateTo ?>" onchange="this.form.submit()">
                </div>
                
                <div class="flex items-end">
                    <?php if ($hasFilter): ?>
                    <a href="transaksi.php" class="btn btn-secondary py-2.5 w-full justify-center">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>Reset</span>
                    </a>
                    <?php else: ?>
                    <div class="text-xs text-slate-300 text-center w-full py-2.5">
                        Pilih filter
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Active Filters Info -->
            <?php if ($hasFilter): ?>
            <div class="flex flex-wrap items-center gap-2 pt-4 border-t border-slate-100">
                <span class="text-xs text-slate-400"><i class="bi bi-funnel-fill mr-1"></i>Aktif:</span>
                <?php if ($filterDateFrom && $filterDateTo): ?>
                    <span class="badge bg-purple-100 text-purple-700 text-xs px-2.5 py-1">
                        <i class="bi bi-calendar-range mr-1"></i>
                        <?= formatTanggal($filterDateFrom, 'short') ?> - <?= formatTanggal($filterDateTo, 'short') ?>
                    </span>
                <?php else: ?>
                    <?php if ($filterMonth): ?>
                        <span class="badge bg-sky-100 text-sky-700 text-xs px-2.5 py-1"><?= getMonthName((int)$filterMonth) ?></span>
                    <?php endif; ?>
                    <?php if ($filterYear): ?>
                        <span class="badge bg-sky-100 text-sky-700 text-xs px-2.5 py-1"><?= $filterYear ?></span>
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ($filterType): ?>
                    <span class="badge <?= $filterType === 'Pemasukan' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' ?> text-xs px-2.5 py-1">
                        <i class="bi <?= $filterType === 'Pemasukan' ? 'bi-arrow-down-circle' : 'bi-arrow-up-circle' ?> mr-1"></i>
                        <?= $filterType ?>
                    </span>
                <?php endif; ?>
                <span class="text-xs text-slate-500 ml-auto font-medium"><?= count($transactions) ?> transaksi ditemukan</span>
            </div>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- Transaction List -->
    <div class="card overflow-hidden">
        <?php if (empty($transactions)): ?>
        <div class="empty-state py-16">
            <i class="bi bi-receipt"></i>
            <p class="font-medium text-slate-600 mb-1">Tidak ada transaksi</p>
            <p class="text-sm mb-4">Belum ada transaksi pada periode ini</p>
            <button onclick="openTransactionModal()" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>
                Tambah Transaksi
            </button>
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
                        <th class="text-center w-24">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transactions as $trx): ?>
                    <tr>
                        <td>
                            <div class="text-sm">
                                <p class="font-medium text-slate-700"><?= formatTanggal($trx['tanggal'], 'short') ?></p>
                            </div>
                        </td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: <?= $trx['warna'] ?>20">
                                    <i class="bi <?= $trx['icon'] ?> text-lg" style="color: <?= $trx['warna'] ?>"></i>
                                </div>
                                <div>
                                    <p class="font-medium text-slate-700"><?= htmlspecialchars($trx['nama_kategori']) ?></p>
                                    <p class="text-xs text-slate-400"><?= $trx['tipe'] ?></p>
                                </div>
                            </div>
                        </td>
                        <td class="hidden md:table-cell">
                            <span class="text-sm text-slate-500"><?= htmlspecialchars($trx['keterangan'] ?: '-') ?></span>
                        </td>
                        <td class="text-right">
                            <span class="font-semibold <?= $trx['tipe'] === 'Pemasukan' ? 'text-emerald-600' : 'text-red-600' ?>">
                                <?= $trx['tipe'] === 'Pemasukan' ? '+' : '-' ?><?= formatRupiah($trx['jumlah']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="flex items-center justify-center gap-1">
                                <button onclick="editTransaction(<?= htmlspecialchars(json_encode($trx)) ?>)" 
                                        class="p-2 text-slate-400 hover:text-sky-500 hover:bg-sky-50 rounded-lg transition-colors"
                                        title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <a href="transaksi.php?action=delete&id=<?= $trx['id_transaksi'] ?>" 
                                   class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                                   data-delete-confirm="Yakin ingin menghapus transaksi ini?"
                                   title="Hapus">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <?= renderPagination($pagination, $baseUrl) ?>
        
        <?php endif; ?>
    </div>
</div>

<!-- Transaction Modal -->
<div id="transactionModal" class="modal-overlay">
    <div class="modal-content p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 id="modalTitle" class="text-xl font-bold text-slate-800">Tambah Transaksi</h3>
            <button data-modal-close class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <i class="bi bi-x-lg text-slate-400"></i>
            </button>
        </div>
        
        <form method="POST" id="transactionForm" class="space-y-4">
            <input type="hidden" name="id_transaksi" id="id_transaksi">
            
            <!-- Transaction Type Tabs -->
            <div class="flex gap-2 p-1 bg-slate-100 rounded-xl">
                <button type="button" onclick="setTransactionType('Pemasukan')" 
                        id="typeIncome" class="flex-1 py-2 px-4 rounded-lg text-sm font-medium transition-all">
                    <i class="bi bi-arrow-down-circle mr-1"></i>
                    Pemasukan
                </button>
                <button type="button" onclick="setTransactionType('Pengeluaran')" 
                        id="typeExpense" class="flex-1 py-2 px-4 rounded-lg text-sm font-medium transition-all">
                    <i class="bi bi-arrow-up-circle mr-1"></i>
                    Pengeluaran
                </button>
            </div>
            
            <!-- Category -->
            <div>
                <label class="form-label">Kategori</label>
                <select name="id_kategori" id="id_kategori" required class="form-input">
                    <option value="">Pilih Kategori</option>
                </select>
            </div>
            
            <!-- Amount -->
            <div>
                <label class="form-label">Jumlah</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">Rp</span>
                    <input type="text" name="jumlah" id="jumlah" required 
                           class="form-input pl-12" placeholder="0"
                           oninput="formatCurrencyInput(this)">
                </div>
            </div>
            
            <!-- Date -->
            <div>
                <label class="form-label">Tanggal</label>
                <input type="date" name="tanggal" id="tanggal" required 
                       class="form-input" value="<?= date('Y-m-d') ?>">
            </div>
            
            <!-- Description -->
            <div>
                <label class="form-label">Keterangan (Opsional)</label>
                <textarea name="keterangan" id="keterangan" rows="2" 
                          class="form-input resize-none" placeholder="Tambahkan catatan..."></textarea>
            </div>
            
            <!-- Actions -->
            <div class="flex gap-3 pt-4">
                <button type="button" data-modal-close class="btn btn-secondary flex-1">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary flex-1">
                    <i class="bi bi-check-lg"></i>
                    Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Category data
const categories = <?= json_encode($categories) ?>;
const incomeCategories = categories.filter(c => c.tipe === 'Pemasukan');
const expenseCategories = categories.filter(c => c.tipe === 'Pengeluaran');

let currentType = 'Pengeluaran';

function setTransactionType(type) {
    currentType = type;
    const categorySelect = document.getElementById('id_kategori');
    const typeIncome = document.getElementById('typeIncome');
    const typeExpense = document.getElementById('typeExpense');
    
    // Update tabs
    if (type === 'Pemasukan') {
        typeIncome.className = 'flex-1 py-2 px-4 rounded-lg text-sm font-medium transition-all bg-emerald-500 text-white';
        typeExpense.className = 'flex-1 py-2 px-4 rounded-lg text-sm font-medium transition-all text-slate-500';
    } else {
        typeExpense.className = 'flex-1 py-2 px-4 rounded-lg text-sm font-medium transition-all bg-red-500 text-white';
        typeIncome.className = 'flex-1 py-2 px-4 rounded-lg text-sm font-medium transition-all text-slate-500';
    }
    
    // Update categories
    const cats = type === 'Pemasukan' ? incomeCategories : expenseCategories;
    categorySelect.innerHTML = '<option value="">Pilih Kategori</option>';
    cats.forEach(cat => {
        const option = document.createElement('option');
        option.value = cat.id_kategori;
        option.textContent = cat.nama_kategori;
        categorySelect.appendChild(option);
    });
}

function openTransactionModal(data = null) {
    const modal = document.getElementById('transactionModal');
    const title = document.getElementById('modalTitle');
    const form = document.getElementById('transactionForm');
    
    // Reset form
    form.reset();
    document.getElementById('id_transaksi').value = '';
    document.getElementById('tanggal').value = '<?= date('Y-m-d') ?>';
    
    if (data) {
        title.textContent = 'Edit Transaksi';
        document.getElementById('id_transaksi').value = data.id_transaksi;
        document.getElementById('jumlah').value = parseInt(data.jumlah).toLocaleString('id-ID');
        document.getElementById('tanggal').value = data.tanggal;
        document.getElementById('keterangan').value = data.keterangan || '';
        
        // Set type and category
        setTransactionType(data.tipe);
        setTimeout(() => {
            document.getElementById('id_kategori').value = data.id_kategori;
        }, 100);
    } else {
        title.textContent = 'Tambah Transaksi';
        setTransactionType('Pengeluaran');
    }
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function editTransaction(data) {
    openTransactionModal(data);
}

function formatCurrencyInput(input) {
    let value = input.value.replace(/\D/g, '');
    if (value) {
        value = parseInt(value).toLocaleString('id-ID');
    }
    input.value = value;
}

// Close modal handlers
document.querySelectorAll('[data-modal-close]').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('transactionModal').classList.remove('active');
        document.body.style.overflow = '';
    });
});

// Initialize with expense type
setTransactionType('Pengeluaran');

// Print transactions function
function printTransactions() {
    // Get current filter info
    const transactions = <?= json_encode(array_map(function($t) {
        return [
            'tanggal' => formatTanggal($t['tanggal'], 'short'),
            'kategori' => $t['nama_kategori'],
            'tipe' => $t['tipe'],
            'keterangan' => $t['keterangan'] ?: '-',
            'jumlah' => formatRupiah($t['jumlah'])
        ];
    }, $transactions)) ?>;
    
    const totalIncome = '<?= formatRupiah($totalIncome) ?>';
    const totalExpense = '<?= formatRupiah($totalExpense) ?>';
    const balance = '<?= formatRupiah($totalIncome - $totalExpense) ?>';
    const filterInfo = '<?= $hasFilter ? ($filterDateFrom && $filterDateTo ? formatTanggal($filterDateFrom, "short") . " - " . formatTanggal($filterDateTo, "short") : ($filterMonth ? getMonthName((int)$filterMonth) : "") . " " . $filterYear) : "Semua Transaksi" ?>';
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Laporan Transaksi - MyTabungan</title>
            <style>
                * { margin: 0; padding: 0; box-sizing: border-box; }
                body { font-family: 'Segoe UI', Tahoma, sans-serif; padding: 40px; color: #333; }
                .header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #10b981; padding-bottom: 20px; }
                .header h1 { color: #10b981; font-size: 24px; margin-bottom: 5px; }
                .header p { color: #666; font-size: 14px; }
                .summary { display: flex; justify-content: space-around; margin-bottom: 30px; background: #f8fafc; padding: 20px; border-radius: 8px; }
                .summary-item { text-align: center; }
                .summary-item .label { font-size: 12px; color: #64748b; margin-bottom: 5px; }
                .summary-item .value { font-size: 18px; font-weight: bold; }
                .income { color: #10b981; }
                .expense { color: #ef4444; }
                .balance { color: #0ea5e9; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th { background: #10b981; color: white; padding: 12px 8px; text-align: left; font-size: 12px; }
                td { padding: 10px 8px; border-bottom: 1px solid #e2e8f0; font-size: 12px; }
                tr:nth-child(even) { background: #f8fafc; }
                .text-right { text-align: right; }
                .footer { margin-top: 30px; text-align: center; font-size: 11px; color: #94a3b8; }
                @media print { body { padding: 20px; } }
            </style>
        </head>
        <body>
            <div class="header">
                <h1>📊 Laporan Transaksi</h1>
                <p>MyTabungan - ${filterInfo}</p>
                <p style="font-size: 11px; margin-top: 5px;">Dicetak: ${new Date().toLocaleDateString('id-ID', { dateStyle: 'long' })}</p>
            </div>
            
            <div class="summary">
                <div class="summary-item">
                    <div class="label">Total Pemasukan</div>
                    <div class="value income">${totalIncome}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Total Pengeluaran</div>
                    <div class="value expense">${totalExpense}</div>
                </div>
                <div class="summary-item">
                    <div class="label">Selisih</div>
                    <div class="value balance">${balance}</div>
                </div>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Kategori</th>
                        <th>Tipe</th>
                        <th>Keterangan</th>
                        <th class="text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    ${transactions.map(t => `
                        <tr>
                            <td>${t.tanggal}</td>
                            <td>${t.kategori}</td>
                            <td>${t.tipe}</td>
                            <td>${t.keterangan}</td>
                            <td class="text-right ${t.tipe === 'Pemasukan' ? 'income' : 'expense'}">${t.tipe === 'Pemasukan' ? '+' : '-'}${t.jumlah}</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
            
            <div class="footer">
                <p>Total ${transactions.length} transaksi | Generated by MyTabungan</p>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    setTimeout(() => printWindow.print(), 250);
}

// Open modal if action=add
<?php if ($action === 'add'): ?>
openTransactionModal();
<?php elseif ($action === 'edit' && $editData): ?>
openTransactionModal(<?= json_encode($editData) ?>);
<?php endif; ?>
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>
