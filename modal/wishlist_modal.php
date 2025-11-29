<!-- Modal Tambah Wishlist -->
<div id="tambahWishlistModal" class="hidden fixed inset-0 bg-black/10 backdrop-blur-sm z-[100] flex items-center justify-center p-4" aria-labelledby="tambahWishlistModalLabel" aria-modal="true" role="dialog">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
        <form action="<?php echo $wishlist_action_url ?? 'tambah.php'; ?>" method="POST">
            <!-- <input type="hidden" name="action" value="add"> -->
            <?php if (isset($redirect_to)): ?>
                <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($redirect_to); ?>">
            <?php endif; ?>
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900" id="tambahWishlistModalLabel">
                    <i class="bi bi-stars text-green-600 mr-2"></i>
                    Tambah Impian Baru
                </h2>
                <button type="button" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-2 transition-colors" onclick="document.getElementById('tambahWishlistModal').classList.add('hidden')">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4">
                <!-- Nama Impian -->
                <div>
                    <label for="nama_barang" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="bi bi-bag-heart text-green-600 mr-1"></i>
                        Nama Impian
                    </label>
                    <input type="text" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" id="nama_barang" name="nama_barang" placeholder="Contoh: Laptop Baru" required>
                </div>

                <!-- Target Harga -->
                <div>
                    <label for="harga" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="bi bi-currency-dollar text-green-600 mr-1"></i>
                        Target Harga
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                        <input type="number" class="w-full pl-12 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" id="harga" name="harga" placeholder="0" required min="0">
                    </div>
                </div>

                <!-- Prioritas -->
                <div>
                    <label for="prioritas" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="bi bi-flag text-green-600 mr-1"></i>
                        Prioritas
                    </label>
                    <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" id="prioritas" name="prioritas" required>
                        <option value="Tinggi">🔴 Tinggi</option>
                        <option value="Sedang" selected>🟡 Sedang</option>
                        <option value="Rendah">🟢 Rendah</option>
                    </select>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <button type="button" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors" onclick="document.getElementById('tambahWishlistModal').classList.add('hidden')">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-medium rounded-lg hover:shadow-lg hover:scale-105 transition-all">
                    <i class="bi bi-check-lg mr-1"></i>
                    Simpan Impian
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Wishlist -->
<div id="editWishlistModal" class="hidden fixed inset-0 bg-black/10 backdrop-blur-sm z-[100] flex items-center justify-center p-4" aria-labelledby="editWishlistModalLabel" aria-modal="true" role="dialog">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
        <form action="edit.php" method="POST">
            <!-- <input type="hidden" name="action" value="edit"> -->
            <input type="hidden" id="edit_id_wishlist" name="id_wishlist">
            
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b border-gray-200">
                <h2 class="text-xl font-bold text-gray-900" id="editWishlistModalLabel">
                    <i class="bi bi-pencil-square text-green-600 mr-2"></i>
                    Edit Impian
                </h2>
                <button type="button" class="text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg p-2 transition-colors" onclick="document.getElementById('editWishlistModal').classList.add('hidden')">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>

            <!-- Modal Body -->
            <div class="p-6 space-y-4">
                <!-- Nama Impian -->
                <div>
                    <label for="edit_nama_barang" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="bi bi-bag-heart text-green-600 mr-1"></i>
                        Nama Impian
                    </label>
                    <input type="text" class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" id="edit_nama_barang" name="nama_barang" required>
                </div>

                <!-- Target Harga -->
                <div>
                    <label for="edit_harga" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="bi bi-currency-dollar text-green-600 mr-1"></i>
                        Target Harga
                    </label>
                    <div class="relative">
                        <span class="absolute left-3 top-1/2 -translate-y-1/2 text-gray-500 font-medium">Rp</span>
                        <input type="number" class="w-full pl-12 pr-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" id="edit_harga" name="harga" required min="0">
                    </div>
                </div>

                <!-- Prioritas -->
                <div>
                    <label for="edit_prioritas" class="block text-sm font-medium text-gray-700 mb-2">
                        <i class="bi bi-flag text-green-600 mr-1"></i>
                        Prioritas
                    </label>
                    <select class="w-full px-4 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition-all" id="edit_prioritas" name="prioritas" required>
                        <option value="Tinggi">🔴 Tinggi</option>
                        <option value="Sedang">🟡 Sedang</option>
                        <option value="Rendah">🟢 Rendah</option>
                    </select>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-end gap-3 p-6 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <button type="button" class="px-4 py-2 text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors" onclick="document.getElementById('editWishlistModal').classList.add('hidden')">
                    Batal
                </button>
                <button type="submit" class="px-6 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-medium rounded-lg hover:shadow-lg hover:scale-105 transition-all">
                    <i class="bi bi-check-lg mr-1"></i>
                    Update Impian
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Helper function to open edit modal
function openEditWishlistModal(data) {
    const modal = document.getElementById('editWishlistModal');
    document.getElementById('edit_id_wishlist').value = data.id;
    document.getElementById('edit_nama_barang').value = data.nama;
    document.getElementById('edit_harga').value = data.harga;
    document.getElementById('edit_prioritas').value = data.prioritas;
    modal.classList.remove('hidden');
}
</script>