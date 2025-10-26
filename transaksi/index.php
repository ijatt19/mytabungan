<?php
require_once __DIR__ . '/../layout/header.php';
require_once __DIR__ . '/../layout/sidebar.php';
require_once __DIR__ . '/../auth/cek_masuk.php';

$id_pengguna = $_SESSION['id_pengguna'];

// Logika untuk Tambah & Edit via Modal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id_kategori = $_POST['id_kategori'];
    $jumlah = $_POST['jumlah'];
    $keterangan = $_POST['keterangan'];
    $tanggal_transaksi = $_POST['tanggal_transaksi'];

    // Validasi dasar
    if (empty($id_kategori) || empty($jumlah) || empty($tanggal_transaksi) || !is_numeric($jumlah) || $jumlah <= 0) {
        $_SESSION['pesan_error'] = "Data tidak valid. Pastikan semua field terisi dengan benar.";
    } else {
        if ($action === 'tambah') {
            try {
                $stmt = $pdo->prepare("INSERT INTO transaksi (id_pengguna, id_kategori, jumlah, keterangan, tanggal_transaksi) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$id_pengguna, $id_kategori, $jumlah, $keterangan, $tanggal_transaksi]);
                $_SESSION['pesan_sukses'] = 'Transaksi berhasil ditambahkan.';
            } catch (PDOException $e) {
                $_SESSION['pesan_error'] = "Gagal menyimpan: " . $e->getMessage();
            }
        } elseif ($action === 'edit') {
            $id_transaksi = $_POST['id_transaksi'];
            try {
                $stmt = $pdo->prepare("UPDATE transaksi SET id_kategori = ?, jumlah = ?, keterangan = ?, tanggal_transaksi = ? WHERE id_transaksi = ? AND id_pengguna = ?");
                $stmt->execute([$id_kategori, $jumlah, $keterangan, $tanggal_transaksi, $id_transaksi, $id_pengguna]);
                $_SESSION['pesan_sukses'] = 'Transaksi berhasil diperbarui.';
            } catch (PDOException $e) {
                $_SESSION['pesan_error'] = "Gagal memperbarui: " . $e->getMessage();
            }
        }
    }
    header('Location: index.php?' . http_build_query($_GET)); // Kembali ke halaman dengan filter aktif
    exit;
}

// Logika Filter
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

try {
    // Logika Pagination
    $limit = 10;
    $halaman = $_GET['halaman'] ?? 1;
    $offset = ($halaman - 1) * $limit;

    // Hitung total data untuk pagination
    $sql_count = "SELECT COUNT(*) FROM transaksi t WHERE t.id_pengguna = :id_pengguna $where_filter";
    $stmt_count = $pdo->prepare($sql_count);
    $stmt_count->execute($params);
    $total_data = $stmt_count->fetchColumn();
    $total_halaman = ceil($total_data / $limit);


    $sql = "SELECT
                t.id_transaksi,
                t.jumlah,
                t.id_kategori,
                t.keterangan,
                t.tanggal_transaksi,
                k.nama_kategori,
                k.tipe AS tipe_kategori
            FROM
                transaksi t
            JOIN
                kategori k ON t.id_kategori = k.id_kategori
            WHERE
                t.id_pengguna = :id_pengguna
                $where_filter
            ORDER BY
                t.tanggal_transaksi DESC, t.id_transaksi DESC
            LIMIT :limit OFFSET :offset";
                
    $stmt = $pdo->prepare($sql);

    // Bind parameter pagination
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

    // Bind parameter filter
    foreach ($params as $key => &$val) {
        $stmt->bindValue($key, $val);
    }

    $stmt->execute();
    $transaksi = $stmt->fetchAll();

    // Ambil daftar kategori untuk dropdown filter
    $stmt_kategori = $pdo->prepare("SELECT * FROM kategori WHERE id_pengguna = ? ORDER BY tipe, nama_kategori");
    $stmt_kategori->execute([$id_pengguna]);
    $daftar_kategori = $stmt_kategori->fetchAll();

} catch (PDOException $e) {
    $_SESSION['pesan_error'] = "Error mengambil data: " . $e->getMessage();
}
?>

