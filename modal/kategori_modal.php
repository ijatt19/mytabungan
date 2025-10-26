<!-- Modal Tambah Kategori -->
<div class="modal fade" id="tambahKategoriModal" tabindex="-1" aria-labelledby="tambahKategoriModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <form action="index.php" method="POST" data-confirm="true">
                <input type="hidden" name="action" value="tambah">
                <div class="modal-header border-0">
                    <h1 class="modal-title fs-5" id="tambahKategoriModalLabel">Tambah Kategori Baru</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="nama_kategori_tambah" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control form-control-modern" id="nama_kategori_tambah" name="nama_kategori" required>
                        <div class="invalid-feedback" id="tambah-warning-nama">Kategori dengan nama ini sudah ada.</div>
                    </div>
                    <div class="mb-3">
                        <label for="tipe_tambah" class="form-label">Tipe</label>
                        <select class="form-select form-control-modern" id="tipe_tambah" name="tipe" required>
                            <option value="" disabled selected>Pilih tipe</option>
                            <option value="Pemasukan">Pemasukan</option>
                            <option value="Pengeluaran">Pengeluaran</option>
                        </select>
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

<!-- Modal Edit Kategori -->
<div class="modal fade" id="editKategoriModal" tabindex="-1" aria-labelledby="editKategoriModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <form action="index.php" method="POST" data-confirm="true">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="edit_id_kategori" name="id_kategori">
                <div class="modal-header border-0">
                    <h1 class="modal-title fs-5" id="editKategoriModalLabel">Edit Kategori</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nama_kategori" class="form-label">Nama Kategori</label>
                        <input type="text" class="form-control form-control-modern" id="edit_nama_kategori" name="nama_kategori" required>
                        <div class="invalid-feedback" id="edit-warning-nama">Kategori dengan nama ini sudah ada.</div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_tipe" class="form-label">Tipe</label>
                        <select class="form-select form-control-modern" id="edit_tipe" name="tipe" required>
                            <option value="Pemasukan">Pemasukan</option>
                            <option value="Pengeluaran">Pengeluaran</option>
                        </select>
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