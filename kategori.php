<?php
/**
 * Category Management Page
 * MyTabungan - Personal Finance Management
 */

require_once __DIR__ . '/auth/auth.php';
require_once __DIR__ . '/config/database.php';

// Require login
requireLogin();

$pageTitle = 'Kategori';
$userId = getCurrentUserId();
$pdo = getConnection();

// =====================================================
// Handle Actions
// =====================================================
$action = $_GET['action'] ?? '';
$editId = $_GET['id'] ?? null;

// Handle DELETE
if ($action === 'delete' && $editId) {
    // Check if category belongs to user (not system category)
    $stmt = $pdo->prepare("SELECT id_pengguna FROM kategori WHERE id_kategori = ?");
    $stmt->execute([$editId]);
    $cat = $stmt->fetch();
    
    if ($cat && $cat['id_pengguna'] == $userId) {
        // Check if category is in use
        $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM transaksi WHERE id_kategori = ?");
        $stmt->execute([$editId]);
        $usage = $stmt->fetch();
        
        if ($usage['count'] > 0) {
            setFlashMessage('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh ' . $usage['count'] . ' transaksi.');
        } else {
            $stmt = $pdo->prepare("DELETE FROM kategori WHERE id_kategori = ? AND id_pengguna = ?");
            $stmt->execute([$editId, $userId]);
            setFlashMessage('success', 'Kategori berhasil dihapus.');
        }
    } else {
        setFlashMessage('error', 'Anda tidak dapat menghapus kategori default.');
    }
    redirect('kategori.php');
}

// Handle POST (Add/Edit)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_kategori = sanitize($_POST['nama_kategori'] ?? '');
    $tipe = $_POST['tipe'] ?? '';
    $icon = $_POST['icon'] ?? 'bi-tag';
    $warna = $_POST['warna'] ?? '#10b981';
    $categoryId = $_POST['id_kategori'] ?? null;
    
    if (empty($nama_kategori) || empty($tipe)) {
        setFlashMessage('error', 'Nama kategori dan tipe harus diisi.');
    } else {
        try {
            if ($categoryId) {
                // Update existing category (only user's own)
                $stmt = $pdo->prepare("
                    UPDATE kategori 
                    SET nama_kategori = ?, tipe = ?, icon = ?, warna = ?
                    WHERE id_kategori = ? AND id_pengguna = ?
                ");
                $stmt->execute([$nama_kategori, $tipe, $icon, $warna, $categoryId, $userId]);
                setFlashMessage('success', 'Kategori berhasil diperbarui.');
            } else {
                // Insert new category
                $stmt = $pdo->prepare("
                    INSERT INTO kategori (id_pengguna, nama_kategori, tipe, icon, warna)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->execute([$userId, $nama_kategori, $tipe, $icon, $warna]);
                setFlashMessage('success', 'Kategori berhasil ditambahkan.');
            }
            redirect('kategori.php');
        } catch (PDOException $e) {
            error_log("Category Error: " . $e->getMessage());
            setFlashMessage('error', 'Terjadi kesalahan. Silakan coba lagi.');
        }
    }
}

// =====================================================
// Get Categories with Pagination
// =====================================================
$currentPage = (int) ($_GET['page'] ?? 1);
$perPage = 12;
$filterType = $_GET['type'] ?? 'Pemasukan';

// Count totals for tabs
$stmt = $pdo->prepare("SELECT tipe, COUNT(*) as cnt FROM kategori WHERE id_pengguna IS NULL OR id_pengguna = ? GROUP BY tipe");
$stmt->execute([$userId]);
$typeCounts = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);
$incomeCount = $typeCounts['Pemasukan'] ?? 0;
$expenseCount = $typeCounts['Pengeluaran'] ?? 0;

// Get total for current type
$totalForType = $filterType === 'Pemasukan' ? $incomeCount : $expenseCount;

// Get pagination data
$pagination = getPagination($totalForType, $perPage, $currentPage);

