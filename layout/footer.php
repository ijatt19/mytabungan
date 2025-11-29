<footer class="bg-gradient-to-r from-gray-50 to-green-50 border-t-2 border-green-200 mt-auto md:ml-64">
    <div class="px-6 py-6">
        <div class="flex flex-col md:flex-row justify-between items-center gap-3">
            <div class="flex items-center gap-2">
                <i class="bi bi-wallet2 text-green-600 text-lg"></i>
                <span class="text-gray-700 text-sm font-medium">&copy; <?php echo date('Y'); ?> <span class="font-bold text-green-600">MyTabungan</span>. All rights reserved.</span>
            </div>
            <div class="flex items-center gap-2 text-gray-600 text-sm">
                <i class="bi bi-code-slash text-green-600"></i>
                <span>Dibuat oleh <span class="font-semibold">Izzat Fakhar Assyakur</span> | 221011400803 | 07TPLP020</span>
            </div>
        </div>
    </div>
</footer>



<!-- Toast Notification Container -->
<div id="toastContainer" class="fixed top-4 right-4 z-[1100] flex flex-col gap-3 pointer-events-none">
    <!-- Toasts will be injected here by JS -->
</div>

<!-- Template for Success Toast -->
<template id="successToastTemplate">
    <div class="toast-item transform transition-all duration-300 translate-x-full opacity-0 flex items-center w-full max-w-xs p-4 text-white bg-gradient-to-br from-emerald-500 to-teal-500 rounded-lg shadow-lg pointer-events-auto" role="alert">
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-emerald-100 bg-emerald-600 rounded-lg">
            <i class="bi bi-check-lg"></i>
        </div>
        <div class="ml-3 text-sm font-medium toast-message">Item moved successfully.</div>
        <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-transparent text-white hover:text-gray-100 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-emerald-600 inline-flex h-8 w-8" aria-label="Close" onclick="this.closest('.toast-item').remove()">
            <i class="bi bi-x text-lg"></i>
        </button>
    </div>
</template>

<!-- Template for Error Toast -->
<template id="errorToastTemplate">
    <div class="toast-item transform transition-all duration-300 translate-x-full opacity-0 flex items-center w-full max-w-xs p-4 text-white bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-lg pointer-events-auto" role="alert">
        <div class="inline-flex items-center justify-center flex-shrink-0 w-8 h-8 text-red-100 bg-red-700 rounded-lg">
            <i class="bi bi-exclamation-lg"></i>
        </div>
        <div class="ml-3 text-sm font-medium toast-message">Item moved successfully.</div>
        <button type="button" class="ml-auto -mx-1.5 -my-1.5 bg-transparent text-white hover:text-gray-100 rounded-lg focus:ring-2 focus:ring-gray-300 p-1.5 hover:bg-red-700 inline-flex h-8 w-8" aria-label="Close" onclick="this.closest('.toast-item').remove()">
            <i class="bi bi-x text-lg"></i>
        </button>
    </div>
</template>

<?php require_once __DIR__ . '/../modal/konfirmasi_modal.php'; ?>
<?php require_once __DIR__ . '/../modal/form_konfirmasi_modal.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<!-- Base URL: <?php echo isset($base_url) ? $base_url : 'NOT SET'; ?> -->
<script src="<?php echo isset($base_url) ? $base_url : '.'; ?>/js/app.js?v=<?php echo time(); ?>"></script>

<?php
// Show success toast if session message exists
if (isset($_SESSION['pesan_sukses'])) {
    $pesan_sukses = $_SESSION['pesan_sukses'];
    echo "<script>
            document.addEventListener('DOMContentLoaded', function() {
                showSuccessToast('{$pesan_sukses}');
            });
          </script>";
    unset($_SESSION['pesan_sukses']);
}

// Show error toast if session message exists
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