<!-- Share Link Modal -->
<div class="modal fade" id="shareLinkModal" tabindex="-1" aria-labelledby="shareLinkModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="shareLinkModalLabel"><i class="bi bi-share-fill"></i> Bagikan Laporan Keuangan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Bagikan link di bawah ini kepada partner Anda. Siapapun dengan link ini dapat melihat ringkasan laporan keuangan Anda tanpa perlu login.</p>
                
                <div class="input-group">
                    <input type="text" class="form-control" id="shareableLinkInput" value="Membuat link..." readonly>
                    <button class="btn btn-outline-secondary" type="button" id="copyShareLinkBtn" data-bs-toggle="tooltip" data-bs-placement="top" title="Salin ke clipboard">
                        <i class="bi bi-clipboard"></i>
                    </button>
                </div>
                <div id="copy-success-message" class="text-success mt-2" style="display: none;">
                    Link berhasil disalin!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>
