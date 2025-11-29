<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<?php
// Fetch Active Wishlist Items
$wishlist_by_priority = ['Tinggi' => [], 'Sedang' => [], 'Rendah' => []];
try {
    $stmt_wishlist = $pdo->prepare("SELECT * FROM wishlist WHERE id_pengguna = ? AND status != 'Selesai' ORDER BY harga ASC");
    $stmt_wishlist->execute([$id_pengguna]);
    $all_wishlist = $stmt_wishlist->fetchAll();
    
    foreach ($all_wishlist as $item) {
        if (isset($wishlist_by_priority[$item['prioritas']])) {
            $wishlist_by_priority[$item['prioritas']][] = $item;
        }
    }
} catch (PDOException $e) {
    // Handle error silently or log
}

foreach (['Tinggi', 'Sedang', 'Rendah'] as $priority): 
    $items = $wishlist_by_priority[$priority];
    $isHidden = $priority !== 'Tinggi' ? 'hidden' : '';
?>
    <div id="content-<?php echo $priority; ?>" class="wishlist-tab-content <?php echo $isHidden; ?>">
        <?php if (empty($items)): ?>
            <div class="text-center py-8">
                <div class="inline-flex items-center justify-center w-12 h-12 bg-gray-100 rounded-full mb-3">
                    <i class="bi bi-clipboard-check text-gray-400 text-xl"></i>
                </div>
                <button onclick="document.getElementById('tambahWishlistModal').classList.remove('hidden')" class="text-gray-500 text-sm hover:text-purple-600 hover:underline transition-colors">
                    Tidak ada Wishlist dengan prioritas <?php echo $priority; ?>, Tekan untuk tambah Wishlist.
                </button>
            </div>
        <?php else: ?>
            <!-- Carousel Container -->
            <div class="draggable-carousel flex overflow-x-auto gap-4 pb-4 scrollbar-hide cursor-grab active:cursor-grabbing select-none">
                <?php foreach ($items as $item): 
                    $target = (float) $item['harga'];
                    $terkumpul = min($saldo_akhir, $target); // Asumsi saldo bisa dipakai semua
                    $persen = ($target > 0) ? ($terkumpul / $target) * 100 : 0;
                    $persen = min($persen, 100);
                ?>
                    <div class="min-w-[280px] sm:min-w-[320px] flex-shrink-0 snap-start border border-gray-100 rounded-xl p-4 hover:border-purple-200 hover:shadow-sm transition-all bg-white">
                        <div class="flex justify-between items-start mb-2">
                            <h6 class="font-semibold text-gray-900 truncate pr-2" title="<?php echo htmlspecialchars($item['nama_barang']); ?>">
                                <?php echo htmlspecialchars($item['nama_barang']); ?>
                            </h6>
                            <span class="text-xs font-bold text-purple-600 bg-purple-50 px-2 py-1 rounded-md">
                                <?php echo floor($persen); ?>%
                            </span>
                        </div>
                        <div class="flex justify-between text-xs text-gray-500 mb-1">
                            <span><?php echo formatRupiah($terkumpul); ?></span>
                            <span><?php echo formatRupiah($target); ?></span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2">
                            <div class="bg-gradient-to-r from-purple-500 to-indigo-500 h-2 rounded-full" style="width: <?php echo $persen; ?>%"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
<?php endforeach; ?>
