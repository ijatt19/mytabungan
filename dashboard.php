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

    require_once __DIR__ . '/includes/chart_data.php';

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

$has_transactions = !empty($recent_transactions);

?>

    <div class="container-fluid">
        <div class="row">
            <?php require_once __DIR__ . '/layout/sidebar.php'; ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 py-4">

                <div class="dashboard-header">
                    <h1 class="h2">Dashboard</h1>
                    <p class="text-muted">Selamat datang kembali, <?php echo htmlspecialchars($nama_pengguna); ?>!</p>
                </div>

            <?php if (!$has_transactions): ?>
                <div class="empty-state">
                    <i class="bi bi-inbox-fill empty-state-icon"></i>
                    <h5>Yuk, Catat Transaksi Pertamamu!</h5>
                    <p>Belum ada transaksi yang tercatat. Tambahkan transaksi pertamamu sekarang!</p>
                    <a href="transaksi/index.php" class="btn btn-primary mt-3">Tambah Transaksi</a>
                </div>
            <?php else: ?>

            <!-- Summary Cards -->
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card summary-card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-success-subtle text-success">
                                    <i class="bi bi-arrow-down-short"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-muted mb-1">Total Pemasukan</p>
                                    <h4 class="mb-0">Rp <?php echo number_format($total_pemasukan, 0, ',', '.'); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card summary-card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-danger-subtle text-danger">
                                    <i class="bi bi-arrow-up-short"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-muted mb-1">Total Pengeluaran</p>
                                    <h4 class="mb-0">Rp <?php echo number_format($total_pengeluaran, 0, ',', '.'); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card summary-card h-100">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="icon-circle bg-primary-subtle text-primary">
                                    <i class="bi bi-wallet2"></i>
                                </div>
                                <div class="ms-3">
                                    <p class="text-muted mb-1">Saldo Akhir</p>
                                    <h4 class="mb-0">Rp <?php echo number_format($saldo_akhir, 0, ',', '.'); ?></h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row g-4">
                <!-- Chart -->
                <div class="col-lg-8">
                    <div class="card h-100">
                        <div class="card-header">
                            Grafik Transaksi (Bulan Ini)
                        </div>
                        <div class="card-body">
                            <canvas id="myChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="col-lg-4">
                    <div class="card h-100">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span>Transaksi Terakhir</span>
                            <a href="transaksi/index.php" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
                        </div>
                        <div class="card-body p-3">
                            <div class="transaction-list">
                                <?php foreach ($recent_transactions as $trx): ?>
                                    <?php
                                        $isPemasukan = ($trx['tipe'] == 'Pemasukan');
                                        $iconClass = $isPemasukan ? 'bi-arrow-down' : 'bi-arrow-up';
                                        $colorClass = $isPemasukan ? 'success' : 'danger';
                                        $prefix = $isPemasukan ? '+' : '-';
                                    ?>
                                    <div class="transaction-item">
                                        <div class="d-flex align-items-center">
                                            <div class="transaction-icon bg-<?php echo $colorClass; ?>-subtle text-<?php echo $colorClass; ?>">
                                                <i class="bi <?php echo $iconClass; ?>"></i>
                                            </div>
                                            <div class="ms-3">
                                                <div class="fw-bold"><?php echo !empty($trx['keterangan']) ? htmlspecialchars($trx['keterangan']) : htmlspecialchars($trx['nama_kategori']); ?></div>
                                                <small class="text-muted">
                                                    <?php echo htmlspecialchars($trx['nama_kategori']); ?> ・ <?php echo date('d M', strtotime($trx['tanggal_transaksi'])); ?>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="fw-bold text-<?php echo $colorClass; ?>">
                                            <?php echo $prefix; ?>Rp <?php echo number_format($trx['jumlah'], 0, ',', '.'); ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                         <div class="card-footer text-center bg-light py-2">
                            <small class="text-muted">Menampilkan 5 transaksi terakhir</small>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>
    <script>
        // Melewatkan data PHP ke JavaScript dengan cara yang lebih bersih
        const chartData = {
            labels: <?php echo json_encode($labels); ?>,
            pemasukan: <?php echo json_encode($pemasukan_data); ?>,
            pengeluaran: <?php echo json_encode($pengeluaran_data); ?>
        };
    </script>
    <script src="/js/dashboard.js"></script>
</div>


<?php
require_once __DIR__ . '/layout/footer.php';
?>