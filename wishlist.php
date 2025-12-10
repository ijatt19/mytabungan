<?php
/**
 * Wishlist / Savings Goals Page
 * MyTabungan - Personal Finance Management
 * 
 * Wishlist savings are linked to transactions:
 * - Adding savings creates an expense transaction
 * - Marking complete requires sufficient balance
 */

require_once __DIR__ . '/auth/auth.php';
require_once __DIR__ . '/config/database.php';

// Require login
requireLogin();

$pageTitle = 'Wishlist';
$userId = getCurrentUserId();
$pdo = getConnection();

// =====================================================
// Helper: Get or Create Wishlist Savings Category
// =====================================================
function getWishlistCategoryId($pdo, $userId) {
    // Check if user has a "Tabungan Wishlist" category (Pemasukan type)
    $stmt = $pdo->prepare("SELECT id_kategori FROM kategori WHERE nama_kategori = 'Tabungan Wishlist' AND tipe = 'Pemasukan' AND (id_pengguna = ? OR id_pengguna IS NULL) LIMIT 1");
    $stmt->execute([$userId]);
    $cat = $stmt->fetch();
    
    if ($cat) {
        return $cat['id_kategori'];
    }
    
    // Create the category for this user (as Pemasukan/Income)
    $stmt = $pdo->prepare("INSERT INTO kategori (id_pengguna, nama_kategori, tipe, icon, warna) VALUES (?, 'Tabungan Wishlist', 'Pemasukan', 'bi-piggy-bank', '#8b5cf6')");
    $stmt->execute([$userId]);
    return $pdo->lastInsertId();
}

