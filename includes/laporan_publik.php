<?php
require_once __DIR__ . '/../config/koneksi.php';

// Ambil token dari URL
$token = $_GET['token'] ?? '';

if (empty($token)) {
    http_response_code(400);
    die("Token tidak ditemukan.");
}

$id_pengguna = null;
$nama_pengguna = 'Pengguna';

try {
    // Cari id_pengguna berdasarkan token
    $sql_token = "SELECT id_pengguna FROM share_links WHERE token = ?";
    $stmt_token = $pdo->prepare($sql_token);
    $stmt_token->execute([$token]);
    $result = $stmt_token->fetch();

    if (!$result) {
        http_response_code(404);
        die("Link tidak valid atau sudah tidak berlaku.");
    }
    $id_pengguna = $result['id_pengguna'];

    // Ambil nama pengguna
    $sql_user = "SELECT nama_lengkap FROM pengguna WHERE id_pengguna = ?";
    $stmt_user = $pdo->prepare($sql_user);
    $stmt_user->execute([$id_pengguna]);
    $nama_pengguna = $stmt_user->fetch()['nama_lengkap'] ?? 'Pengguna';

    // Ambil data keuangan
    $sql_pemasukan = "SELECT SUM(jumlah) AS total FROM transaksi t JOIN kategori k ON t.id_kategori = k.id_kategori WHERE t.id_pengguna = ? AND k.tipe = 'Pemasukan'";
    $stmt_pemasukan = $pdo->prepare($sql_pemasukan);
    $stmt_pemasukan->execute([$id_pengguna]);
    $total_pemasukan = $stmt_pemasukan->fetch()['total'] ?? 0;

    $sql_pengeluaran = "SELECT SUM(jumlah) AS total FROM transaksi t JOIN kategori k ON t.id_kategori = k.id_kategori WHERE t.id_pengguna = ? AND k.tipe = 'Pengeluaran'";
    $stmt_pengeluaran = $pdo->prepare($sql_pengeluaran);
    $stmt_pengeluaran->execute([$id_pengguna]);
    $total_pengeluaran = $stmt_pengeluaran->fetch()['total'] ?? 0;

    $saldo_akhir = $total_pemasukan - $total_pengeluaran;

    // Ambil 10 transaksi terakhir
    $sql_recent = "SELECT t.tanggal_transaksi, t.keterangan, k.nama_kategori, k.tipe, t.jumlah 
                   FROM transaksi t
                   JOIN kategori k ON t.id_kategori = k.id_kategori 
                   WHERE t.id_pengguna = ? 
                   ORDER BY t.tanggal_transaksi DESC, t.id_transaksi DESC
                   LIMIT 10";
    $stmt_recent = $pdo->prepare($sql_recent);
    $stmt_recent->execute([$id_pengguna]);
    $recent_transactions = $stmt_recent->fetchAll();

} catch (PDOException $e) {
    // Jangan tampilkan error detail di production
    http_response_code(500);
    die("Terjadi kesalahan pada server.");
}

// Fungsi untuk format Rupiah
if (!function_exists('format_rupiah')) {
    function format_rupiah($number) {
        return 'Rp ' . number_format($number, 0, ',', '.');
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - <?php echo htmlspecialchars($nama_pengguna); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../css/public-report.css" rel="stylesheet">
</head>
<body>
    <div class="report-container">
        <!-- Header -->
        <header class="report-header">
            <div class="report-logo">
                <i class="bi bi-wallet2"></i>
            </div>
            <h1 class="report-title">Laporan Keuangan</h1>
            <div class="report-meta">
                <i class="bi bi-person-circle text-primary"></i>
                <span>Milik: <strong><?php echo htmlspecialchars($nama_pengguna); ?></strong></span>
                <span class="mx-2 text-muted">|</span>
                <i class="bi bi-calendar-check text-success"></i>
                <span><?php echo date('d M Y'); ?></span>
            </div>
        </header>

        <!-- Summary Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div class="summary-card income">
                    <div class="summary-label">Total Pemasukan</div>
                    <div class="summary-amount text-success">
                        <?php echo format_rupiah($total_pemasukan); ?>
                    </div>
                    <i class="bi bi-arrow-down-circle-fill summary-icon text-success"></i>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card expense">
                    <div class="summary-label">Total Pengeluaran</div>
                    <div class="summary-amount text-danger">
                        <?php echo format_rupiah($total_pengeluaran); ?>
                    </div>
                    <i class="bi bi-arrow-up-circle-fill summary-icon text-danger"></i>
                </div>
            </div>
            <div class="col-md-4">
                <div class="summary-card balance">
                    <div class="summary-label">Saldo Akhir</div>
                    <div class="summary-amount <?php echo $saldo_akhir >= 0 ? 'text-primary' : 'text-danger'; ?>">
                        <?php echo format_rupiah($saldo_akhir); ?>
                    </div>
                    <i class="bi bi-wallet-fill summary-icon text-primary"></i>
                </div>
            </div>
        </div>

        <!-- Transaction List -->
        <div class="transaction-section">
            <div class="section-header">
                <h2 class="section-title">
                    <i class="bi bi-clock-history text-primary"></i>
                    10 Transaksi Terakhir
                </h2>
            </div>
            <div class="table-responsive">
                <table class="table table-custom">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Keterangan</th>
                            <th>Kategori</th>
                            <th class="text-end">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recent_transactions)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-3 opacity-25"></i>
                                    Belum ada data transaksi yang tercatat.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recent_transactions as $trx): ?>
                                <tr>
                                    <td>
                                        <div class="transaction-date">
                                            <span class="date-day"><?php echo date('d', strtotime($trx['tanggal_transaksi'])); ?></span>
                                            <span class="date-month"><?php echo date('M Y', strtotime($trx['tanggal_transaksi'])); ?></span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-medium text-dark"><?php echo htmlspecialchars($trx['keterangan']); ?></div>
                                    </td>
                                    <td>
                                        <span class="category-badge <?php echo $trx['tipe'] === 'Pemasukan' ? 'badge-income' : 'badge-expense'; ?>">
                                            <i class="bi <?php echo $trx['tipe'] === 'Pemasukan' ? 'bi-arrow-down-short' : 'bi-arrow-up-short'; ?>"></i>
                                            <?php echo htmlspecialchars($trx['nama_kategori']); ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="<?php echo $trx['tipe'] === 'Pemasukan' ? 'amount-positive' : 'amount-negative'; ?>">
                                            <?php echo ($trx['tipe'] === 'Pemasukan' ? '+' : '-') . format_rupiah($trx['jumlah']); ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- CTA Footer -->
        <div class="cta-box">
            <h3 class="fw-bold mb-2">Kelola Keuanganmu Sendiri</h3>
            <p class="text-white-50 mb-4">Bergabunglah dengan MyTabungan dan capai kebebasan finansialmu sekarang.</p>
            <a href="../index.php" class="cta-btn">
                Mulai Gratis Sekarang <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>

        <div class="report-footer">
            <p class="mb-1">&copy; <?php echo date('Y'); ?> MyTabungan. All rights reserved.</p>
            <p class="small">Generated securely by MyTabungan System</p>
        </div>
    </div>
</body>
</html>
