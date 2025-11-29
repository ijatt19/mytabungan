<?php
require_once __DIR__ . '/../config/koneksi.php';

// Start Session & Check Login
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['id_pengguna'])) {
    header('Location: ../auth/login.php');
    exit;
}

$id_pengguna = $_SESSION['id_pengguna'];

// --- Handle Form Submissions (Edit Only) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../includes/functions.php';

    $action = $_POST['action'] ?? '';
    $id_kategori = cleanInput($_POST['id_kategori']);
    $jumlah = cleanInput($_POST['jumlah']);
    $keterangan = cleanInput($_POST['keterangan']);
    $tanggal_transaksi = cleanInput($_POST['tanggal_transaksi']);

    if (empty($id_kategori) || empty($jumlah) || empty($tanggal_transaksi) || !is_numeric($jumlah) || $jumlah <= 0) {
        $_SESSION['pesan_error'] = "Data tidak valid. Pastikan semua field terisi dengan benar.";
    } else {
        try {
            if ($action === 'edit') {
                $id_transaksi = $_POST['id_transaksi'];
                $stmt = $pdo->prepare("UPDATE transaksi SET id_kategori = ?, jumlah = ?, keterangan = ?, tanggal_transaksi = ? WHERE id_transaksi = ? AND id_pengguna = ?");
                $stmt->execute([$id_kategori, $jumlah, $keterangan, $tanggal_transaksi, $id_transaksi, $id_pengguna]);
                $_SESSION['pesan_sukses'] = 'Transaksi berhasil diupdate.';
            }
        } catch (PDOException $e) {
            $_SESSION['pesan_error'] = "Gagal menyimpan: " . $e->getMessage();
        }
    }
    header('Location: index.php?' . http_build_query($_GET));
    exit;
}

require_once __DIR__ . '/../layout/header_pages.php';
require_once __DIR__ . '/../layout/sidebar.php';

// --- Filter Logic ---
$tgl_mulai_filter = $_GET['tgl_mulai'] ?? '';
$tgl_selesai_filter = $_GET['tgl_selesai'] ?? '';
$kategori_filter = $_GET['id_kategori'] ?? '';

$params = [':id_pengguna' => $id_pengguna];
$where_filter = "";

if (!empty($tgl_mulai_filter)) {
    $where_filter .= " AND t.tanggal_transaksi >= :tgl_mulai";
    $params[':tgl_mulai'] = $tgl_mulai_filter;
}
if (!empty($tgl_selesai_filter)) {
    $where_filter .= " AND t.tanggal_transaksi <= :tgl_selesai";
    $params[':tgl_selesai'] = $tgl_selesai_filter;
}
if (!empty($kategori_filter)) {
    $where_filter .= " AND t.id_kategori = :id_kategori";
    $params[':id_kategori'] = $kategori_filter;
}

