/**
 * MyTabungan - Modal Module
 * Modal open/close and overlay handling
 */

(function() {
    'use strict';

    function init() {
        // Close modal on overlay click
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', e => {
                if (e.target === overlay) closeModal(overlay);
            });
        });

        // Close modal buttons
        document.querySelectorAll('[data-modal-close]').forEach(btn => {
            btn.addEventListener('click', () => {
                const modal = btn.closest('.modal-overlay');
                if (modal) closeModal(modal);
            });
        });

        // Close on ESC key
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.active').forEach(closeModal);
            }
        });
    }

    function openModal(modalId) {
        const modal = typeof modalId === 'string' 
            ? document.getElementById(modalId) 
            : modalId;
        if (modal) {
            modal.classList.add('active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(modal) {
        if (typeof modal === 'string') modal = document.getElementById(modal);
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
            // Reset forms inside modal
            modal.querySelectorAll('form').forEach(form => form.reset());
        }
    }

    // Export functions
    window.openModal = openModal;
    window.closeModal = closeModal;

    document.addEventListener('DOMContentLoaded', init);
})();
