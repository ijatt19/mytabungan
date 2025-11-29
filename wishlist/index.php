<?php
require_once __DIR__ . '/../config/koneksi.php';

// Start Session & Check Login (Manual check before headers)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id_pengguna'])) {
    header('Location: ../auth/login.php');
    exit;
}

$id_pengguna = $_SESSION['id_pengguna'];

// Logic dipindah ke tambah.php, edit.php, dan hapus.php

// Include Layouts (AFTER logic)
require_once __DIR__ . '/../layout/header_pages.php';
// require_once __DIR__ . '/../auth/cek_masuk.php'; // Already checked manually above
require_once __DIR__ . '/../layout/sidebar.php';

// --- Filter Logic ---
$search_query = cleanInput($_GET['q'] ?? '');
$status_filter = cleanInput($_GET['status'] ?? '');
$prioritas_filter = cleanInput($_GET['prioritas'] ?? '');

$params = [':id_pengguna' => $id_pengguna];
$where_clauses = [];

if (!empty($search_query)) {
    $where_clauses[] = "nama_barang LIKE :search_query";
    $params[':search_query'] = "%" . $search_query . "%";
}
if (!empty($status_filter)) {
    $where_clauses[] = "status = :status";
    $params[':status'] = $status_filter;
}
if (!empty($prioritas_filter)) {
    $where_clauses[] = "prioritas = :prioritas";
    $params[':prioritas'] = $prioritas_filter;
}

$where_sql = "";
if (!empty($where_clauses)) {
    $where_sql = " AND " . implode(" AND ", $where_clauses);
}

// --- Data Fetching ---
try {
    // 1. Calculate Totals based on Filters (Contextual Summary)
    $sql_summary = "SELECT 
                        COUNT(*) as total_items,
                        SUM(harga) as total_nilai,
                        SUM(CASE WHEN status = 'Selesai' THEN 1 ELSE 0 END) as total_selesai
                    FROM wishlist 
                    WHERE id_pengguna = :id_pengguna $where_sql";
    $stmt_summary = $pdo->prepare($sql_summary);
    $stmt_summary->execute($params);
    $summary = $stmt_summary->fetch();

    $total_items = $summary['total_items'] ?? 0;
    $total_nilai = $summary['total_nilai'] ?? 0;
    $total_selesai = $summary['total_selesai'] ?? 0;

    // 2. Pagination
    $limit = 6;
    $halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
    $offset = ($halaman - 1) * $limit;

    $sql_count = "SELECT COUNT(*) FROM wishlist WHERE id_pengguna = :id_pengguna $where_sql";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute($params);
    $total_data = $stmt_count->fetchColumn();
    $total_halaman = ceil($total_data / $limit);

    // 3. Fetch Wishlist Items
    $sql = "SELECT * FROM wishlist WHERE id_pengguna = :id_pengguna $where_sql ORDER BY FIELD(prioritas, 'Tinggi', 'Sedang', 'Rendah'), dibuat_pada DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    foreach ($params as $key => &$val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 4. Calculate Current Balance (for progress)
    $stmt_saldo = $pdo->prepare("SELECT (SELECT SUM(jumlah) FROM transaksi t JOIN kategori k ON t.id_kategori = k.id_kategori WHERE t.id_pengguna = ? AND k.tipe = 'Pemasukan') - (SELECT SUM(jumlah) FROM transaksi t JOIN kategori k ON t.id_kategori = k.id_kategori WHERE t.id_pengguna = ? AND k.tipe = 'Pengeluaran') AS saldo");
    $stmt_saldo->execute([$id_pengguna, $id_pengguna]);
    $saldo_akhir = $stmt_saldo->fetchColumn() ?? 0;

} catch (PDOException $e) {
    $_SESSION['pesan_error'] = "Gagal mengambil data wishlist: " . $e->getMessage();
    $wishlist_items = [];
    $total_halaman = 0;
}
?>

