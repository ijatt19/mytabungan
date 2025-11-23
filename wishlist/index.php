<?php
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
require_once __DIR__ . '/../auth/cek_masuk.php';

$id_pengguna = $_SESSION['id_pengguna'];

// Logika untuk Tambah & Edit Wishlist (POST request)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $nama_barang = trim($_POST['nama_barang']);
        $harga = $_POST['harga'];
        $prioritas = $_POST['prioritas'];

        if (empty($nama_barang) || empty($harga) || empty($prioritas)) {
            $_SESSION['pesan_error'] = 'Semua kolom wajib diisi.';
        } elseif (!is_numeric($harga) || $harga < 0) {
            $_SESSION['pesan_error'] = 'Harga harus berupa angka yang valid.';
        } else {
            try {
                $sql = "INSERT INTO wishlist (id_pengguna, nama_barang, harga, prioritas, status, dibuat_pada) VALUES (?, ?, ?, ?, 'Belum Tercapai', NOW())";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$id_pengguna, $nama_barang, $harga, $prioritas]);
                $_SESSION['pesan_sukses'] = 'Impian baru berhasil ditambahkan!';
            } catch (PDOException $e) {
                $_SESSION['pesan_error'] = 'Terjadi kesalahan database: ' . $e->getMessage();
            }
        }
        header('Location: index.php');
        exit;

    } elseif ($_POST['action'] === 'edit') {
        $id_wishlist = $_POST['id_wishlist'];
        $nama_barang = trim($_POST['nama_barang']);
        $harga = $_POST['harga'];
        $prioritas = $_POST['prioritas'];

        if (empty($nama_barang) || empty($harga) || empty($prioritas) || empty($id_wishlist)) {
            $_SESSION['pesan_error'] = 'Gagal memperbarui, semua kolom wajib diisi.';
        } elseif (!is_numeric($harga) || $harga < 0) {
            $_SESSION['pesan_error'] = 'Harga harus berupa angka yang valid.';
        } else {
            try {
                $sql = "UPDATE wishlist SET nama_barang = ?, harga = ?, prioritas = ? WHERE id_wishlist = ? AND id_pengguna = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$nama_barang, $harga, $prioritas, $id_wishlist, $id_pengguna]);
                $_SESSION['pesan_sukses'] = 'Impian berhasil diperbarui.';
            } catch (PDOException $e) {
                $_SESSION['pesan_error'] = 'Terjadi kesalahan database: ' . $e->getMessage();
            }
        }
        header('Location: index.php?' . http_build_query($_GET));
        exit;
    }
}

// Logika Filter & Pencarian
$search_query = $_GET['q'] ?? '';
$status_filter = $_GET['status'] ?? '';
$prioritas_filter = $_GET['prioritas'] ?? '';

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

try {
    // Logika Pagination
    $limit = 6; // 6 kartu per halaman
    $halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
    $offset = ($halaman - 1) * $limit;

    // Hitung total data untuk pagination
    $sql_count = "SELECT COUNT(*) FROM wishlist WHERE id_pengguna = :id_pengguna $where_sql";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute($params);
    $total_data = $stmt_count->fetchColumn();
    $total_halaman = ceil($total_data / $limit);

    // Ambil data wishlist dengan filter dan pagination
    $sql = "SELECT * FROM wishlist WHERE id_pengguna = :id_pengguna $where_sql ORDER BY FIELD(prioritas, 'Tinggi', 'Sedang', 'Rendah'), dibuat_pada DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);

    // Bind parameter filter
    foreach ($params as $key => &$val) {
        $stmt->bindValue($key, $val);
    }
    // Bind parameter pagination
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    $stmt->execute();
    $wishlist_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Ambil saldo akhir untuk perhitungan progress
    $stmt_saldo = $pdo->prepare("SELECT (SELECT SUM(jumlah) FROM transaksi t JOIN kategori k ON t.id_kategori = k.id_kategori WHERE t.id_pengguna = ? AND k.tipe = 'Pemasukan') - (SELECT SUM(jumlah) FROM transaksi t JOIN kategori k ON t.id_kategori = k.id_kategori WHERE t.id_pengguna = ? AND k.tipe = 'Pengeluaran') AS saldo");
    $stmt_saldo->execute([$id_pengguna, $id_pengguna]);
    $saldo_akhir = $stmt_saldo->fetchColumn() ?? 0;

} catch (PDOException $e) {
    $_SESSION['pesan_error'] = "Gagal mengambil data wishlist: " . $e->getMessage();
    $wishlist_items = [];
    $total_halaman = 0;
}
?>

