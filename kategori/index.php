<?php
require_once __DIR__ . '/../config/koneksi.php';

// API endpoint untuk cek nama kategori (AJAX)
if (isset($_GET['action']) && $_GET['action'] === 'check_name') {
    header('Content-Type: application/json');
    if (!isset($_SESSION['id_pengguna'])) {
        echo json_encode(['exists' => false, 'error' => 'Unauthorized']);
        exit;
    }

    $id_pengguna = $_SESSION['id_pengguna'];
    $nama_kategori = $_GET['nama_kategori'] ?? '';
    $exclude_id = $_GET['exclude_id'] ?? 0;

    $sql = "SELECT id_kategori FROM kategori WHERE nama_kategori = ? AND id_pengguna = ?";
    $params = [$nama_kategori, $id_pengguna];

    if ($exclude_id > 0) {
        $sql .= " AND id_kategori != ?";
        $params[] = $exclude_id;
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo json_encode(['exists' => $stmt->fetch() !== false]);
    exit; // Hentikan eksekusi agar tidak merender sisa halaman HTML
}

require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
require_once __DIR__ . '/../auth/cek_masuk.php';

$id_pengguna = $_SESSION['id_pengguna'];

// Logika untuk Tambah, Edit, dan Hapus
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'tambah') {
        $nama_kategori = trim($_POST['nama_kategori']);
        $tipe = $_POST['tipe'];
        if (!empty($nama_kategori) && !empty($tipe)) {
            // Cek duplikasi nama kategori sebelum menambah
            $stmt_cek = $pdo->prepare("SELECT id_kategori FROM kategori WHERE nama_kategori = ? AND id_pengguna = ?");
            $stmt_cek->execute([$nama_kategori, $id_pengguna]);
            if ($stmt_cek->fetch()) {
                $_SESSION['pesan_error'] = "Gagal menambah. Kategori dengan nama '{$nama_kategori}' sudah ada.";
            } else {
                $stmt = $pdo->prepare("INSERT INTO kategori (nama_kategori, tipe, id_pengguna) VALUES (?, ?, ?)");
                $stmt->execute([$nama_kategori, $tipe, $id_pengguna]);
                $_SESSION['pesan_sukses'] = 'Kategori baru berhasil ditambahkan.';
            }
        } else {
            $_SESSION['pesan_error'] = 'Nama kategori dan tipe wajib diisi.';
        }
    } elseif ($action === 'edit') {
        $id_kategori = $_POST['id_kategori'];
        $nama_kategori = trim($_POST['nama_kategori']);
        $tipe = $_POST['tipe'];
        if (!empty($nama_kategori) && !empty($tipe) && !empty($id_kategori)) {
            // Cek duplikasi nama, kecuali untuk kategori yang sedang diedit itu sendiri
            $stmt_cek = $pdo->prepare("SELECT id_kategori FROM kategori WHERE nama_kategori = ? AND id_pengguna = ? AND id_kategori != ?");
            $stmt_cek->execute([$nama_kategori, $id_pengguna, $id_kategori]);
            if ($stmt_cek->fetch()) {
                $_SESSION['pesan_error'] = "Gagal memperbarui. Kategori dengan nama '{$nama_kategori}' sudah ada.";
            } else {
                $stmt = $pdo->prepare("UPDATE kategori SET nama_kategori = ?, tipe = ? WHERE id_kategori = ? AND id_pengguna = ?");
                $stmt->execute([$nama_kategori, $tipe, $id_kategori, $id_pengguna]);
                $_SESSION['pesan_sukses'] = 'Kategori berhasil diperbarui.';
            }
        } else {
            $_SESSION['pesan_error'] = 'Semua field wajib diisi.';
        }
    }
    header('Location: index.php?' . http_build_query($_GET)); // Kembali ke halaman dengan filter & pagination aktif
    exit;
}

// Logika Filter & Pagination
$search_query = $_GET['q'] ?? '';
$tipe_filter = $_GET['tipe'] ?? '';
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
    // Logika Pagination
    $limit = 5; // Jumlah item per halaman
    $halaman = $_GET['halaman'] ?? 1;
    $offset = ($halaman - 1) * $limit;

    // Hitung total data untuk pagination
    $sql_count = "SELECT COUNT(*) FROM kategori WHERE id_pengguna = :id_pengguna $where_filter";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute($params);
    $total_data = $stmt_count->fetchColumn();
    $total_halaman = ceil($total_data / $limit);
    $jumlah_kategori = $total_data; // Total semua kategori

    // Ambil data kategori dengan limit dan offset
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
    $total_halaman = 0;
}
?>

