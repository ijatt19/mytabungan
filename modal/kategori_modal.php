<!-- Modal Tambah Kategori -->
<div id="tambahKategoriModal" class="hidden fixed inset-0 bg-black/10 backdrop-blur-sm z-[100] flex items-center justify-center p-4" aria-labelledby="tambahKategoriModalLabel" aria-modal="true" role="dialog">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
        <form action="tambah.php" method="POST">
            <!-- <input type="hidden" name="action" value="tambah"> -->
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h5 class="text-xl font-semibold text-gray-900" id="tambahKategoriModalLabel">Tambah Kategori Baru</h5>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="document.getElementById('tambahKategoriModal').classList.add('hidden')">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label for="nama_kategori_tambah" class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" id="nama_kategori_tambah" name="nama_kategori" placeholder="Contoh: Gaji, Makan" required>
                </div>
                <div>
                    <label for="tipe_tambah" class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" id="tipe_tambah" name="tipe" required>
                        <option value="">Pilih Tipe</option>
                        <option value="Pemasukan">Pemasukan</option>
                        <option value="Pengeluaran">Pengeluaran</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <button type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors" onclick="document.getElementById('tambahKategoriModal').classList.add('hidden')">Batal</button>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-medium rounded-lg hover:shadow-lg transition-all">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Kategori -->
<div id="editKategoriModal" class="hidden fixed inset-0 bg-black/10 backdrop-blur-sm z-[100] flex items-center justify-center p-4" aria-labelledby="editKategoriModalLabel" aria-modal="true" role="dialog">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md transform transition-all">
        <form action="edit.php" method="POST">
            <!-- <input type="hidden" name="action" value="edit"> -->
            <input type="hidden" id="edit_id_kategori" name="id_kategori">
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
                <h5 class="text-xl font-semibold text-gray-900" id="editKategoriModalLabel">Edit Kategori</h5>
                <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="document.getElementById('editKategoriModal').classList.add('hidden')">
                    <i class="bi bi-x-lg text-xl"></i>
                </button>
            </div>
            <div class="p-6 space-y-4">
                <div>
                    <label for="edit_nama_kategori" class="block text-sm font-medium text-gray-700 mb-1">Nama Kategori</label>
                    <input type="text" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" id="edit_nama_kategori" name="nama_kategori" required>
                </div>
                <div>
                    <label for="edit_tipe" class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                    <select class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" id="edit_tipe" name="tipe" required>
                        <option value="">Pilih Tipe</option>
                        <option value="Pemasukan">Pemasukan</option>
                        <option value="Pengeluaran">Pengeluaran</option>
                    </select>
                </div>
            </div>
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
                <button type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors" onclick="document.getElementById('editKategoriModal').classList.add('hidden')">Batal</button>
                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-green-600 to-emerald-600 text-white font-medium rounded-lg hover:shadow-lg transition-all">Update</button>
            </div>
        </form>
    </div>
</div>

<script>
// Helper function for opening edit modal
function openEditKategoriModal(kategori) {
    const modal = document.getElementById('editKategoriModal');
    document.getElementById('edit_id_kategori').value = kategori.id_kategori;
    document.getElementById('edit_nama_kategori').value = kategori.nama_kategori;
    document.getElementById('edit_tipe').value = kategori.tipe;
    modal.classList.remove('hidden');
}
</script>
