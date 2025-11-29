<?php
require_once __DIR__ . '/../config/koneksi.php';
require_once __DIR__ . '/../includes/functions.php';

// Start Session & Check Login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id_pengguna'])) {
    header('Location: ../auth/login.php');
    exit;
}

$id_pengguna = $_SESSION['id_pengguna'];

require_once __DIR__ . '/../layout/header_pages.php';
require_once __DIR__ . '/../layout/sidebar.php';

// Logika Filter & Pagination
$search_query = cleanInput($_GET['q'] ?? '');
$tipe_filter = cleanInput($_GET['tipe'] ?? '');
$params = [':id_pengguna' => $id_pengguna];
$where_filter = "";

if (!empty($search_query)) {
    $where_filter .= " AND nama_kategori LIKE :search_query";
    $params[':search_query'] = "%" . $search_query . "%";
}

if (!empty($tipe_filter)) {
    $where_filter .= " AND tipe = :tipe";
    $params[':tipe'] = $tipe_filter;
}

try {
    $limit = 10;
    $halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
    $offset = ($halaman - 1) * $limit;

    // Count Total Data & Stats
    $sql_count = "SELECT 
                    COUNT(*) as total,
                    COUNT(CASE WHEN tipe = 'Pemasukan' THEN 1 END) as total_pemasukan,
                    COUNT(CASE WHEN tipe = 'Pengeluaran' THEN 1 END) as total_pengeluaran
                  FROM kategori WHERE id_pengguna = :id_pengguna $where_filter";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute($params);
    $stats = $stmt_count->fetch(PDO::FETCH_ASSOC);
    
    $total_data = $stats['total'];
    $jumlah_kategori = $total_data;
    $total_pemasukan = $stats['total_pemasukan'];
    $total_pengeluaran = $stats['total_pengeluaran'];
    
    $total_halaman = ceil($total_data / $limit);

    $sql = "SELECT * FROM kategori WHERE id_pengguna = :id_pengguna $where_filter ORDER BY tipe, nama_kategori ASC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    foreach ($params as $key => &$val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->execute();
    $kategori = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $_SESSION['pesan_error'] = "Gagal mengambil data: " . $e->getMessage();
    $kategori = [];
    $jumlah_kategori = 0;
    $total_pemasukan = 0;
    $total_pengeluaran = 0;
    $total_halaman = 0;
}
?>