<main class="px-4 py-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 fw-bold">Kelola Kategori</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-primary rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahKategoriModal">
                <i class="bi bi-plus-lg me-2"></i>
                Tambah Kategori
            </button>
        </div>
    </div>

    <!-- Stats and Filter Cards -->
    <div class="row mb-4 align-items-end">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-circle bg-light-green text-success-dark me-3">
                        <i class="bi bi-tags-fill"></i>
                    </div>
                    <div>
                        <h6 class="card-subtitle text-muted">Jumlah Kategori</h6>
                        <h4 class="card-title fw-bold"><?php echo $jumlah_kategori; ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <form action="index.php" method="GET" id="filter-form">
                <input type="hidden" name="q" value="<?php echo htmlspecialchars($search_query); ?>">
                <div>
                    <label for="tipe_filter" class="form-label">Filter berdasarkan Tipe</label>
                    <select class="form-select" id="tipe_filter" name="tipe" onchange="this.form.submit()">
                        <option value="">Semua Tipe</option>
                        <option value="Pemasukan" <?php echo ($tipe_filter == 'Pemasukan') ? 'selected' : ''; ?>>Pemasukan</option>
                        <option value="Pengeluaran" <?php echo ($tipe_filter == 'Pengeluaran') ? 'selected' : ''; ?>>Pengeluaran</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Main Table Card -->
    <div class="card shadow-sm rounded-4 border-0">
        <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Daftar Kategori</h5>
            <form action="index.php" method="GET" class="w-25" id="search-form">
                <input type="hidden" name="tipe" value="<?php echo htmlspecialchars($tipe_filter); ?>">
                <div class="input-group input-group-sm">
                    <input type="text" id="searchInput" name="q" class="form-control rounded-pill" placeholder="Cari kategori..." value="<?php echo htmlspecialchars($search_query); ?>">
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-borderless align-middle mb-0" id="kategoriTable">
                    <thead>
                        <tr>
                            <th class="p-3">Nama Kategori</th>
                            <th class="p-3">Tipe</th>
                            <th class="p-3 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($kategori)): ?>
                            <tr>
                                <td colspan="3">
                                    <div class="empty-state">
                                        <i class="bi bi-tags"></i>
                                        <h5>Belum Ada Kategori</h5>
                                        <p>Buat kategori baru untuk mulai mengelompokkan transaksimu.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($kategori as $k): ?>
                                <tr>
                                    <td class="p-3"><?php echo htmlspecialchars($k['nama_kategori']); ?></td>
                                    <td class="p-3">
                                        <span class="badge <?php echo ($k['tipe'] == 'Pemasukan') ? 'badge-pemasukan' : 'badge-pengeluaran'; ?>"><?php echo htmlspecialchars($k['tipe']); ?></span>
                                    </td>
                                    <td class="p-3 text-end">
                                        <button type="button" class="btn btn-action btn-outline-warning" title="Edit"
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editKategoriModal"
                                            data-id="<?php echo $k['id_kategori']; ?>"
                                            data-nama="<?php echo htmlspecialchars($k['nama_kategori']); ?>"
                                            data-tipe="<?php echo $k['tipe']; ?>">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <button type="button" class="btn btn-action btn-outline-danger" title="Hapus"
                                            data-bs-toggle="modal"
                                            data-bs-target="#konfirmasiModal"
                                            data-url="hapus.php?id=<?php echo $k['id_kategori']; ?>">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                        <tr id="no-results" style="display: none;">
                            <td colspan="3" class="text-center p-5">
                                <p class="mb-0">Tidak ada kategori yang cocok dengan pencarian Anda.</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if ($total_halaman > 1): ?>
        <div class="card-footer bg-white">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mb-0">
                    <?php
                    $query_params = $_GET;
                    
                    // Tombol Previous
                    if ($halaman > 1) {
                        $query_params['halaman'] = $halaman - 1;
                        echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($query_params) . '">Previous</a></li>';
                    }

                    // Tampilkan link halaman
                    for ($i = 1; $i <= $total_halaman; $i++) {
                        $query_params['halaman'] = $i;
                        $active = ($i == $halaman) ? 'active' : '';
                        echo '<li class="page-item ' . $active . '"><a class="page-link" href="?' . http_build_query($query_params) . '">' . $i . '</a></li>';
                    }

                    // Tombol Next
                    if ($halaman < $total_halaman) {
                        $query_params['halaman'] = $halaman + 1;
                        echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($query_params) . '">Next</a></li>';
                    } ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../modal/kategori_modal.php'; ?>

<?php
require_once __DIR__ . '/../layout/footer.php';
?>
<script src="/js/kategori.js"></script>