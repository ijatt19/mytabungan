<?php
// File ini mengambil dan memproses data untuk ditampilkan di chart dashboard.
// Variabel yang dibutuhkan: $pdo, $id_pengguna

// Inisialisasi variabel untuk chart
$labels = [];
$pemasukan_data = [];
$pengeluaran_data = [];

try {
    // Ambil data dari database untuk bulan ini
    $sql_chart = "SELECT 
                    DATE(tanggal_transaksi) as tanggal, 
                    SUM(CASE WHEN k.tipe = 'Pemasukan' THEN t.jumlah ELSE 0 END) as total_pemasukan, 
                    SUM(CASE WHEN k.tipe = 'Pengeluaran' THEN t.jumlah ELSE 0 END) as total_pengeluaran
                FROM transaksi t
                JOIN kategori k ON t.id_kategori = k.id_kategori
                WHERE t.id_pengguna = ? AND MONTH(t.tanggal_transaksi) = MONTH(CURDATE()) AND YEAR(t.tanggal_transaksi) = YEAR(CURDATE())
                GROUP BY DATE(tanggal_transaksi)
                ORDER BY tanggal ASC";
    $stmt_chart = $pdo->prepare($sql_chart);
    $stmt_chart->execute([$id_pengguna]);
    $result_chart = $stmt_chart->fetchAll();

    // Siapkan array untuk setiap hari di bulan ini
    $days = [];
    $days_in_month = date('t');
    $current_month_year = date('Y-m');
    for ($i = 1; $i <= $days_in_month; $i++) {
        $day = $current_month_year . '-' . str_pad($i, 2, '0', STR_PAD_LEFT);
        $days[$day] = ['pemasukan' => 0, 'pengeluaran' => 0];
    }

    // Isi array dengan data dari database
    foreach ($result_chart as $row) {
        $days[$row['tanggal']] = ['pemasukan' => $row['total_pemasukan'], 'pengeluaran' => $row['total_pengeluaran']];
    }

    // Format data untuk dikirim ke JavaScript
    foreach ($days as $day => $data) {
        $labels[] = date('d M', strtotime($day));
        $pemasukan_data[] = $data['pemasukan'];
        $pengeluaran_data[] = $data['pengeluaran'];
    }
} catch (PDOException $e) {
    die("Error fetching chart data: " . $e->getMessage());
}