// Get paginated categories for current type
$stmt = $pdo->prepare("
    SELECT k.*, 
           (SELECT COUNT(*) FROM transaksi t WHERE t.id_kategori = k.id_kategori AND t.id_pengguna = ?) as usage_count
    FROM kategori k
    WHERE (k.id_pengguna IS NULL OR k.id_pengguna = ?) AND k.tipe = ?
    ORDER BY k.nama_kategori
    LIMIT {$pagination['limit']} OFFSET {$pagination['offset']}
");
$stmt->execute([$userId, $userId, $filterType]);
$categories = $stmt->fetchAll();

// Build base URL for pagination
$baseUrl = 'kategori.php?type=' . $filterType;

// Available icons
$availableIcons = [
    'bi-wallet2', 'bi-cash-stack', 'bi-credit-card', 'bi-bank', 'bi-gift', 'bi-graph-up-arrow',
    'bi-cup-hot', 'bi-cart3', 'bi-bag', 'bi-car-front', 'bi-bus-front', 'bi-fuel-pump',
    'bi-house', 'bi-lightning', 'bi-droplet', 'bi-wifi', 'bi-phone', 'bi-tv',
    'bi-heart-pulse', 'bi-hospital', 'bi-capsule', 'bi-book', 'bi-mortarboard',
    'bi-controller', 'bi-film', 'bi-music-note', 'bi-airplane', 'bi-globe',
    'bi-shirt', 'bi-scissors', 'bi-tools', 'bi-gear', 'bi-tag', 'bi-tags',
    'bi-shop', 'bi-building', 'bi-briefcase', 'bi-piggy-bank', 'bi-coin'
];

// Include layout
include __DIR__ . '/layout/header.php';
include __DIR__ . '/layout/sidebar.php';
?>

<!-- Category Page Content -->
<div class="space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Kategori</h2>
            <p class="text-slate-500 mt-1">Kelola kategori pemasukan dan pengeluaran</p>
        </div>
        <button onclick="openCategoryModal()" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i>
            <span>Tambah Kategori</span>
        </button>
    </div>
    
    <!-- Category Tabs (URL-based) -->
    <div class="flex gap-2">
        <a href="kategori.php?type=Pemasukan" 
           class="px-4 py-2 rounded-xl font-medium transition-all <?= $filterType === 'Pemasukan' ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
            <i class="bi bi-arrow-down-circle mr-1"></i>
            Pemasukan (<?= $incomeCount ?>)
        </a>
        <a href="kategori.php?type=Pengeluaran" 
           class="px-4 py-2 rounded-xl font-medium transition-all <?= $filterType === 'Pengeluaran' ? 'bg-red-500 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
            <i class="bi bi-arrow-up-circle mr-1"></i>
            Pengeluaran (<?= $expenseCount ?>)
        </a>
    </div>
    
    <!-- Categories Grid -->
    <?php if (empty($categories)): ?>
    <div class="card p-8 text-center">
        <i class="bi bi-folder-x text-4xl text-slate-300 mb-2"></i>
        <p class="text-slate-500">Tidak ada kategori <?= $filterType === 'Pemasukan' ? 'pemasukan' : 'pengeluaran' ?></p>
    </div>
    <?php else: ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        <?php foreach ($categories as $cat): ?>
        <div class="card p-4 animate-fade-in">
            <div class="flex items-start justify-between mb-3">
                <div class="w-12 h-12 rounded-xl flex items-center justify-center" style="background-color: <?= $cat['warna'] ?>20">
                    <i class="bi <?= $cat['icon'] ?> text-2xl" style="color: <?= $cat['warna'] ?>"></i>
                </div>
                <?php if ($cat['id_pengguna']): ?>
                <div class="flex gap-1">
                    <button onclick='editCategory(<?= json_encode($cat) ?>)' 
                            class="p-2 text-slate-400 hover:text-sky-500 hover:bg-sky-50 rounded-lg transition-colors">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <a href="kategori.php?action=delete&id=<?= $cat['id_kategori'] ?>&type=<?= $filterType ?>" 
                       class="p-2 text-slate-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-colors"
                       data-delete-confirm="Yakin ingin menghapus kategori ini?">
                        <i class="bi bi-trash"></i>
                    </a>
                </div>
                <?php else: ?>
                <span class="badge bg-slate-100 text-slate-500">Default</span>
                <?php endif; ?>
            </div>
            <h4 class="font-semibold text-slate-800"><?= htmlspecialchars($cat['nama_kategori']) ?></h4>
            <p class="text-sm text-slate-400 mt-1"><?= $cat['usage_count'] ?> transaksi</p>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Pagination -->
    <?= renderPagination($pagination, $baseUrl) ?>
    
    <?php endif; ?>
</div>

<!-- Category Modal -->
<div id="categoryModal" class="modal-overlay">
    <div class="modal-content p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 id="modalTitle" class="text-xl font-bold text-slate-800">Tambah Kategori</h3>
            <button data-modal-close class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <i class="bi bi-x-lg text-slate-400"></i>
            </button>
        </div>
        
        <form method="POST" id="categoryForm" class="space-y-4">
            <input type="hidden" name="id_kategori" id="id_kategori">
            
            <!-- Category Name -->
            <div>
                <label class="form-label">Nama Kategori</label>
                <input type="text" name="nama_kategori" id="nama_kategori" required 
                       class="form-input" placeholder="Contoh: Makan Siang">
            </div>
            
            <!-- Type -->
            <div>
                <label class="form-label">Tipe</label>
                <div class="flex gap-2">
                    <label class="flex-1">
                        <input type="radio" name="tipe" value="Pemasukan" class="peer hidden" required>
                        <div class="p-3 rounded-xl border-2 border-slate-200 text-center cursor-pointer transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-50">
                            <i class="bi bi-arrow-down-circle text-emerald-500 mr-1"></i>
                            Pemasukan
                        </div>
                    </label>
                    <label class="flex-1">
                        <input type="radio" name="tipe" value="Pengeluaran" class="peer hidden" checked>
                        <div class="p-3 rounded-xl border-2 border-slate-200 text-center cursor-pointer transition-all peer-checked:border-red-500 peer-checked:bg-red-50">
                            <i class="bi bi-arrow-up-circle text-red-500 mr-1"></i>
                            Pengeluaran
                        </div>
                    </label>
                </div>
            </div>
            
            <!-- Icon -->
            <div>
                <label class="form-label">Ikon</label>
                <div class="grid grid-cols-8 gap-2 max-h-32 overflow-y-auto p-2 bg-slate-50 rounded-xl">
                    <?php foreach ($availableIcons as $icon): ?>
                    <label class="cursor-pointer">
                        <input type="radio" name="icon" value="<?= $icon ?>" class="peer hidden" <?= $icon === 'bi-tag' ? 'checked' : '' ?>>
                        <div class="w-full aspect-square flex items-center justify-center rounded-lg border-2 border-transparent transition-all peer-checked:border-emerald-500 peer-checked:bg-emerald-100 hover:bg-slate-100">
                            <i class="bi <?= $icon ?> text-lg"></i>
                        </div>
                    </label>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <!-- Color -->
            <div>
                <label class="form-label">Warna</label>
                <div class="flex gap-2">
                    <?php 
                    $colors = ['#10b981', '#059669', '#0d9488', '#0ea5e9', '#3b82f6', '#6366f1', '#8b5cf6', '#a855f7', '#ec4899', '#ef4444', '#f97316', '#eab308'];
                    foreach ($colors as $color): 
                    ?>
                    <label class="cursor-pointer">
                        <input type="radio" name="warna" value="<?= $color ?>" class="peer hidden" <?= $color === '#10b981' ? 'checked' : '' ?>>
                        <div class="w-8 h-8 rounded-full border-2 border-transparent transition-all peer-checked:border-slate-800 peer-checked:scale-110" 
                             style="background-color: <?= $color ?>"></div>
                    </label>
                    <?php endforeach; ?>
                </div>
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
function showCategoryTab(tab) {
    const tabIncome = document.getElementById('tabIncome');
    const tabExpense = document.getElementById('tabExpense');
    const incomeCategories = document.getElementById('incomeCategories');
    const expenseCategories = document.getElementById('expenseCategories');
    
    if (tab === 'income') {
        tabIncome.className = 'px-4 py-2 rounded-xl font-medium transition-all bg-emerald-500 text-white';
        tabExpense.className = 'px-4 py-2 rounded-xl font-medium transition-all bg-slate-100 text-slate-600';
        incomeCategories.classList.remove('hidden');
        expenseCategories.classList.add('hidden');
    } else {
        tabExpense.className = 'px-4 py-2 rounded-xl font-medium transition-all bg-red-500 text-white';
        tabIncome.className = 'px-4 py-2 rounded-xl font-medium transition-all bg-slate-100 text-slate-600';
        expenseCategories.classList.remove('hidden');
        incomeCategories.classList.add('hidden');
    }
}

function openCategoryModal(data = null) {
    const modal = document.getElementById('categoryModal');
    const title = document.getElementById('modalTitle');
    const form = document.getElementById('categoryForm');
    
    // Reset form
    form.reset();
    document.getElementById('id_kategori').value = '';
    
    if (data) {
        title.textContent = 'Edit Kategori';
        document.getElementById('id_kategori').value = data.id_kategori;
        document.getElementById('nama_kategori').value = data.nama_kategori;
        
        // Set type
        document.querySelector(`input[name="tipe"][value="${data.tipe}"]`).checked = true;
        
        // Set icon
        const iconInput = document.querySelector(`input[name="icon"][value="${data.icon}"]`);
        if (iconInput) iconInput.checked = true;
        
        // Set color
        const colorInput = document.querySelector(`input[name="warna"][value="${data.warna}"]`);
        if (colorInput) colorInput.checked = true;
    } else {
        title.textContent = 'Tambah Kategori';
    }
    
    modal.classList.add('active');
    document.body.style.overflow = 'hidden';
}

function editCategory(data) {
    openCategoryModal(data);
}

// Close modal handlers
document.querySelectorAll('[data-modal-close]').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('categoryModal').classList.remove('active');
        document.body.style.overflow = '';
    });
});
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>