<main class="px-4 py-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2 fw-bold">Daftar Impian</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-primary rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahWishlistModal">
                <i class="bi bi-plus-lg me-2"></i>
                Tambah Impian
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <form action="index.php" method="GET" id="filter-form">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="q" class="form-label">Cari Impian</label>
                        <input type="text" class="form-control" id="q" name="q" placeholder="Contoh: Laptop baru..." value="<?php echo htmlspecialchars($search_query); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="">Semua Status</option>
                            <option value="Aktif" <?php echo ($status_filter == 'Aktif') ? 'selected' : ''; ?>>Aktif</option>
                            <option value="Selesai" <?php echo ($status_filter == 'Selesai') ? 'selected' : ''; ?>>Selesai</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="prioritas" class="form-label">Prioritas</label>
                        <select class="form-select" id="prioritas" name="prioritas">
                            <option value="">Semua Prioritas</option>
                            <option value="Tinggi" <?php echo ($prioritas_filter == 'Tinggi') ? 'selected' : ''; ?>>Tinggi</option>
                            <option value="Sedang" <?php echo ($prioritas_filter == 'Sedang') ? 'selected' : ''; ?>>Sedang</option>
                            <option value="Rendah" <?php echo ($prioritas_filter == 'Rendah') ? 'selected' : ''; ?>>Rendah</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-success w-100">Filter</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Wishlist Grid -->
    <div class="row g-4">
        <?php if (empty($wishlist_items)): ?>
            <div class="col-12">
                <div class="text-center p-5 bg-light rounded-3">
                    <i class="bi bi-search-heart fs-1 text-muted"></i>
                    <h5 class="mt-3">Tidak Ada Impian yang Ditemukan</h5>
                    <p class="text-muted">Coba ubah filter pencarianmu atau <a href="#tambahWishlistModal" data-bs-toggle="modal">tambahkan impian baru</a>.</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($wishlist_items as $goal):
                $target = (float) $goal['harga'];
                // Simulasi dana terkumpul dari saldo utama, dibatasi oleh harga target
                $terkumpul = min($saldo_akhir, $target);
                $persentase = ($target > 0) ? ($terkumpul / $target) * 100 : 0;
                if ($goal['status'] === 'Selesai') {
                    $terkumpul = $target;
                    $persentase = 100;
                }
            ?>
            <div class="col-md-6 col-lg-4">
                <div class="card wishlist-list-card h-100">
                    <div class="card-body d-flex flex-column">
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-start">
                                <h5 class="wishlist-title mb-1"><?php echo htmlspecialchars($goal['nama_barang']); ?></h5>
                                <span class="badge badge-prioritas-<?php echo strtolower($goal['prioritas']); ?>"><?php echo htmlspecialchars($goal['prioritas']); ?></span>
                            </div>
                            <p class="wishlist-target">Target: <?php echo "Rp " . number_format($target, 0, ',', '.'); ?></p>
                        </div>

                        <div class="progress-container mt-auto">
                             <div class="progress-info">
                                <span class="progress-percentage"><?php echo floor($persentase); ?>% Tercapai</span>
                            </div>
                            <div class="progress progress-modern">
                                <div class="progress-bar" role="progressbar" style="width: <?php echo $persentase; ?>%;" aria-valuenow="<?php echo $persentase; ?>"></div>
                            </div>
                            <div class="progress-amount">
                                <span><?php echo "Rp " . number_format($terkumpul, 0, ',', '.'); ?></span> / <span><?php echo "Rp " . number_format($target, 0, ',', '.'); ?></span>
                            </div>
                        </div>

                        <div class="wishlist-actions mt-3">
                            <button type="button" class="btn btn-sm btn-outline-warning edit-wishlist-btn" 
                                data-bs-toggle="modal" 
                                data-bs-target="#editWishlistModal"
                                data-id="<?php echo $goal['id_wishlist']; ?>"
                                data-nama="<?php echo htmlspecialchars($goal['nama_barang']); ?>"
                                data-harga="<?php echo $goal['harga']; ?>"
                                data-prioritas="<?php echo $goal['prioritas']; ?>">Edit</button>
                            <a href="hapus.php?id=<?php echo $goal['id_wishlist']; ?>" class="btn btn-sm btn-outline-danger" onclick="return confirm('Yakin ingin menghapus impian ini?')">Hapus</a>
                            <?php if ($goal['status'] == 'Aktif'): ?>
                                <a href="selesai.php?id=<?php echo $goal['id_wishlist']; ?>" class="btn btn-sm btn-outline-success ms-auto">Tandai Selesai</a>
                            <?php else: ?>
                                <span class="badge bg-success-subtle text-success-emphasis ms-auto p-2">Telah Tercapai</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <?php if ($total_halaman > 1): ?>
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
            <?php
            $query_params = $_GET;
            // Tombol Previous
            if ($halaman > 1) {
                $query_params['halaman'] = $halaman - 1;
                echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($query_params) . '">Previous</a></li>';
            }
            // Link halaman
            for ($i = 1; $i <= $total_halaman; $i++) {
                $query_params['halaman'] = $i;
                $active = ($i == $halaman) ? 'active' : '';
                echo '<li class="page-item ' . $active . '"><a class="page-link" href="?' . http_build_query($query_params) . '">' . $i . '</a></li>';
            }
            // Tombol Next
            if ($halaman < $total_halaman) {
                $query_params['halaman'] = $halaman + 1;
                echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($query_params) . '">Next</a></li>';
            }
            ?>
        </ul>
    </nav>
    <?php endif; ?>

</main>

<?php require_once __DIR__ . '/../modal/wishlist_modal.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editWishlistModal = document.getElementById('editWishlistModal');
    if (editWishlistModal) {
        editWishlistModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const harga = button.getAttribute('data-harga');
            const prioritas = button.getAttribute('data-prioritas');

            const modal = this;
            modal.querySelector('#edit_id_wishlist').value = id;
            modal.querySelector('#edit_nama_barang').value = nama;
            modal.querySelector('#edit_harga').value = harga;
            modal.querySelector('#edit_prioritas').value = prioritas;
        });
    }
});
</script>

<?php
require_once __DIR__ . '/../layout/footer.php';
?>