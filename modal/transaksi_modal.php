
<!-- Modal Tambah Transaksi -->
<div id="tambahTransaksiModal" class="hidden fixed inset-0 bg-black/10 backdrop-blur-sm z-[100] flex items-center justify-center p-4" aria-labelledby="tambahTransaksiModalLabel" aria-modal="true" role="dialog">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
        <form action="<?php echo $transaksi_action_url ?? 'tambah.php'; ?>" method="POST">
            <!-- <input type="hidden" name="action" value="tambah"> -->
            <?php if (isset($redirect_to)): ?>
                <input type="hidden" name="redirect_to" value="<?php echo htmlspecialchars($redirect_to); ?>">
            <?php endif; ?>
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h5 class="text-xl font-semibold text-gray-900" id="tambahTransaksiModalLabel">Tambah Transaksi Baru</h5>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="document.getElementById('tambahTransaksiModal').classList.add('hidden')">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label for="tanggal_transaksi_tambah" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" id="tanggal_transaksi_tambah" name="tanggal_transaksi" value="<?php echo date('Y-m-d'); ?>" required>
                </div>
                <div>
                    <label for="id_kategori_tambah" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" id="id_kategori_tambah" name="id_kategori" required>
                        <option value="">Pilih Kategori</option>
                        <?php render_kategori_options($daftar_kategori); ?>
                    </select>
                </div>
                <div>
                    <label for="jumlah_tambah" class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Rp)</label>
                    <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" id="jumlah_tambah" name="jumlah" placeholder="Contoh: 50000" required>
                </div>
                <div>
                    <label for="keterangan_tambah" class="block text-sm font-medium text-gray-700 mb-1">Keterangan (Opsional)</label>
                    <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" id="keterangan_tambah" name="keterangan" rows="2"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <button type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors" onclick="document.getElementById('tambahTransaksiModal').classList.add('hidden')">Batal</button>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-medium rounded-lg hover:shadow-lg transition-all">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Transaksi -->
<div id="editTransaksiModal" class="hidden fixed inset-0 bg-black/10 backdrop-blur-sm z-[100] flex items-center justify-center p-4" aria-labelledby="editTransaksiModalLabel" aria-modal="true" role="dialog">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
        <form action="" method="POST">
            <input type="hidden" name="action" value="edit">
            <input type="hidden" id="edit_id_transaksi" name="id_transaksi">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h5 class="text-xl font-semibold text-gray-900" id="editTransaksiModalLabel">Edit Transaksi</h5>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="document.getElementById('editTransaksiModal').classList.add('hidden')">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label for="edit_tanggal_transaksi" class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                    <input type="date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" id="edit_tanggal_transaksi" name="tanggal_transaksi" required>
                </div>
                <div>
                    <label for="edit_id_kategori" class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" id="edit_id_kategori" name="id_kategori" required>
                        <option value="">Pilih Kategori</option>
                        <?php render_kategori_options($daftar_kategori); ?>
                    </select>
                </div>
                <div>
                    <label for="edit_jumlah" class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Rp)</label>
                    <input type="number" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" id="edit_jumlah" name="jumlah" required>
                </div>
                <div>
                    <label for="edit_keterangan" class="block text-sm font-medium text-gray-700 mb-1">Keterangan (Opsional)</label>
                    <textarea class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500" id="edit_keterangan" name="keterangan" rows="2"></textarea>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <button type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors" onclick="document.getElementById('editTransaksiModal').classList.add('hidden')">Batal</button>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-medium rounded-lg hover:shadow-lg transition-all">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
// Helper functions for modals
function openEditModal(transaction) {
    const modal = document.getElementById('editTransaksiModal');
    document.getElementById('edit_id_transaksi').value = transaction.id_transaksi;
    document.getElementById('edit_tanggal_transaksi').value = transaction.tanggal_transaksi;
    document.getElementById('edit_id_kategori').value = transaction.id_kategori;
    document.getElementById('edit_jumlah').value = transaction.jumlah;
    document.getElementById('edit_keterangan').value = transaction.keterangan || '';
    modal.classList.remove('hidden');
}


</script>