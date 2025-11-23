<?php
require_once __DIR__ . '/auth/cek_masuk.php';
require_once __DIR__ . '/layout/header.php';

$id_pengguna = $_SESSION['id_pengguna'];
$nama_pengguna = $_SESSION['nama_lengkap'] ?? 'Pengguna';

// Data for cards and chart
$total_pemasukan = 0;
$total_pengeluaran = 0;
$saldo_akhir = 0;
$recent_transactions = [];

try {
    // Fetch data for summary cards
    $sql_pemasukan = "SELECT SUM(jumlah) AS total FROM transaksi t JOIN kategori k ON t.id_kategori = k.id_kategori WHERE t.id_pengguna = ? AND k.tipe = 'Pemasukan'";
    $stmt_pemasukan = $pdo->prepare($sql_pemasukan);
    $stmt_pemasukan->execute([$id_pengguna]);
    $total_pemasukan = $stmt_pemasukan->fetch()['total'] ?? 0;

    $sql_pengeluaran = "SELECT SUM(jumlah) AS total FROM transaksi t JOIN kategori k ON t.id_kategori = k.id_kategori WHERE t.id_pengguna = ? AND k.tipe = 'Pengeluaran'";
    $stmt_pengeluaran = $pdo->prepare($sql_pengeluaran);
    $stmt_pengeluaran->execute([$id_pengguna]);
    $total_pengeluaran = $stmt_pengeluaran->fetch()['total'] ?? 0;

    $saldo_akhir = $total_pemasukan - $total_pengeluaran;

    // Fetch recent transactions (last 5)
    $sql_recent = "SELECT t.tanggal_transaksi, t.keterangan, k.nama_kategori, k.tipe, t.jumlah 
                   FROM transaksi t
                   JOIN kategori k ON t.id_kategori = k.id_kategori 
                   WHERE t.id_pengguna = ? 
                   ORDER BY t.tanggal_transaksi DESC, t.id_transaksi DESC
                   LIMIT 5";
    $stmt_recent = $pdo->prepare($sql_recent);
    $stmt_recent->execute([$id_pengguna]);
    $recent_transactions = $stmt_recent->fetchAll();

    // Ambil filter prioritas dari URL, defaultnya kosong (tampilkan semua)
    $priority_filter = $_GET['priority_filter'] ?? '';

    // Ambil data untuk "Impian Terdekat" dengan filter
    $sql_wishlist = "SELECT * FROM wishlist WHERE id_pengguna = ? AND status = 'Aktif'";
    $params_wishlist = [$id_pengguna];

    if (!empty($priority_filter)) {
        $sql_wishlist .= " AND prioritas = ?";
        $params_wishlist[] = $priority_filter;
    }
    $sql_wishlist .= " ORDER BY FIELD(prioritas, 'Tinggi', 'Sedang', 'Rendah'), harga ASC";
    $stmt_wishlist = $pdo->prepare($sql_wishlist);
    $stmt_wishlist->execute($params_wishlist);
    $next_goals = $stmt_wishlist->fetchAll();

    require_once __DIR__ . '/includes/chart_data.php';

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$has_transactions = !empty($recent_transactions);

?>

<?php require_once __DIR__ . '/layout/sidebar.php'; ?>

<main class="px-4 py-4">

    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center mb-3">
        <h1 class="h2">Dashboard</h1>
        <div class="btn-toolbar mb-2 mb-md-0">
            <button type="button" id="generateShareLinkBtn" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#shareLinkModal">
                <i class="bi bi-share-fill"></i> Bagikan Laporan
            </button>
        </div>
    </div>
    <p class="text-muted">Selamat datang kembali, <?php echo htmlspecialchars($nama_pengguna); ?>!</p>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Pemasukan</h6>
                    <h3 class="text-success mb-0">Rp <?php echo number_format($total_pemasukan, 0, ',', '.'); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Total Pengeluaran</h6>
                    <h3 class="text-danger mb-0">Rp <?php echo number_format($total_pengeluaran, 0, ',', '.'); ?></h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h6 class="text-muted mb-2">Saldo Akhir</h6>
                    <h3 class="<?php echo $saldo_akhir >= 0 ? 'text-primary' : 'text-danger'; ?> mb-0">
                        Rp <?php echo number_format($saldo_akhir, 0, ',', '.'); ?>
                    </h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0">Grafik Pemasukan & Pengeluaran</h5>
        </div>
        <div class="card-body">
            <div style="height: 300px;">
                <canvas id="myChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Transaksi Terakhir</h5>
        </div>
        <div class="card-body">
            <?php if ($has_transactions): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Keterangan</th>
                                <th>Kategori</th>
                                <th>Tipe</th>
                                <th class="text-end">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_transactions as $transaction): ?>
                                <tr>
                                    <td><?php echo date('d M Y', strtotime($transaction['tanggal_transaksi'])); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['keterangan']); ?></td>
                                    <td><?php echo htmlspecialchars($transaction['nama_kategori']); ?></td>
                                    <td>
                                        <span class="badge <?php echo $transaction['tipe'] === 'Pemasukan' ? 'bg-success' : 'bg-danger'; ?>">
                                            <?php echo $transaction['tipe']; ?>
                                        </span>
                                    </td>
                                    <td class="text-end <?php echo $transaction['tipe'] === 'Pemasukan' ? 'text-success' : 'text-danger'; ?>">
                                        Rp <?php echo number_format($transaction['jumlah'], 0, ',', '.'); ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p class="text-muted text-center py-4">Belum ada transaksi.</p>
            <?php endif; ?>
        </div>
    </div>

</main>

<script>
    // Chart data untuk dashboard.js
    const chartData = {
        labels: <?php echo json_encode($labels); ?>,
        pemasukan: <?php echo json_encode($pemasukan_data); ?>,
        pengeluaran: <?php echo json_encode($pengeluaran_data); ?>
    };
</script>
<script src="/js/dashboard.js"></script>


<?php
require_once __DIR__ . '/layout/footer.php';
?>