<main class="main-content p-6 lg:p-8 transition-all duration-300 ease-in-out ml-0 lg:ml-64">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-900">Daftar Impian</h1>
            <p class="text-slate-500 text-sm mt-1">Rencanakan dan wujudkan impianmu satu per satu.</p>
        </div>
        <button type="button" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-medium rounded-xl hover:bg-purple-700 focus:ring-4 focus:ring-purple-100 transition-all shadow-sm hover:shadow-md hover:-translate-y-0.5" onclick="document.getElementById('tambahWishlistModal').classList.remove('hidden')">
            <i class="bi bi-plus-lg mr-2"></i> Tambah Impian
        </button>
    </div>

    <!-- Flash Messages -->
    <!-- Flash Messages handled by Toast in footer -->

    <!-- Contextual Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Impian -->
        <div class="relative overflow-hidden rounded-2xl bg-linear-to-br from-purple-500 to-indigo-600 text-white shadow-lg shadow-purple-100 p-6">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
            <div class="relative z-10 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white border border-white/10">
                    <i class="bi bi-stars text-xl"></i>
                </div>
                <div>
                    <p class="text-purple-100 text-sm font-medium">Total Impian</p>
                    <h3 class="text-3xl font-bold"><?php echo $total_items; ?> <span class="text-lg font-normal opacity-80">Item</span></h3>
                </div>
            </div>
        </div>

        <!-- Total Nilai -->
        <div class="relative overflow-hidden rounded-2xl bg-linear-to-br from-blue-500 to-cyan-600 text-white shadow-lg shadow-blue-100 p-6">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
            <div class="relative z-10 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white border border-white/10">
                    <i class="bi bi-tag-fill text-xl"></i>
                </div>
                <div>
                    <p class="text-blue-100 text-sm font-medium">Total Nilai Target</p>
                    <h3 class="text-2xl font-bold"><?php echo formatRupiah($total_nilai); ?></h3>
                </div>
            </div>
        </div>

        <!-- Tercapai -->
        <div class="relative overflow-hidden rounded-2xl bg-linear-to-br from-emerald-500 to-teal-600 text-white shadow-lg shadow-emerald-100 p-6">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
            <div class="relative z-10 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white border border-white/10">
                    <i class="bi bi-trophy-fill text-xl"></i>
                </div>
                <div>
                    <p class="text-emerald-100 text-sm font-medium">Impian Tercapai</p>
                    <h3 class="text-3xl font-bold"><?php echo $total_selesai; ?> <span class="text-lg font-normal opacity-80">Selesai</span></h3>
                </div>
            </div>
        </div>

        <!-- Total Saldo -->
        <div class="relative overflow-hidden rounded-2xl bg-linear-to-br from-orange-500 to-amber-600 text-white shadow-lg shadow-orange-100 p-6">
            <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white opacity-10 rounded-full blur-xl"></div>
            <div class="relative z-10 flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white border border-white/10">
                    <i class="bi bi-wallet2 text-xl"></i>
                </div>
                <div>
                    <p class="text-orange-100 text-sm font-medium">Total Saldo</p>
                    <h3 class="text-2xl font-bold"><?php echo formatRupiah($saldo_akhir); ?></h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 mb-8 p-6">
        <form action="index.php" method="GET" id="filter-form">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                <div class="md:col-span-5">
                    <label for="q" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Cari Impian</label>
                    <div class="relative">
                        <input type="text" class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all placeholder-slate-400" id="q" name="q" placeholder="Contoh: Laptop baru..." value="<?php echo htmlspecialchars($search_query); ?>" onchange="this.form.submit()">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-search"></i>
                        </div>
                    </div>
                </div>
                <div class="md:col-span-3">
                    <label for="status" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Status</label>
                    <div class="relative">
                        <select class="w-full pl-3 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all appearance-none cursor-pointer text-slate-700 font-medium" id="status" name="status" onchange="this.form.submit()">
                            <option value="">Semua Status</option>
                            <option value="Aktif" <?php echo ($status_filter == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                            <option value="Selesai" <?php echo ($status_filter == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>
                <div class="md:col-span-3">
                    <label for="prioritas" class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Prioritas</label>
                    <div class="relative">
                        <select class="w-full pl-3 pr-10 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all appearance-none cursor-pointer text-slate-700 font-medium" id="prioritas" name="prioritas" onchange="this.form.submit()">
                            <option value="">Semua Prioritas</option>
                            <option value="Tinggi" <?php echo ($prioritas_filter == 'Tinggi') ? 'selected' : ''; ?>>Tinggi</option>
                            <option value="Sedang" <?php echo ($prioritas_filter == 'Sedang') ? 'selected' : ''; ?>>Sedang</option>
                            <option value="Rendah" <?php echo ($prioritas_filter == 'Rendah') ? 'selected' : ''; ?>>Rendah</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none text-slate-400">
                            <i class="bi bi-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>
                <div class="md:col-span-1">
                    <a href="index.php" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-600 font-medium rounded-xl hover:bg-slate-50 hover:text-rose-600 transition-colors text-sm" title="Reset Filter">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- Wishlist Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php if (empty($wishlist_items)): ?>
            <div class="col-span-full">
                <div class="text-center p-12 bg-white rounded-2xl shadow-sm border border-slate-200">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-slate-100 rounded-full mb-4 text-slate-400">
                        <i class="bi bi-search text-4xl"></i>
                    </div>
                    <h5 class="text-xl font-bold text-slate-900 mb-2">Tidak Ada Impian yang Ditemukan</h5>
                    <p class="text-slate-500 mb-6">Coba ubah filter pencarianmu atau tambahkan impian baru.</p>
                    <button type="button" class="inline-flex items-center gap-2 px-6 py-2.5 bg-purple-600 text-white font-medium rounded-xl hover:bg-purple-700 transition-colors" onclick="document.getElementById('tambahWishlistModal').classList.remove('hidden')">
                        <i class="bi bi-plus-lg"></i>
                        Tambah Impian
                    </button>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($wishlist_items as $goal):
                $target = (float) $goal['harga'];
                $terkumpul = min($saldo_akhir, $target);
                $persentase = ($target > 0) ? ($terkumpul / $target) * 100 : 0;
                if ($goal['status'] === 'Selesai') {
                    $terkumpul = $target;
                    $persentase = 100;
                }
                
                $badge_color = 'bg-slate-100 text-slate-700';
                if ($goal['prioritas'] == 'Tinggi') $badge_color = 'bg-rose-100 text-rose-700';
                else if ($goal['prioritas'] == 'Sedang') $badge_color = 'bg-yellow-100 text-yellow-700';
                else if ($goal['prioritas'] == 'Rendah') $badge_color = 'bg-blue-100 text-blue-700';
            ?>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 hover:shadow-lg transition-all duration-300 flex flex-col h-full group overflow-hidden">
                <div class="p-6 flex flex-col flex-grow">
                    <div class="flex justify-between items-start mb-4">
                        <h5 class="text-lg font-bold text-slate-900 line-clamp-2 group-hover:text-purple-600 transition-colors"><?php echo htmlspecialchars($goal['nama_barang']); ?></h5>
                        <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-bold <?php echo $badge_color; ?>">
                            <?php echo htmlspecialchars($goal['prioritas']); ?>
                        </span>
                    </div>
                    
                    <div class="mb-6">
                        <p class="text-xs text-slate-500 uppercase tracking-wide mb-1 font-bold">Target Harga</p>
                        <p class="text-2xl font-bold text-slate-900"><?php echo formatRupiah($target); ?></p>
                    </div>

                    <div class="mt-auto">
                        <div class="flex justify-between items-end mb-2">
                            <span class="text-sm font-bold <?php echo $persentase >= 100 ? 'text-emerald-600' : 'text-slate-700'; ?>">
                                <?php echo floor($persentase); ?>%
                            </span>
                            <span class="text-xs text-slate-500 font-medium">
                                <?php echo formatRupiah($terkumpul); ?> / <?php echo formatRupiah($target); ?>
                            </span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-3 overflow-hidden">
                            <div class="bg-linear-to-r from-emerald-500 to-teal-500 h-3 rounded-full transition-all duration-1000 ease-out" style="width: <?php echo $persentase; ?>%"></div>
                        </div>
                    </div>
                </div>

                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-between gap-2">
                    <div class="flex gap-2">
                        <button type="button" class="p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit"
                            onclick='openEditWishlistModal(<?php echo htmlspecialchars(json_encode([
                                "id" => $goal["id_wishlist"],
                                "nama" => $goal["nama_barang"],
                                "harga" => $goal["harga"],
                                "prioritas" => $goal["prioritas"]
                            ]), ENT_QUOTES, 'UTF-8'); ?>)'>
                            <i class="bi bi-pencil-square text-lg"></i>
                        </button>
                        <a href="#" onclick="confirmDelete('hapus.php?id=<?php echo $goal['id_wishlist']; ?>')" class="p-2 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                            <i class="bi bi-trash text-lg"></i>
                        </a>
                    </div>
                    
                    <?php if ($goal['status'] === 'Selesai'): ?>
                        <span class="inline-flex items-center rounded-lg bg-emerald-100 px-3 py-1.5 text-xs font-bold text-emerald-700">
                            <i class="bi bi-check-circle-fill mr-1.5"></i> Tercapai
                        </span>
                    <?php else: ?>
                        <a href="selesai.php?id=<?php echo $goal['id_wishlist']; ?>" class="inline-flex items-center gap-1.5 text-sm font-bold text-emerald-600 hover:text-emerald-700 hover:bg-emerald-50 px-3 py-1.5 rounded-lg transition-colors">
                            <i class="bi bi-check2-circle text-lg"></i> Tandai Selesai
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_halaman > 1): ?>
    <div class="mt-8 flex justify-center">
        <nav class="inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
            <?php
            $query_params = $_GET;
            
            // Previous
            if ($halaman > 1) {
                $query_params['halaman'] = $halaman - 1;
                echo '<a href="?' . http_build_query($query_params) . '" class="relative inline-flex items-center rounded-l-md px-3 py-2 text-slate-400 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:z-20 focus:outline-offset-0"><span class="sr-only">Previous</span><i class="bi bi-chevron-left"></i></a>';
            } else {
                echo '<span class="relative inline-flex items-center rounded-l-md px-3 py-2 text-slate-300 ring-1 ring-inset ring-slate-300 cursor-not-allowed"><span class="sr-only">Previous</span><i class="bi bi-chevron-left"></i></span>';
            }

            // Numbers
            for ($i = 1; $i <= $total_halaman; $i++) {
                $query_params['halaman'] = $i;
                if ($i == $halaman) {
                    echo '<a href="#" aria-current="page" class="relative z-10 inline-flex items-center bg-purple-600 px-4 py-2 text-sm font-semibold text-white focus:z-20 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-purple-600">' . $i . '</a>';
                } else {
                    echo '<a href="?' . http_build_query($query_params) . '" class="relative inline-flex items-center px-4 py-2 text-sm font-semibold text-slate-900 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:z-20 focus:outline-offset-0">' . $i . '</a>';
                }
            }

            // Next
            if ($halaman < $total_halaman) {
                $query_params['halaman'] = $halaman + 1;
                echo '<a href="?' . http_build_query($query_params) . '" class="relative inline-flex items-center rounded-r-md px-3 py-2 text-slate-400 ring-1 ring-inset ring-slate-300 hover:bg-slate-50 focus:z-20 focus:outline-offset-0"><span class="sr-only">Next</span><i class="bi bi-chevron-right"></i></a>';
            } else {
                echo '<span class="relative inline-flex items-center rounded-r-md px-3 py-2 text-slate-300 ring-1 ring-inset ring-slate-300 cursor-not-allowed"><span class="sr-only">Next</span><i class="bi bi-chevron-right"></i></span>';
            }
            ?>
        </nav>
    </div>
    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/../modal/wishlist_modal.php'; ?>
<?php require_once __DIR__ . '/../layout/footer.php'; ?>