// =====================================================
// Helper: Get Current Balance
// =====================================================
function getCurrentBalance($pdo, $userId) {
    $stmt = $pdo->prepare("
        SELECT 
            COALESCE(SUM(CASE WHEN k.tipe = 'Pemasukan' THEN t.jumlah ELSE 0 END), 0) -
            COALESCE(SUM(CASE WHEN k.tipe = 'Pengeluaran' THEN t.jumlah ELSE 0 END), 0) as balance
        FROM transaksi t 
        JOIN kategori k ON t.id_kategori = k.id_kategori 
        WHERE t.id_pengguna = ?
    ");
    $stmt->execute([$userId]);
    return (float) $stmt->fetch()['balance'];
}

// =====================================================
// Handle Actions
// =====================================================
$action = $_GET['action'] ?? '';
$editId = $_GET['id'] ?? null;

// Handle DELETE
if ($action === 'delete' && $editId) {
    $stmt = $pdo->prepare("DELETE FROM wishlist WHERE id_wishlist = ? AND id_pengguna = ?");
    $stmt->execute([$editId, $userId]);
    
    if ($stmt->rowCount() > 0) {
        setFlashMessage('success', 'Item wishlist berhasil dihapus.');
    } else {
        setFlashMessage('error', 'Gagal menghapus item wishlist.');
    }
    redirect('wishlist.php');
}

// Handle Mark as Complete
if ($action === 'complete' && $editId) {
    // Get wishlist item
    $stmt = $pdo->prepare("SELECT * FROM wishlist WHERE id_wishlist = ? AND id_pengguna = ?");
    $stmt->execute([$editId, $userId]);
    $item = $stmt->fetch();
    
    if (!$item) {
        setFlashMessage('error', 'Wishlist tidak ditemukan.');
        redirect('wishlist.php');
    }
    
    // Calculate remaining amount needed
    $remaining = $item['harga'] - $item['tabungan_terkumpul'];
    
    if ($remaining > 0) {
        // Create INCOME transaction for the remaining amount (like getting final funds)
        $categoryId = getWishlistCategoryId($pdo, $userId);
        $stmt = $pdo->prepare("INSERT INTO transaksi (id_pengguna, id_kategori, jumlah, tanggal, keterangan) VALUES (?, ?, ?, CURDATE(), ?)");
        $stmt->execute([$userId, $categoryId, $remaining, 'Pelunasan: ' . $item['nama_barang']]);
    }
    
    // Mark as complete
    $stmt = $pdo->prepare("UPDATE wishlist SET status = 'Tercapai', tabungan_terkumpul = harga WHERE id_wishlist = ?");
    $stmt->execute([$editId]);
    
    setFlashMessage('success', 'Selamat! "' . $item['nama_barang'] . '" telah tercapai! 🎉');
    redirect('wishlist.php');
}

// Handle POST (Add/Edit/Add Savings)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formType = $_POST['form_type'] ?? '';
    
    if ($formType === 'add_savings') {
        // Add savings to existing wishlist (as income)
        $wishlistId = $_POST['id_wishlist'] ?? '';
        $amount = (float) str_replace(['.', ','], '', $_POST['amount'] ?? '');
        
        if ($wishlistId && $amount > 0) {
            // Get wishlist item to calculate max amount
            $stmt = $pdo->prepare("SELECT * FROM wishlist WHERE id_wishlist = ? AND id_pengguna = ?");
            $stmt->execute([$wishlistId, $userId]);
            $item = $stmt->fetch();
            
            if (!$item) {
                setFlashMessage('error', 'Wishlist tidak ditemukan.');
                redirect('wishlist.php');
            }
            
            // Limit amount to what's needed
            $remaining = $item['harga'] - $item['tabungan_terkumpul'];
            $actualAmount = min($amount, $remaining);
            
            if ($actualAmount <= 0) {
                setFlashMessage('info', 'Target sudah tercapai!');
                redirect('wishlist.php');
            }
            
            // Create INCOME transaction (adds to balance)
            $categoryId = getWishlistCategoryId($pdo, $userId);
            $stmt = $pdo->prepare("INSERT INTO transaksi (id_pengguna, id_kategori, jumlah, tanggal, keterangan) VALUES (?, ?, ?, CURDATE(), ?)");
            $stmt->execute([$userId, $categoryId, $actualAmount, 'Tabungan: ' . $item['nama_barang']]);
            
            // Update wishlist savings
            $stmt = $pdo->prepare("UPDATE wishlist SET tabungan_terkumpul = tabungan_terkumpul + ? WHERE id_wishlist = ?");
            $stmt->execute([$actualAmount, $wishlistId]);
            
            // Check if goal reached
            $newTotal = $item['tabungan_terkumpul'] + $actualAmount;
            if ($newTotal >= $item['harga']) {
                $stmt = $pdo->prepare("UPDATE wishlist SET status = 'Tercapai' WHERE id_wishlist = ?");
                $stmt->execute([$wishlistId]);
                setFlashMessage('success', 'Selamat! Target tabungan untuk "' . $item['nama_barang'] . '" telah tercapai! 🎉');
            } else {
                setFlashMessage('success', 'Tabungan ' . formatRupiah($actualAmount) . ' berhasil ditambahkan.');
            }
        }
        redirect('wishlist.php');
        
    } else {
        // Add/Edit wishlist item
        $nama_barang = sanitize($_POST['nama_barang'] ?? '');
        $harga = str_replace(['.', ','], '', $_POST['harga'] ?? '');
        $prioritas = $_POST['prioritas'] ?? 'Sedang';
        $target_date = $_POST['target_date'] ?? null;
        $catatan = sanitize($_POST['catatan'] ?? '');
        $wishlistId = $_POST['id_wishlist'] ?? null;
        
        if (empty($nama_barang) || empty($harga)) {
            setFlashMessage('error', 'Nama barang dan harga target harus diisi.');
        } else {
            try {
                if ($wishlistId) {
                    // Update existing
                    $stmt = $pdo->prepare("
                        UPDATE wishlist 
                        SET nama_barang = ?, harga = ?, prioritas = ?, target_date = ?, catatan = ?
                        WHERE id_wishlist = ? AND id_pengguna = ?
                    ");
                    $stmt->execute([$nama_barang, $harga, $prioritas, $target_date ?: null, $catatan, $wishlistId, $userId]);
                    setFlashMessage('success', 'Wishlist berhasil diperbarui.');
                } else {
                    // Insert new
                    $stmt = $pdo->prepare("
                        INSERT INTO wishlist (id_pengguna, nama_barang, harga, prioritas, target_date, catatan)
                        VALUES (?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->execute([$userId, $nama_barang, $harga, $prioritas, $target_date ?: null, $catatan]);
                    setFlashMessage('success', 'Wishlist berhasil ditambahkan.');
                }
                redirect('wishlist.php');
            } catch (PDOException $e) {
                error_log("Wishlist Error: " . $e->getMessage());
                setFlashMessage('error', 'Terjadi kesalahan. Silakan coba lagi.');
            }
        }
    }
}


// =====================================================
// Get Wishlist Items
// =====================================================
$filterStatus = $_GET['status'] ?? '';
$currentPage = (int) ($_GET['page'] ?? 1);
$perPage = 6;

// Build WHERE clause
$whereClause = "WHERE id_pengguna = ?";
$params = [$userId];

if ($filterStatus) {
    $whereClause .= " AND status = ?";
    $params[] = $filterStatus;
}

// Count total for pagination
$countSql = "SELECT COUNT(*) as total FROM wishlist $whereClause";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$totalCount = $stmt->fetch()['total'];

// Get pagination data
$pagination = getPagination($totalCount, $perPage, $currentPage);

// Get all items for stats (without pagination)
$statsSql = "SELECT status, tabungan_terkumpul, harga FROM wishlist WHERE id_pengguna = ?";
$stmt = $pdo->prepare($statsSql);
$stmt->execute([$userId]);
$allItems = $stmt->fetchAll();

// Stats
$totalItems = count($allItems);
$completedItems = count(array_filter($allItems, fn($w) => $w['status'] === 'Tercapai'));
$totalSaved = array_sum(array_column($allItems, 'tabungan_terkumpul'));
$totalTarget = array_sum(array_column($allItems, 'harga'));

// Get paginated wishlist items
$sql = "SELECT * FROM wishlist $whereClause 
        ORDER BY FIELD(prioritas, 'Tinggi', 'Sedang', 'Rendah'), created_at DESC
        LIMIT {$pagination['limit']} OFFSET {$pagination['offset']}";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$wishlistItems = $stmt->fetchAll();

// Build base URL for pagination
$baseUrl = 'wishlist.php' . ($filterStatus ? '?status=' . $filterStatus : '');

// Include layout
include __DIR__ . '/layout/header.php';
include __DIR__ . '/layout/sidebar.php';
?>

<!-- Wishlist Page Content -->
<div class="space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Wishlist</h2>
            <p class="text-slate-500 mt-1">Target tabungan dan impian Anda</p>
        </div>
        <button onclick="openWishlistModal()" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah Wishlist</span>
        </button>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card p-4 text-center">
            <p class="text-3xl font-bold text-emerald-600"><?= $totalItems ?></p>
            <p class="text-sm text-slate-500">Total Wishlist</p>
        </div>
        <div class="card p-4 text-center">
            <p class="text-3xl font-bold text-sky-600"><?= $completedItems ?></p>
            <p class="text-sm text-slate-500">Tercapai</p>
        </div>
        <div class="card p-4 text-center">
            <p class="text-xl font-bold text-teal-600"><?= formatShortCurrency($totalSaved) ?></p>
            <p class="text-sm text-slate-500">Terkumpul</p>
        </div>
        <div class="card p-4 text-center">
            <p class="text-xl font-bold text-slate-600"><?= formatShortCurrency($totalTarget) ?></p>
            <p class="text-sm text-slate-500">Target</p>
        </div>
    </div>
    
    <!-- Filter Tabs -->
    <div class="flex gap-2 flex-wrap">
        <a href="wishlist.php" class="px-4 py-2 rounded-xl font-medium transition-all <?= !$filterStatus ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-600' ?>">
            Semua
        </a>
        <a href="wishlist.php?status=Belum" class="px-4 py-2 rounded-xl font-medium transition-all <?= $filterStatus === 'Belum' ? 'bg-amber-500 text-white' : 'bg-slate-100 text-slate-600' ?>">
            <i class="bi bi-hourglass-split mr-1"></i>
            Dalam Proses
        </a>
        <a href="wishlist.php?status=Tercapai" class="px-4 py-2 rounded-xl font-medium transition-all <?= $filterStatus === 'Tercapai' ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-600' ?>">
            <i class="bi bi-check-circle mr-1"></i>
            Tercapai
        </a>
    </div>
    
    <!-- Wishlist Grid -->
    <?php if (empty($wishlistItems)): ?>
    <div class="card p-16 text-center">
        <div class="empty-state">
            <i class="bi bi-heart"></i>
            <p class="font-medium text-slate-600 mb-1">Belum ada wishlist</p>
            <p class="text-sm mb-4">Mulai tambahkan target tabungan Anda</p>
            <button onclick="openWishlistModal()" class="btn btn-primary">
                <i class="bi bi-plus-lg"></i>
                Tambah Wishlist
            </button>
        </div>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($wishlistItems as $item): ?>
        <?php 
        $progress = calculatePercentage($item['tabungan_terkumpul'], $item['harga']);
        $isCompleted = $item['status'] === 'Tercapai';
        $priorityColors = [
            'Tinggi' => 'bg-red-100 text-red-700',
            'Sedang' => 'bg-amber-100 text-amber-700',
            'Rendah' => 'bg-emerald-100 text-emerald-700'
        ];
        ?>
        <div class="card p-5 <?= $isCompleted ? 'ring-2 ring-emerald-500' : '' ?> animate-fade-in">
            <!-- Header -->
            <div class="flex items-start justify-between mb-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-1">
                        <?php if ($isCompleted): ?>
                        <span class="badge badge-success">
                            <i class="bi bi-check-circle-fill"></i>
                            Tercapai
                        </span>
                        <?php else: ?>
                        <span class="badge <?= $priorityColors[$item['prioritas']] ?>">
                            <?= $item['prioritas'] ?>
                        </span>
                        <?php endif; ?>
                    </div>
                    <h4 class="font-semibold text-slate-800 text-lg"><?= htmlspecialchars($item['nama_barang']) ?></h4>
                </div>
                <div class="flex gap-1">
                    <?php if (!$isCompleted): ?>
                    <button onclick='openSavingsModal(<?= json_encode($item) ?>)' 
                            class="p-2 text-emerald-500 hover:bg-emerald-50 rounded-lg transition-colors"
                            title="Tambah Tabungan">
                        <i class="bi bi-plus-circle"></i>
                    </button>
                    <?php endif; ?>
                    <button onclick='editWishlist(<?= json_encode($item) ?>)' 
                            class="p-2 text-slate-400 hover:text-sky-500 hover:bg-sky-50 rounded-lg transition-colors"
                            title="Edit">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <a href="wishlist.php?action=delete&id=<?= $item['id_wishlist'] ?>" 
                       class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                       data-delete-confirm="Yakin ingin menghapus wishlist ini?"
                       title="Hapus">
                        <i class="bi bi-trash"></i>
                    </a>
                </div>
            </div>
            
            <!-- Target Price -->
            <div class="mb-4">
                <p class="text-2xl font-bold text-slate-800"><?= formatRupiah($item['harga']) ?></p>
                <?php if ($item['target_date']): ?>
                <p class="text-sm text-slate-400">
                    <i class="bi bi-calendar3 mr-1"></i>
                    Target: <?= formatTanggal($item['target_date'], 'short') ?>
                </p>
                <?php endif; ?>
            </div>
            
            <!-- Progress Bar -->
            <div class="mb-3">
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-slate-500">Terkumpul</span>
                    <span class="font-semibold text-emerald-600"><?= $progress ?>%</span>
                </div>
                <div class="progress-bar h-3">
                    <div class="progress-bar-fill bg-gradient-to-r from-emerald-500 to-teal-500" 
                         style="width: <?= $progress ?>%"></div>
                </div>
                <div class="flex justify-between text-sm mt-1 text-slate-500">
                    <span><?= formatRupiah($item['tabungan_terkumpul']) ?></span>
                    <span>Kurang <?= formatRupiah($item['harga'] - $item['tabungan_terkumpul']) ?></span>
                </div>
            </div>
            
            <!-- Notes -->
            <?php if ($item['catatan']): ?>
            <p class="text-sm text-slate-400 italic"><?= htmlspecialchars($item['catatan']) ?></p>
            <?php endif; ?>
            
            <!-- Quick Actions -->
            <?php if (!$isCompleted): ?>
            <div class="mt-4 pt-4 border-t border-slate-100 flex gap-2">
                <button onclick='openSavingsModal(<?= json_encode($item) ?>)' 
                        class="btn btn-primary btn-sm flex-1 text-sm py-2">
                    <i class="bi bi-plus-lg"></i>
                    Tambah Tabungan
                </button>
                <a href="wishlist.php?action=complete&id=<?= $item['id_wishlist'] ?>" 
                   class="btn btn-secondary btn-sm text-sm py-2"
                   data-delete-confirm="Tandai sebagai tercapai?">
                    <i class="bi bi-check-lg"></i>
                </a>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Pagination -->
    <?= renderPagination($pagination, $baseUrl) ?>
    
    <?php endif; ?>
</div>

<!-- Wishlist Modal -->
<div id="wishlistModal" class="modal-overlay">
    <div class="modal-content p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 id="wishlistModalTitle" class="text-xl font-bold text-slate-800">Tambah Wishlist</h3>
            <button data-modal-close class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <i class="bi bi-x-lg text-slate-400"></i>
            </button>
        </div>
        
        <form method="POST" id="wishlistForm" class="space-y-4">
            <input type="hidden" name="id_wishlist" id="id_wishlist">
            
            <!-- Item Name -->
            <div>
                <label class="form-label">Nama Barang / Target</label>
                <input type="text" name="nama_barang" id="nama_barang" required 
                       class="form-input" placeholder="Contoh: iPhone 15, Liburan ke Bali">
            </div>
            
            <!-- Target Price -->
            <div>
                <label class="form-label">Harga Target</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">Rp</span>
                    <input type="text" name="harga" id="harga" required 
                           class="form-input pl-12" placeholder="0"
                           oninput="formatCurrencyInput(this)">
                </div>
            </div>
            
            <!-- Priority -->
            <div>
                <label class="form-label">Prioritas</label>
                <div class="flex gap-2">
                    <label class="flex-1">
                        <input type="radio" name="prioritas" value="Rendah" class="peer hidden">
                        <div class="p-3 rounded-xl border-2 border-slate-200 text-center cursor-pointer transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50 text-sm">
                            Rendah
                        </div>
                    </label>
                    <label class="flex-1">
                        <input type="radio" name="prioritas" value="Sedang" class="peer hidden" checked>
                        <div class="p-3 rounded-xl border-2 border-slate-200 text-center cursor-pointer transition-all peer-checked:border-amber-500 peer-checked:bg-amber-50 text-sm">
                            Sedang
                        </div>
                    </label>
                    <label class="flex-1">
                        <input type="radio" name="prioritas" value="Tinggi" class="peer hidden">
                        <div class="p-3 rounded-xl border-2 border-slate-200 text-center cursor-pointer transition-all peer-checked:border-red-500 peer-checked:bg-red-50 text-sm">
                            Tinggi
                        </div>
                    </label>
                </div>
            </div>
            
            <!-- Target Date -->
            <div>
                <label class="form-label">Target Tanggal (Opsional)</label>
                <input type="date" name="target_date" id="target_date" class="form-input">
            </div>
            
            <!-- Notes -->
            <div>
                <label class="form-label">Catatan (Opsional)</label>
                <textarea name="catatan" id="catatan" rows="2" 
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

<!-- Add Savings Modal -->
<div id="savingsModal" class="modal-overlay">
    <div class="modal-content p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-slate-800">Tambah Tabungan</h3>
            <button data-modal-close class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <i class="bi bi-x-lg text-slate-400"></i>
            </button>
        </div>
        
        <form method="POST" id="savingsForm" class="space-y-4">
            <input type="hidden" name="form_type" value="add_savings">
            <input type="hidden" name="id_wishlist" id="savings_wishlist_id">
            
            <div class="p-4 bg-slate-50 rounded-xl mb-4">
                <p class="text-sm text-slate-500 mb-1">Target:</p>
                <p id="savings_item_name" class="font-semibold text-slate-800"></p>
                <div class="flex items-center gap-2 mt-2">
                    <span id="savings_current" class="text-emerald-600 font-medium"></span>
                    <span class="text-slate-400">/</span>
                    <span id="savings_target" class="text-slate-600"></span>
                </div>
            </div>
            
            <!-- Amount -->
            <div>
                <label class="form-label">Jumlah Tabungan</label>
                <div class="relative">
                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">Rp</span>
                    <input type="text" name="amount" id="savings_amount" required 
                           class="form-input pl-12" placeholder="0"
                           oninput="formatCurrencyInput(this)">
                </div>
            </div>
            
            <!-- Quick Amount Buttons -->
            <div class="flex gap-2 flex-wrap">
                <button type="button" onclick="setQuickAmount(50000)" class="btn btn-secondary btn-sm">+50rb</button>
                <button type="button" onclick="setQuickAmount(100000)" class="btn btn-secondary btn-sm">+100rb</button>
                <button type="button" onclick="setQuickAmount(250000)" class="btn btn-secondary btn-sm">+250rb</button>
                <button type="button" onclick="setQuickAmount(500000)" class="btn btn-secondary btn-sm">+500rb</button>
            </div>
            
            <!-- Actions -->
            <div class="flex gap-3 pt-4">
                <button type="button" data-modal-close class="btn btn-secondary flex-1">
                    Batal
                </button>
                <button type="submit" class="btn btn-primary flex-1">
                    <i class="bi bi-piggy-bank"></i>
                    Tabung
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function formatCurrencyInput(input) {
    let value = input.value.replace(/\D/g, '');
    if (value) {
        value = parseInt(value).toLocaleString('id-ID');
    }
    input.value = value;
}

function openWishlistModal(data = null) {
    const modal = document.getElementById('wishlistModal');
    const title = document.getElementById('wishlistModalTitle');
    const form = document.getElementById('wishlistForm');
    
    // Reset form
    form.reset();
    document.getElementById('id_wishlist').value = '';
    
    if (data) {
        title.textContent = 'Edit Wishlist';
        document.getElementById('id_wishlist').value = data.id_wishlist;
        document.getElementById('nama_barang').value = data.nama_barang;
        document.getElementById('harga').value = parseInt(data.harga).toLocaleString('id-ID');
        document.getElementById('target_date').value = data.target_date || '';
        document.getElementById('catatan').value = data.catatan || '';
        
        // Set priority
        const priorityInput = document.querySelector(`input[name="prioritas"][value="${data.prioritas}"]`);
        if (priorityInput) priorityInput.checked = true;
    } else {
        title.textContent = 'Tambah Wishlist';
    }
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function editWishlist(data) {
    openWishlistModal(data);
}

function openSavingsModal(item) {
    const modal = document.getElementById('savingsModal');
    document.getElementById('savings_wishlist_id').value = item.id_wishlist;
    document.getElementById('savings_item_name').textContent = item.nama_barang;
    document.getElementById('savings_current').textContent = MyTabungan.formatRupiah(item.tabungan_terkumpul);
    document.getElementById('savings_target').textContent = MyTabungan.formatRupiah(item.harga);
    document.getElementById('savings_amount').value = '';
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function setQuickAmount(amount) {
    const input = document.getElementById('savings_amount');
    const current = parseInt(input.value.replace(/\D/g, '')) || 0;
    input.value = (current + amount).toLocaleString('id-ID');
}

// Close modal handlers
document.querySelectorAll('[data-modal-close]').forEach(btn => {
    btn.addEventListener('click', () => {
        document.querySelectorAll('.modal-overlay').forEach(m => m.classList.remove('active'));
        document.body.style.overflow = '';
    });
});
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>