// --- Data Fetching ---
try {
    // 1. Calculate Totals based on Filters (Contextual Summary)
    $sql_summary = "SELECT 
                        SUM(CASE WHEN k.tipe = 'Pemasukan' THEN t.jumlah ELSE 0 END) as total_pemasukan,
                        SUM(CASE WHEN k.tipe = 'Pengeluaran' THEN t.jumlah ELSE 0 END) as total_pengeluaran
                    FROM transaksi t
                    JOIN kategori k ON t.id_kategori = k.id_kategori
                    WHERE t.id_pengguna = :id_pengguna $where_filter";
    $stmt_summary = $pdo->prepare($sql_summary);
    $stmt_summary->execute($params);
    $summary = $stmt_summary->fetch();
    
    $filter_pemasukan = $summary['total_pemasukan'] ?? 0;
    $filter_pengeluaran = $summary['total_pengeluaran'] ?? 0;
    $filter_selisih = $filter_pemasukan - $filter_pengeluaran;

    // 2. Pagination
    $limit = 10;
    $halaman = $_GET['halaman'] ?? 1;
    $offset = ($halaman - 1) * $limit;

    $sql_count = "SELECT COUNT(*) FROM transaksi t WHERE t.id_pengguna = :id_pengguna $where_filter";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute($params);
    $total_data = $stmt_count->fetchColumn();
    $total_halaman = ceil($total_data / $limit);

    // 3. Fetch Transactions
    $sql = "SELECT
                t.id_transaksi, t.jumlah, t.id_kategori, t.keterangan, t.tanggal_transaksi,
                k.nama_kategori, k.tipe AS tipe_kategori
            FROM transaksi t
            JOIN kategori k ON t.id_kategori = k.id_kategori
            WHERE t.id_pengguna = :id_pengguna $where_filter
            ORDER BY t.tanggal_transaksi DESC, t.id_transaksi DESC
            LIMIT :limit OFFSET :offset";
                
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    foreach ($params as $key => &$val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->execute();
    $transaksi = $stmt->fetchAll();

    // 4. Fetch Categories for Dropdown
    $stmt_kat = $pdo->prepare("SELECT * FROM kategori WHERE id_pengguna = ? ORDER BY nama_kategori ASC");
    $stmt_kat->execute([$id_pengguna]);
    $daftar_kategori = $stmt_kat->fetchAll();
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<main class="main-content p-6 lg:p-8 transition-all duration-300 ease-in-out ml-0 lg:ml-64">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Transaksi</h1>
            <p class="text-gray-500 text-sm mt-1">Kelola pemasukan dan pengeluaran Anda.</p>
        </div>
        <button onclick="document.getElementById('tambahTransaksiModal').classList.remove('hidden')" class="inline-flex items-center px-4 py-2 bg-emerald-600 text-white text-sm font-medium rounded-lg hover:bg-emerald-700 focus:ring-4 focus:ring-emerald-100 transition-all shadow-sm hover:shadow-md">
            <i class="bi bi-plus-lg mr-2"></i> Tambah Transaksi
        </button>
    </div>

    <!-- Flash Messages -->
    <!-- Flash Messages handled by Toast in footer -->

    <!-- Contextual Summary Cards (Based on Filters) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Pemasukan Card -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600">
                <i class="bi bi-arrow-down-left text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Pemasukan</p>
                <h3 class="text-xl font-bold text-gray-900"><?php echo formatRupiah($filter_pemasukan); ?></h3>
            </div>
        </div>

        <!-- Pengeluaran Card -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                <i class="bi bi-arrow-up-right text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Total Pengeluaran</p>
                <h3 class="text-xl font-bold text-gray-900"><?php echo formatRupiah($filter_pengeluaran); ?></h3>
            </div>
        </div>

        <!-- Selisih Card -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
            <div class="w-12 h-12 rounded-full <?php echo $filter_selisih >= 0 ? 'bg-blue-100 text-blue-600' : 'bg-orange-100 text-orange-600'; ?> flex items-center justify-center">
                <i class="bi bi-wallet2 text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium">Selisih (Cashflow)</p>
                <h3 class="text-xl font-bold <?php echo $filter_selisih >= 0 ? 'text-blue-600' : 'text-orange-600'; ?>">
                    <?php echo formatRupiah($filter_selisih); ?>
                </h3>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 mb-8">
        <form action="" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
            <div>
                <label for="tgl_mulai" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Dari Tanggal</label>
                <input type="date" name="tgl_mulai" id="tgl_mulai" value="<?php echo $tgl_mulai_filter; ?>" onchange="this.form.submit()" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
            </div>
            <div>
                <label for="tgl_selesai" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Sampai Tanggal</label>
                <input type="date" name="tgl_selesai" id="tgl_selesai" value="<?php echo $tgl_selesai_filter; ?>" onchange="this.form.submit()" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
            </div>
            <div>
                <label for="id_kategori" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Kategori</label>
                <select name="id_kategori" id="id_kategori" onchange="this.form.submit()" class="w-full px-3 py-2 bg-gray-50 border border-gray-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all">
                    <option value="">Semua Kategori</option>
                    <?php render_kategori_options($daftar_kategori, $kategori_filter); ?>
                </select>
            </div>
            <div class="flex gap-2">
                <a href="index.php" class="w-full px-4 py-2 bg-gray-100 text-gray-600 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors text-center flex items-center justify-center gap-2">
                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Transaction List -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Keterangan</th>
                        <th class="px-6 py-4 text-right text-xs font-semibold text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-4 text-center text-xs font-semibold text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    <?php if (empty($transaksi)): ?>
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="bi bi-inbox text-3xl text-gray-400"></i>
                                    </div>
                                    <p class="text-lg font-medium text-gray-900">Belum ada transaksi</p>
                                    <p class="text-sm text-gray-500 mt-1">Mulai catat keuanganmu hari ini!</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($transaksi as $row): ?>
                            <tr class="hover:bg-gray-50/80 transition-colors group">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <?php echo formatTanggal($row['tanggal_transaksi']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo ($row['tipe_kategori'] == 'Pemasukan') ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'; ?>">
                                        <?php echo htmlspecialchars($row['nama_kategori']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                    <?php echo htmlspecialchars($row['keterangan']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold <?php echo ($row['tipe_kategori'] == 'Pemasukan') ? 'text-emerald-600' : 'text-red-600'; ?>">
                                    <?php echo ($row['tipe_kategori'] == 'Pemasukan' ? '+ ' : '- ') . formatRupiah($row['jumlah']); ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                    <div class="flex justify-center gap-2">
                                        <button onclick="openEditModal(<?php echo htmlspecialchars(json_encode($row), ENT_QUOTES, 'UTF-8'); ?>)" class="text-blue-600 hover:text-blue-800" title="Edit">
                                            <i class="bi bi-pencil-square text-lg"></i>
                                        </button>
                                        <button onclick="confirmDelete('hapus.php?id=<?php echo $row['id_transaksi']; ?>')" class="text-red-600 hover:text-red-800" title="Hapus">
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

<?php 
require_once __DIR__ . '/../modal/transaksi_modal.php';
require_once __DIR__ . '/../layout/footer.php'; 
?>