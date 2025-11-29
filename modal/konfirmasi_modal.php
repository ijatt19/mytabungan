<!-- Generic Confirmation Modal -->
<div id="konfirmasiModal" class="hidden fixed inset-0 bg-black/10 backdrop-blur-sm z-[100] flex items-center justify-center p-4" aria-labelledby="konfirmasiModalLabel" aria-modal="true" role="dialog">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm transform transition-all">
        <div class="p-6 text-center">
            <div class="mb-4">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100">
                    <i class="bi bi-exclamation-triangle-fill text-red-600 text-4xl"></i>
                </div>
            </div>
            <h5 class="text-xl font-bold text-gray-900 mb-2" id="konfirmasiModalLabel">Hapus Data?</h5>
            <p class="text-gray-600 text-sm" id="konfirmasiModalBody">
                Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.
            </p>
        </div>
        <div class="flex border-t border-gray-200">
            <button type="button" class="flex-1 px-4 py-3 text-base font-semibold text-gray-700 hover:bg-gray-50 border-r border-gray-200 transition-colors" onclick="document.getElementById('konfirmasiModal').classList.add('hidden')">
                Batal
            </button>
            <a href="#" id="konfirmasiModalButton" class="flex-1 px-4 py-3 text-base font-semibold text-red-600 hover:bg-red-50 transition-colors inline-flex items-center justify-center gap-2">
                <i class="bi bi-trash"></i>
                Hapus
            </a>
        </div>
    </div>
</div>