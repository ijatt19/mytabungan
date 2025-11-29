<!-- Generic Form Confirmation Modal -->
<div id="formKonfirmasiModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" aria-labelledby="formKonfirmasiModalLabel" aria-modal="true" role="dialog">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm transform transition-all">
        <div class="p-6 text-center">
            <div class="mb-4">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100">
                    <i class="bi bi-question-circle-fill text-green-600 text-4xl"></i>
                </div>
            </div>
            <h5 class="text-xl font-bold text-gray-900 mb-2" id="formKonfirmasiModalLabel">Konfirmasi Aksi</h5>
            <p class="text-gray-600 text-sm" id="formKonfirmasiModalBody">
                Apakah Anda yakin ingin menyimpan data ini?
            </p>
        </div>
        <div class="flex border-t border-gray-200">
            <button type="button" class="flex-1 px-4 py-3 text-base font-medium text-gray-700 hover:bg-gray-50 border-r border-gray-200 transition-colors" onclick="document.getElementById('formKonfirmasiModal').classList.add('hidden')">
                Batal
            </button>
            <button type="button" id="formKonfirmasiButton" class="flex-1 px-4 py-3 text-base font-bold text-green-600 hover:bg-green-50 transition-colors">
                Ya, Lanjutkan
            </button>
        </div>
    </div>
</div>