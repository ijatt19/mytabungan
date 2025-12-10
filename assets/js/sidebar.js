/**
 * MyTabungan - Sidebar Module
 * Sidebar toggle and responsive handling
 */

(function() {
    'use strict';

    const COLLAPSED_WIDTH = '6rem';
    const STORAGE_KEY = 'sidebarCollapsed';

    function init() {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebar-toggle');
        const mainContent = document.getElementById('main-content');
        const footer = document.querySelector('footer.fixed');

        if (!sidebar || !toggle) return;

        const isDesktop = () => window.innerWidth >= 1024;
        
        // Toggle handler
        toggle.addEventListener('click', () => {
            const isCollapsed = sidebar.classList.toggle('sidebar-collapsed');
            localStorage.setItem(STORAGE_KEY, isCollapsed);
            updateLayout(isCollapsed, mainContent, footer);
        });

        // Restore saved state
        if (isDesktop() && localStorage.getItem(STORAGE_KEY) === 'true') {
            sidebar.classList.add('sidebar-collapsed');
            updateLayout(true, mainContent, footer);
        }

        // Handle resize
        window.addEventListener('resize', () => {
            if (!isDesktop()) {
                mainContent && (mainContent.style.marginLeft = '');
                footer && (footer.style.left = '');
            } else {
                const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
                updateLayout(isCollapsed, mainContent, footer);
            }
        });
    }

    function updateLayout(isCollapsed, mainContent, footer) {
        const margin = isCollapsed ? COLLAPSED_WIDTH : '';
        mainContent && (mainContent.style.marginLeft = margin);
        footer && (footer.style.left = isCollapsed ? COLLAPSED_WIDTH : '16rem');
    }

    // Initialize on DOM ready
    document.addEventListener('DOMContentLoaded', init);
})();
