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