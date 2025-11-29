<!-- Share Link Modal -->
<div id="shareLinkModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4" aria-labelledby="shareLinkModalLabel" aria-modal="true" role="dialog">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg transform transition-all">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-200">
            <h5 class="text-xl font-semibold text-gray-900 flex items-center gap-2" id="shareLinkModalLabel">
                <i class="bi bi-share-fill text-green-600"></i> Bagikan Laporan Keuangan
            </h5>
            <button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" onclick="document.getElementById('shareLinkModal').classList.add('hidden')">
                <i class="bi bi-x-lg text-xl"></i>
            </button>
        </div>
        <div class="p-6">
            <p class="text-gray-600 mb-4">Bagikan link di bawah ini kepada partner Anda. Siapapun dengann link ini dapat melihat ringkasan laporan keuangan Anda tanpa perlu login.</p>
            
            <div class="flex gap-2">
                <input type="text" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 bg-gray-50" id="shareableLinkInput" value="Membuat link..." readonly>
                <button class="px-4 py-2 bg-gray-100 border border-gray-300 rounded-lg hover:bg-gray-200 hover:border-gray-400 transition-colors" type="button" id="copyShareLinkBtn" title="Salin ke clipboard">
                    <i class="bi bi-clipboard text-gray-700"></i>
                </button>
            </div>
            <div id="copy-success-message" class="hidden text-green-600 text-sm mt-2">
                <i class="bi bi-check-circle-fill mr-1"></i> Link berhasil disalin!
            </div>
        </div>
        <div class="flex justify-end gap-3 px-6 py-4 border-t border-gray-200 bg-gray-50 rounded-b-2xl">
            <button type="button" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 font-medium rounded-lg transition-colors" onclick="document.getElementById('shareLinkModal').classList.add('hidden')">Tutup</button>
        </div>
    </div>
</div>
