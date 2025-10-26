</div>
    </div>

<footer class="footer mt-auto py-3 bg-light border-top" style="z-index: 101;">
    <div class="container-fluid" style="padding-left: 280px;">
        <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted small">&copy; <?php echo date('Y'); ?> MyTabungan. All rights reserved.</span>
            <span class="text-muted small">Dibuat oleh Izzat Fakhar Assyakur | 221011400803 | 07TPLP020</span>
        </div>
    </div>
</footer>

<!-- Toast Notification Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100">
    <div id="successToast" class="toast align-items-center text-bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-check-circle-fill me-2"></i>
                <span id="successToastBody"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    <div id="errorToast" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                <span id="errorToastBody"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../modal/konfirmasi_modal.php'; ?>
<?php require_once __DIR__ . '/../modal/form_konfirmasi_modal.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/js/app.js"></script>
<?php
if (isset($_SESSION['pesan_sukses'])) {
    $pesan_sukses = $_SESSION['pesan_sukses'];
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showSuccessToast('{$pesan_sukses}');
            });
          </script>";
    unset($_SESSION['pesan_sukses']);
}
?>

</body>
</html>