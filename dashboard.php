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

            <?php // Memanggil komponen kartu summary
            require_once __DIR__ . '/dashboard/dashboard_summary_cards.php'; ?>

            <?php // Memanggil komponen carousel wishlist
            require_once __DIR__ . '/dashboard/dashboard_wishlist_carousel.php'; ?>

            <?php // Memanggil komponen konten utama (grafik & transaksi terakhir)
            require_once __DIR__ . '/dashboard/dashboard_main_content.php'; ?>

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