<main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">Daftar Transaksi</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" class="btn btn-primary rounded-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#tambahTransaksiModal">
                <i class="bi bi-plus-lg me-2"></i>
                Tambah Transaksi
            </button>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="index.php" method="GET" id="filter-form">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="date" class="form-control" id="tgl_mulai" name="tgl_mulai" value="<?php echo htmlspecialchars($tgl_mulai_filter); ?>" title="Tanggal Mulai">
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" id="tgl_selesai" name="tgl_selesai" value="<?php echo htmlspecialchars($tgl_selesai_filter); ?>" title="Tanggal Selesai">
                    </div>
                    <div class="col-md-4">
                        <select class="form-select" id="id_kategori" name="id_kategori">
                            <option value="">Semua Kategori</option>
                            <?php
                            $grouped_kategori = [];
                            foreach ($daftar_kategori as $k) {
                                $grouped_kategori[$k['tipe']][] = $k;
                            }

                            foreach ($grouped_kategori as $tipe => $kategoris) {
                                echo '<optgroup label="' . htmlspecialchars($tipe) . '">';
                                foreach ($kategoris as $k) {
                                    $selected = ($kategori_filter == $k['id_kategori']) ? 'selected' : '';
                                    echo "<option value='{$k['id_kategori']}' {$selected}>" . htmlspecialchars($k['nama_kategori']) . "</option>";
                                }
                                echo '</optgroup>';
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <a href="index.php" class="btn btn-outline-secondary w-100"><i class="bi bi-arrow-repeat me-2"></i> Reset</a>
                    </div>
                </div>
            </form>
        </div>
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle mb-0">
                <thead>
                    <tr>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Kategori</th>
                        <th scope="col">Keterangan</th>
                        <th scope="col" class="text-end">Jumlah (Rp)</th>
                        <th scope="col" class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($transaksi)) : ?>
                        <tr>
                            <td colspan="5">
                                <div class="empty-state">
                                    <i class="bi bi-journal-text"></i>
                                    <h5>Belum Ada Transaksi</h5>
                                    <p>Tidak ada data untuk ditampilkan. Coba ubah filter atau tambahkan transaksi baru.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else : ?>
                        <?php foreach ($transaksi as $t) : ?>
                            <tr>
                                <td><?php echo date('d M Y', strtotime($t['tanggal_transaksi'])); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo ($t['tipe_kategori'] == 'Pemasukan') ? 'success-subtle text-success-emphasis' : 'danger-subtle text-danger-emphasis'; ?>">
                                        <?php echo htmlspecialchars($t['nama_kategori']); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($t['keterangan']); ?></td>
                                <td class="text-end fw-bold">
                                    <?php
                                    $isPemasukan = ($t['tipe_kategori'] == 'Pemasukan');
                                    $warna = $isPemasukan ? 'text-success' : 'text-danger';
                                    $prefix = $isPemasukan ? '+' : '-';
                                    ?>
                                    <span class="<?php echo $warna; ?>">
                                        <?php echo $prefix . " " . number_format($t['jumlah'], 0, ',', '.'); ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-action btn-outline-warning" title="Edit"
                                        data-bs-toggle="modal"
                                        data-bs-target="#editTransaksiModal"
                                        data-id="<?php echo $t['id_transaksi']; ?>"
                                        data-tanggal="<?php echo $t['tanggal_transaksi']; ?>"
                                        data-kategori-id="<?php echo $t['id_kategori']; ?>"
                                        data-jumlah="<?php echo $t['jumlah']; ?>"
                                        data-keterangan="<?php echo htmlspecialchars($t['keterangan']); ?>">
                                        <i class="bi bi-pencil-square"></i>
                                    </button>

                                    <button type="button" class="btn btn-action btn-outline-danger" title="Hapus"
                                        data-bs-toggle="modal"
                                        data-bs-target="#konfirmasiModal"
                                        data-url="hapus.php?id=<?php echo $t['id_transaksi']; ?>">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php if ($total_halaman > 1): ?>
        <div class="card-footer">
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center mb-0">
                    <?php
                    // Buat URL dasar untuk pagination
                    $query_params = $_GET;
                    
                    // Tombol Previous
                    if ($halaman > 1) {
                        $query_params['halaman'] = $halaman - 1;
                        echo '<li class="page-item"><a class="page-link" href="?' . http_build_query($query_params) . '">Previous</a></li>';
                    } else {
                        echo '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
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
                    } else {
                        echo '<li class="page-item disabled"><span class="page-link">Next</span></li>';
                    }
                    ?>
                </ul>
            </nav>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/../modal/transaksi_modal.php'; ?>

<?php
require_once __DIR__ . '/../layout/footer.php';
?>
<script src="/js/transaksi.js"></script>