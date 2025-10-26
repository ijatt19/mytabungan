<!-- Generic Confirmation Modal -->
<div class="modal fade" id="konfirmasiModal" tabindex="-1" aria-labelledby="konfirmasiModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 400px;">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-body p-4 text-center">
                <div class="mb-3">
                    <i class="bi bi-exclamation-triangle-fill text-danger" style="font-size: 3rem;"></i>
                </div>
                <h5 class="modal-title mb-2 fw-bold" id="konfirmasiModalLabel">Hapus Data?</h5>
                <p class="text-muted" id="konfirmasiModalBody">
                    Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            <div class="modal-footer flex-nowrap p-0">
                <button type="button" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 m-0 rounded-0 border-end" data-bs-dismiss="modal">
                    <strong>Batal</strong>
                </button>
                <a href="#" id="konfirmasiModalButton" class="btn btn-lg btn-link fs-6 text-decoration-none col-6 m-0 rounded-0 text-danger">
                    <i class="bi bi-trash me-2"></i>
                    Hapus
                </a>
            </div>
        </div>
    </div>
</div>