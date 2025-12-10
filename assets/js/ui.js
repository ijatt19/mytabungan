/**
 * MyTabungan - UI Module
 * Alerts, dropdowns, delete confirmation, and toast notifications
 */

(function() {
    'use strict';

    function init() {
        initAlerts();
        initDropdowns();
        initDeleteConfirmation();
    }

    // =====================================================
    // Toast Alerts
    // =====================================================
    function initAlerts() {
        document.querySelectorAll('.toast-close').forEach(btn => {
            btn.addEventListener('click', () => btn.closest('.toast')?.remove());
        });
    }

    function showAlert(type, message, duration = 3000) {
        const colors = {
            success: 'bg-emerald-500',
            error: 'bg-red-500',
            warning: 'bg-amber-500',
            info: 'bg-sky-500'
        };

        const icons = {
            success: 'bi-check-circle',
            error: 'bi-x-circle',
            warning: 'bi-exclamation-triangle',
            info: 'bi-info-circle'
        };

        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 ${colors[type] || colors.info} text-white px-4 py-3 rounded-xl shadow-lg z-50 flex items-center gap-2 animate-fade-in`;
        toast.innerHTML = `<i class="bi ${icons[type] || icons.info}"></i><span>${message}</span>`;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transition = 'opacity 0.3s';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }

    // =====================================================
    // Dropdowns
    // =====================================================
    function initDropdowns() {
        document.querySelectorAll('[data-dropdown-toggle]').forEach(trigger => {
            const targetId = trigger.getAttribute('data-dropdown-toggle');
            const dropdown = document.getElementById(targetId);
            
            if (!dropdown) return;

            trigger.addEventListener('click', e => {
                e.stopPropagation();
                dropdown.classList.toggle('active');
            });
        });

        // Close dropdowns on outside click
        document.addEventListener('click', () => {
            document.querySelectorAll('.dropdown-menu.active').forEach(d => d.classList.remove('active'));
        });
    }

    // =====================================================
    // Delete Confirmation
    // =====================================================
    function initDeleteConfirmation() {
        document.querySelectorAll('[data-delete-confirm]').forEach(link => {
            link.addEventListener('click', e => {
                e.preventDefault();
                const message = link.getAttribute('data-delete-confirm') || 'Yakin ingin menghapus?';
                showConfirm(message, () => window.location.href = link.href);
            });
        });
    }

    function showConfirm(message, onConfirm, onCancel = null) {
        // Remove existing confirm modal
        document.getElementById('confirmModal')?.remove();

        const modal = document.createElement('div');
        modal.id = 'confirmModal';
        modal.className = 'modal-overlay active';
        modal.innerHTML = `
            <div class="modal-content p-6 max-w-sm text-center">
                <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-red-100 flex items-center justify-center">
                    <i class="bi bi-exclamation-triangle text-red-500 text-3xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Konfirmasi</h3>
                <p class="text-slate-600 mb-6">${message}</p>
                <div class="flex gap-3">
                    <button id="confirmCancel" class="btn btn-secondary flex-1">Batal</button>
                    <button id="confirmOk" class="btn btn-danger flex-1">Ya, Hapus</button>
                </div>
            </div>
        `;

        document.body.appendChild(modal);
        document.body.style.overflow = 'hidden';

        modal.querySelector('#confirmCancel').onclick = () => { closeConfirm(); onCancel?.(); };
        modal.querySelector('#confirmOk').onclick = () => { closeConfirm(); onConfirm(); };
        modal.addEventListener('click', e => { if (e.target === modal) closeConfirm(); });
    }

    function closeConfirm() {
        const modal = document.getElementById('confirmModal');
        if (modal) {
            modal.remove();
            document.body.style.overflow = '';
        }
    }

    // Export functions
    window.showAlert = showAlert;
    window.showConfirm = showConfirm;

    document.addEventListener('DOMContentLoaded', init);
})();