<main class="main-content p-6 lg:p-8 transition-all duration-300 ease-in-out ml-0 lg:ml-64">
    
    <!-- Page Header & Stats -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Kelola Kategori</h1>
            <p class="text-gray-500 text-sm mt-1">Atur kategori untuk mengelompokkan transaksimu.</p>
        </div>
        <button onclick="document.getElementById('tambahKategoriModal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-100 transition-all shadow-sm hover:shadow-md">
            <i class="bi bi-plus-lg mr-2"></i> Tambah Kategori
        </button>
    </div>

    <!-- Stats Card -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Total Kategori Card -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="absolute right-0 top-0 w-32 h-32 bg-linear-to-br from-indigo-50 to-purple-50 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110 duration-500"></div>
            <div class="w-14 h-14 rounded-xl bg-linear-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white shadow-lg shadow-indigo-200 relative z-10 transform group-hover:rotate-6 transition-transform duration-300">
                <i class="bi bi-tags-fill text-2xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-sm text-gray-500 font-medium mb-1">Total Kategori</p>
                <h3 class="text-3xl font-bold text-gray-900"><?php echo $jumlah_kategori; ?></h3>
            </div>
        </div>

        <!-- Kategori Pemasukan -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="absolute right-0 top-0 w-32 h-32 bg-linear-to-br from-emerald-50 to-teal-50 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110 duration-500"></div>
            <div class="w-14 h-14 rounded-xl bg-linear-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg shadow-emerald-200 relative z-10 transform group-hover:rotate-6 transition-transform duration-300">
                <i class="bi bi-arrow-down-left text-2xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-sm text-gray-500 font-medium mb-1">Kategori Pemasukan</p>
                <h3 class="text-3xl font-bold text-gray-900"><?php echo $total_pemasukan; ?></h3>
            </div>
        </div>

        <!-- Kategori Pengeluaran -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4 relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="absolute right-0 top-0 w-32 h-32 bg-linear-to-br from-red-50 to-orange-50 rounded-full -mr-16 -mt-16 transition-transform group-hover:scale-110 duration-500"></div>
            <div class="w-14 h-14 rounded-xl bg-linear-to-br from-red-500 to-orange-600 flex items-center justify-center text-white shadow-lg shadow-red-200 relative z-10 transform group-hover:rotate-6 transition-transform duration-300">
                <i class="bi bi-arrow-up-right text-2xl"></i>
            </div>
            <div class="relative z-10">
                <p class="text-sm text-gray-500 font-medium mb-1">Kategori Pengeluaran</p>
                <h3 class="text-3xl font-bold text-gray-900"><?php echo $total_pengeluaran; ?></h3>
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 mb-8">
        <form action="index.php" method="GET" class="grid grid-cols-1 md:grid-cols-12 gap-6 items-end">
            <!-- Search Box -->
            <div class="md:col-span-5">
                <label for="q" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Cari Kategori</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="bi bi-search text-gray-400 group-focus-within:text-emerald-500 transition-colors"></i>
                    </div>
                    <input type="text" name="q" id="q" class="block w-full pl-11 pr-4 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-emerald-500 text-sm transition-all duration-200" placeholder="Ketik nama kategori..." value="<?php echo htmlspecialchars($search_query); ?>">
                </div>
            </div>

            <!-- Filter Tipe -->
            <div class="md:col-span-4">
                <label for="tipe" class="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Tipe Kategori</label>
                <div class="relative group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="bi bi-funnel text-gray-400 group-focus-within:text-emerald-500 transition-colors"></i>
                    </div>
                    <select name="tipe" id="tipe" class="block w-full pl-11 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl focus:bg-white focus:ring-2 focus:ring-emerald-100 focus:border-emerald-500 text-sm appearance-none cursor-pointer transition-all duration-200" onchange="this.form.submit()">
                        <option value="">Semua Tipe</option>
                        <option value="Pemasukan" <?php echo ($tipe_filter == 'Pemasukan') ? 'selected' : ''; ?>>Pemasukan</option>
                        <option value="Pengeluaran" <?php echo ($tipe_filter == 'Pengeluaran') ? 'selected' : ''; ?>>Pengeluaran</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                        <i class="bi bi-chevron-down text-gray-400 text-xs"></i>
                    </div>
                </div>
            </div>

            <!-- Reset Button -->
            <div class="md:col-span-3 flex justify-end">
                <?php if (!empty($search_query) || !empty($tipe_filter)): ?>
                    <a href="index.php" class="inline-flex items-center px-6 py-3 border border-gray-200 shadow-sm text-sm font-semibold rounded-xl text-gray-600 bg-white hover:bg-gray-50 hover:text-gray-900 hover:border-gray-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-200 transition-all w-full md:w-auto justify-center group">
                        <i class="bi bi-arrow-counterclockwise mr-2 group-hover:-rotate-180 transition-transform duration-500"></i> Reset Filter
                    </a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Main Table Card -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Nama Kategori</th>
                        <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tipe</th>
                        <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($kategori)): ?>
                        <tr>
                            <td colspan="3" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center py-8">
                                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mb-4 shadow-sm">
                                        <i class="bi bi-inbox text-3xl text-gray-300"></i>
                                    </div>
                                    <p class="text-lg font-medium text-gray-900">Belum ada kategori</p>
                                    <p class="text-sm text-gray-500 mt-1 mb-6 max-w-xs text-center">Mulai buat kategori untuk mengelompokkan transaksi keuanganmu dengan lebih rapi!</p>
                                    <button onclick="document.getElementById('tambahKategoriModal').classList.remove('hidden')" class="inline-flex items-center px-5 py-2.5 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-100 transition-all shadow-sm hover:shadow-md">
                                        <i class="bi bi-plus-lg mr-2"></i> Tambah Kategori
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($kategori as $k): ?>
                            <tr class="hover:bg-gray-50/80 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 rounded-xl <?php echo ($k['tipe'] == 'Pemasukan') ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600'; ?> flex items-center justify-center text-lg shadow-sm">
                                            <i class="bi bi-tag-fill"></i>
                                        </div>
                                        <span class="font-medium text-gray-900 group-hover:text-emerald-600 transition-colors text-base">
                                            <?php echo htmlspecialchars($k['nama_kategori']); ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium <?php echo ($k['tipe'] == 'Pemasukan') ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo htmlspecialchars($k['tipe']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center gap-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button onclick="openEditKategoriModal(<?php echo htmlspecialchars(json_encode($k), ENT_QUOTES, 'UTF-8'); ?>)" class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                            <i class="bi bi-pencil-square text-lg"></i>
                                        </button>
                                        <button onclick="confirmDelete('hapus.php?id=<?php echo $k['id_kategori']; ?>')" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                            <i class="bi bi-trash text-lg"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_halaman > 1): ?>
            <div class="px-6 py-4 border-t border-gray-100 bg-gray-50 flex justify-center">
                <nav class="inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                    <!-- Previous -->
                    <a href="?halaman=<?php echo max(1, $halaman - 1); ?>&<?php echo http_build_query(array_diff_key($_GET, ['halaman' => ''])); ?>" class="relative inline-flex items-center px-3 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 <?php echo ($halaman <= 1) ? 'pointer-events-none opacity-50' : ''; ?>">
                        <span class="sr-only">Previous</span>
                        <i class="bi bi-chevron-left"></i>
                    </a>
                    
                    <!-- Page Numbers -->
                    <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                        <a href="?halaman=<?php echo $i; ?>&<?php echo http_build_query(array_diff_key($_GET, ['halaman' => ''])); ?>" class="relative inline-flex items-center px-4 py-2 border border-gray-300 bg-white text-sm font-medium <?php echo ($halaman == $i) ? 'z-10 bg-emerald-50 border-emerald-500 text-emerald-600' : 'text-gray-500 hover:bg-gray-50'; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endfor; ?>

                    <!-- Next -->
                    <a href="?halaman=<?php echo min($total_halaman, $halaman + 1); ?>&<?php echo http_build_query(array_diff_key($_GET, ['halaman' => ''])); ?>" class="relative inline-flex items-center px-3 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 <?php echo ($halaman >= $total_halaman) ? 'pointer-events-none opacity-50' : ''; ?>">
                        <span class="sr-only">Next</span>
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </nav>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../modal/kategori_modal.php'; ?>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>
<script src="<?php echo $base_url; ?>/js/kategori.js"></script>