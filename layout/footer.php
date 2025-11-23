<footer class="footer mt-auto py-3 bg-light border-top">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center">
            <span class="text-muted small">&copy; <?php echo date('Y'); ?> MyTabungan. All rights reserved.</span>
            <span class="text-muted small">Dibuat oleh Izzat Fakhar Assyakur | 221011400803 | 07TPLP020</span>
        </div>
    </div>
</footer>

        </div> <!-- end main-content-wrapper -->
    </div> <!-- end app-container -->

<!-- Toast Notification Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100">
    <!-- Success Toast -->
    <div id="successToast" class="toast align-items-center border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" style="min-width: 300px; background: linear-gradient(135deg, #10b981 0%, #14b8a6 100%);">
        <div class="d-flex">
            <div class="toast-body text-white fw-500">
                <i class="bi bi-check-circle-fill me-2 fs-5"></i>
                <span id="successToastBody"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
    
    <!-- Error Toast -->
    <div id="errorToast" class="toast align-items-center border-0 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true" style="min-width: 300px; background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);">
        <div class="d-flex">
            <div class="toast-body text-white fw-500">
                <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
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
if (isset($_SESSION['pesan_error'])) {
    $pesan_error = $_SESSION['pesan_error'];
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showErrorToast('{$pesan_error}');
            });
          </script>";
    unset($_SESSION['pesan_error']);
}
?>

<?php require_once __DIR__ . '/../modal/share_link_modal.php'; ?>
</body>
</html>