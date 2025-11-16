<?php
// File: /dashboard/dashboard_wishlist_carousel.php

// Pastikan variabel $next_goals, $saldo_akhir, $pdo, dan $priority_filter tersedia dari dashboard.php
if (!isset($next_goals) || !isset($saldo_akhir)) {
    return; // Jangan render apapun jika data tidak ada
}
?>

<div class="row mb-4">
    <div class="col-12">
        <?php if (empty($next_goals)): ?>
            <!-- Tampilan jika tidak ada wishlist sama sekali -->
            <?php if (empty($priority_filter)): ?>
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h4 fw-bold mb-0">Impian Terdekatmu</h3>
                </div>
                <div class="text-center p-4 bg-light rounded-3">
                    <i class="bi bi-gem fs-2 text-muted"></i>
                    <p class="mt-2 mb-0 text-muted">Kamu belum punya impian. <a href="wishlist/">Tambah sekarang!</a></p>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div id="wishlistCarousel" class="carousel slide" data-bs-ride="false">
                <!-- Wishlist Filter Tabs -->
                <div class="wishlist-filter-tabs mb-3">
                    <?php
                        $filters = ['' => 'Semua', 'Tinggi' => 'Prioritas Tinggi', 'Sedang' => 'Prioritas Sedang', 'Rendah' => 'Prioritas Rendah'];
                        foreach ($filters as $key => $value):
                            $isActive = ($priority_filter === $key) ? 'active' : '';
                            $url = empty($key) ? strtok($_SERVER["REQUEST_URI"],'?') : "?priority_filter=$key";
                    ?>
                        <a href="<?php echo $url; ?>" class="filter-tab <?php echo $isActive; ?>"><?php echo $value; ?></a>
                    <?php endforeach; ?>
                </div>

                <div class="carousel-inner">
                    <?php foreach ($next_goals as $index => $goal):
                        // Calculations for progress and stats
                        $target = (float) $goal['harga'];
                        
                        // In the old system, 'terkumpul' was calculated against total savings.
                        // A better approach for multiple goals is to track savings per goal.
                        // For this redesign, we'll assume 'terkumpul' is a field in the wishlist table.
                        // If it's not, we'll simulate it as 0 for now.
                        $terkumpul = isset($goal['terkumpul']) ? (float) $goal['terkumpul'] : 0;
                        
                        // Let's use the total balance for now as a stand-in, like the old logic, but capped at the target
                        $terkumpul = min($saldo_akhir, $target);

                        $persentase = ($target > 0) ? ($terkumpul / $target) * 100 : 0;
                        
                        $sisa_target = $target - $terkumpul;
                        $estimasi_selesai_text = "N/A";
                        $rata_rata_nabung = 0;

                        if (isset($goal['dibuat_pada'])) {
                            try {
                                $tanggal_dibuat = new DateTime($goal['dibuat_pada']);
                                $sekarang = new DateTime();
                                $durasi = $sekarang->diff($tanggal_dibuat);
                                $hari_berlalu = $durasi->days;

                                // Avoid division by zero, assume at least 1 day for calculation
                                if ($hari_berlalu < 1) {
                                    $hari_berlalu = 1;
                                }

                                // This logic assumes savings started from day one.
                                // A more accurate calculation would require tracking savings transactions towards the goal.
                                $rata_rata_nabung = $terkumpul / $hari_berlalu;

                                if ($rata_rata_nabung > 0 && $sisa_target > 0) {
                                    $estimasi_hari = ceil($sisa_target / $rata_rata_nabung);
                                    $estimasi_selesai_text = "$estimasi_hari hari lagi";
                                } elseif ($sisa_target <= 0) {
                                    $estimasi_selesai_text = "Tercapai!";
                                }
                            } catch (Exception $e) {
                                // Handle potential DateTime errors
                                $estimasi_selesai_text = "Error";
                            }
                        }
                    ?>
                    <div class="carousel-item <?php echo ($index === 0) ? 'active' : ''; ?>">
                        <div class="card wishlist-card-modern">
                            <div class="card-body">
                                <div class="wishlist-header">
                                    <div class="wishlist-title-container">
                                        <h5 class="wishlist-title"><?php echo htmlspecialchars($goal['nama_barang']); ?></h5>
                                        <p class="wishlist-target">Target: <?php echo "Rp " . number_format($target, 0, ',', '.'); ?></p>
                                    </div>
                                    <a href="wishlist/" class="view-all-link">Lihat Semua &rarr;</a>
                                </div>

                                <div class="progress-container">
                                    <div class="progress-info">
                                        <span class="progress-percentage"><?php echo floor($persentase); ?>% Tercapai</span>
                                    </div>
                                    <div class="progress progress-modern">
                                        <div class="progress-bar" role="progressbar" style="width: <?php echo $persentase; ?>%;" aria-valuenow="<?php echo $persentase; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div class="progress-amount">
                                        <span><?php echo "Rp " . number_format($terkumpul, 0, ',', '.'); ?></span> / <span><?php echo "Rp " . number_format($target, 0, ',', '.'); ?></span>
                                    </div>
                                </div>

                                <div class="wishlist-stats">
                                    <div class="stat-item">
                                        📅 Estimasi selesai: <strong><?php echo $estimasi_selesai_text; ?></strong>
                                    </div>
                                    <div class="stat-item">
                                        💰 Rata-rata nabung: <strong><?php echo "Rp " . number_format($rata_rata_nabung, 0, ',', '.'); ?> / hari</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <?php if (count($next_goals) > 1): ?>
                <div class="carousel-controls-container">
                    <button class="carousel-control-prev" type="button" data-bs-target="#wishlistCarousel" data-bs-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Previous</span>
                    </button>
                    <div class="carousel-indicators">
                        <?php for ($i = 0; $i < count($next_goals); $i++): ?>
                            <button type="button" data-bs-target="#wishlistCarousel" data-bs-slide-to="<?php echo $i; ?>" class="<?php echo ($i === 0) ? 'active' : ''; ?>" aria-current="<?php echo ($i === 0) ? 'true' : 'false'; ?>" aria-label="Slide <?php echo $i + 1; ?>"></button>
                        <?php endfor; ?>
                    </div>
                    <button class="carousel-control-next" type="button" data-bs-target="#wishlistCarousel" data-bs-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="visually-hidden">Next</span>
                    </button>
                </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php // Tampilkan pesan ini jika ada filter aktif tapi tidak ada hasil
            if (!empty($priority_filter) && empty($next_goals)): ?>
            <div class="text-center p-4 bg-light rounded-3 mt-3">
                <i class="bi bi-search fs-2 text-muted"></i>
                <p class="mt-2 mb-0 text-muted">
                    Tidak ada wishlist dengan prioritas "<?php echo htmlspecialchars($priority_filter); ?>". <a href="?">Tampilkan semua</a>.
                </p>
            </div>
        <?php endif; ?>
    </div>
</div>
