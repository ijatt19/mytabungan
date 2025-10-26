<?php
// Fungsi untuk menampilkan opsi kategori
function render_kategori_options($daftar_kategori, $selected_id = null) {
    $grouped_kategori = [];
    foreach ($daftar_kategori as $k) {
        $grouped_kategori[$k['tipe']][] = $k;
    }

    foreach ($grouped_kategori as $tipe => $kategoris) {
        echo '<optgroup label="' . htmlspecialchars($tipe) . '">';
        foreach ($kategoris as $k) {
            $selected = ($selected_id == $k['id_kategori']) ? 'selected' : '';
            echo "<option value='{$k['id_kategori']}' {$selected}>" . htmlspecialchars($k['nama_kategori']) . "</option>";
        }
        echo '</optgroup>';
    }
}
?>

<!-- Modal Tambah Transaksi -->
<div class="modal fade" id="tambahTransaksiModal" tabindex="-1" aria-labelledby="tambahTransaksiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <form action="index.php" method="POST" data-confirm="true">
                <input type="hidden" name="action" value="tambah">
                <div class="modal-header border-0">
                    <h1 class="modal-title fs-5" id="tambahTransaksiModalLabel">Tambah Transaksi Baru</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tanggal_transaksi_tambah" class="form-label">Tanggal</label>
                        <input type="date" class="form-control form-control-modern" id="tanggal_transaksi_tambah" name="tanggal_transaksi" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="id_kategori_tambah" class="form-label">Kategori</label>
                        <select class="form-select form-control-modern" id="id_kategori_tambah" name="id_kategori" required>
                            <option value="">Pilih Kategori</option>
                            <?php render_kategori_options($daftar_kategori); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="jumlah_tambah" class="form-label">Jumlah (Rp)</label>
                        <input type="number" class="form-control form-control-modern" id="jumlah_tambah" name="jumlah" placeholder="Contoh: 50000" required>
                    </div>
                    <div class="mb-3">
                        <label for="keterangan_tambah" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control form-control-modern" id="keterangan_tambah" name="keterangan" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Transaksi -->
<div class="modal fade" id="editTransaksiModal" tabindex="-1" aria-labelledby="editTransaksiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <form action="index.php" method="POST" data-confirm="true">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="edit_id_transaksi" name="id_transaksi">
                <div class="modal-header border-0">
                    <h1 class="modal-title fs-5" id="editTransaksiModalLabel">Edit Transaksi</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_tanggal_transaksi" class="form-label">Tanggal</label>
                        <input type="date" class="form-control form-control-modern" id="edit_tanggal_transaksi" name="tanggal_transaksi" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_id_kategori" class="form-label">Kategori</label>
                        <select class="form-select form-control-modern" id="edit_id_kategori" name="id_kategori" required>
                            <option value="">Pilih Kategori</option>
                            <?php render_kategori_options($daftar_kategori); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_jumlah" class="form-label">Jumlah (Rp)</label>
                        <input type="number" class="form-control form-control-modern" id="edit_jumlah" name="jumlah" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control form-control-modern" id="edit_keterangan" name="keterangan" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>