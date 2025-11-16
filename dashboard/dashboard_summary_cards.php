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