<!-- Modal Edit Wishlist -->
<div class="modal fade" id="editWishlistModal" tabindex="-1" aria-labelledby="editWishlistModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <form action="index.php?<?php echo http_build_query($_GET); ?>" method="POST">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" id="edit_id_wishlist" name="id_wishlist">
                <div class="modal-header border-0">
                    <h1 class="modal-title fs-5" id="editWishlistModalLabel">Edit Impian</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_nama_barang" class="form-label">Nama Impian</label>
                        <input type="text" class="form-control form-control-modern" id="edit_nama_barang" name="nama_barang" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_harga" class="form-label">Target Harga</label>
                        <div class="input-group">
                            <span class="input-group-text">Rp</span>
                            <input type="number" class="form-control form-control-modern" id="edit_harga" name="harga" required min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_prioritas" class="form-label">Prioritas</label>
                        <select class="form-select form-control-modern" id="edit_prioritas" name="prioritas" required>
                            <option value="Tinggi">Tinggi</option>
                            <option value="Sedang">Sedang</option>
                            <option value="Rendah">Rendah</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary rounded-3">Update Impian</button>
                </div>
            </form>
        </div>
    </div>
</div>