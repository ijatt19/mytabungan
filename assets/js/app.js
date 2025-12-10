/**
 * MyTabungan - Main JavaScript
 * Personal Finance Management Application
 */

// =====================================================
// DOM Ready
// =====================================================
document.addEventListener('DOMContentLoaded', function() {
    initSidebar();
    initModals();
    initForms();
    initAlerts();
    initDropdowns();
    initDeleteConfirmation();
});

// =====================================================
// Sidebar Toggle
// =====================================================
function initSidebar() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const mobileMenuBtn = document.getElementById('mobile-menu-btn');
    const mobileOverlay = document.getElementById('mobile-overlay');
    const mainContent = document.getElementById('main-content');
    
    // Check if we're on desktop
    const isDesktop = () => window.innerWidth >= 1024;
    
    // Desktop toggle
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('sidebar-collapsed');
            
            // Save state to localStorage
            const isCollapsed = sidebar.classList.contains('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
            
            // Adjust main content margin only on desktop
            if (mainContent && isDesktop()) {
                mainContent.style.marginLeft = isCollapsed ? '6rem' : '';
            }
        });
        
        // Restore state from localStorage (only on desktop)
        if (isDesktop()) {
            const savedState = localStorage.getItem('sidebarCollapsed');
            if (savedState === 'true') {
                sidebar.classList.add('sidebar-collapsed');
                if (mainContent) {
                    mainContent.style.marginLeft = '6rem';
                }
            }
        }
    }
    
    // Handle window resize - reset margin on mobile
    window.addEventListener('resize', function() {
        if (mainContent) {
            if (!isDesktop()) {
                // Mobile: no margin, sidebar is overlay
                mainContent.style.marginLeft = '';
                sidebar?.classList.add('-translate-x-full');
                mobileOverlay?.classList.add('hidden');
            } else {
                // Desktop: check if collapsed
                const isCollapsed = sidebar?.classList.contains('sidebar-collapsed');
                mainContent.style.marginLeft = isCollapsed ? '6rem' : '';
                sidebar?.classList.remove('-translate-x-full');
            }
        }
    });
    
    // Mobile menu toggle
    if (mobileMenuBtn && sidebar) {
        mobileMenuBtn.addEventListener('click', function() {
            sidebar.classList.toggle('-translate-x-full');
            mobileOverlay?.classList.toggle('hidden');
        });
    }
    
    // Close mobile menu when clicking overlay
    if (mobileOverlay) {
        mobileOverlay.addEventListener('click', function() {
            sidebar?.classList.add('-translate-x-full');
            mobileOverlay.classList.add('hidden');
        });
    }
    
    // Close mobile menu when clicking a link
    const sidebarLinks = document.querySelectorAll('#sidebar a');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (!isDesktop()) {
                sidebar?.classList.add('-translate-x-full');
                mobileOverlay?.classList.add('hidden');
            }
        });
    });
}


// =====================================================
// Modal Management
// =====================================================
function initModals() {
    // Open modal buttons
    const modalTriggers = document.querySelectorAll('[data-modal-target]');
    modalTriggers.forEach(trigger => {
        trigger.addEventListener('click', function() {
            const modalId = this.getAttribute('data-modal-target');
            openModal(modalId);
        });
    });
    
    // Close modal buttons
    const modalCloses = document.querySelectorAll('[data-modal-close]');
    modalCloses.forEach(closeBtn => {
        closeBtn.addEventListener('click', function() {
            const modal = this.closest('.modal-overlay');
            closeModal(modal);
        });
    });
    
    // Close modal when clicking overlay
    const modalOverlays = document.querySelectorAll('.modal-overlay');
    modalOverlays.forEach(overlay => {
        overlay.addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal(this);
            }
        });
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const activeModal = document.querySelector('.modal-overlay.active');
            if (activeModal) {
                closeModal(activeModal);
            }
        }
    });
}

function openModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
        
        // Focus first input
        setTimeout(() => {
            const firstInput = modal.querySelector('input, select, textarea');
            firstInput?.focus();
        }, 100);
    }
}

function closeModal(modal) {
    if (typeof modal === 'string') {
        modal = document.getElementById(modal);
    }
    if (modal) {
        modal.classList.remove('active');
        document.body.style.overflow = '';
        
        // Reset form if exists
        const form = modal.querySelector('form');
        if (form) {
            form.reset();
            clearFormErrors(form);
        }
    }
}

// =====================================================
// Form Handling
// =====================================================
function initForms() {
    // Real-time validation
    const forms = document.querySelectorAll('form[data-validate]');
    forms.forEach(form => {
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateInput(this);
            });
            
            input.addEventListener('input', function() {
                clearInputError(this);
            });
        });
        
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
    });
    
    // Number formatting for currency inputs
    const currencyInputs = document.querySelectorAll('input[data-currency]');
    currencyInputs.forEach(input => {
        input.addEventListener('input', function() {
            formatCurrencyInput(this);
        });
    });
    
    // Dynamic category loading based on type
    const typeSelects = document.querySelectorAll('select[name="tipe"]');
    typeSelects.forEach(select => {
        select.addEventListener('change', function() {
            loadCategoriesByType(this.value);
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const inputs = form.querySelectorAll('input[required], select[required], textarea[required]');
    
    inputs.forEach(input => {
        if (!validateInput(input)) {
            isValid = false;
        }
    });
    
    return isValid;
}

function validateInput(input) {
    const value = input.value.trim();
    let isValid = true;
    let errorMessage = '';
    
    // Required validation
    if (input.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = 'Field ini wajib diisi';
    }
    
    // Email validation
    if (input.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = 'Format email tidak valid';
        }
    }
    
    // Password validation
    if (input.type === 'password' && input.hasAttribute('data-min-length')) {
        const minLength = parseInt(input.getAttribute('data-min-length'));
        if (value.length < minLength) {
            isValid = false;
            errorMessage = `Password minimal ${minLength} karakter`;
        }
    }
    
    // Password confirmation
    if (input.name === 'password_confirm') {
        const passwordInput = input.form.querySelector('input[name="password"]');
        if (passwordInput && value !== passwordInput.value) {
            isValid = false;
            errorMessage = 'Password tidak cocok';
        }
    }
    
    // Number validation
    if (input.type === 'number' && value) {
        const numValue = parseFloat(value);
        const min = input.getAttribute('min');
        const max = input.getAttribute('max');
        
        if (min !== null && numValue < parseFloat(min)) {
            isValid = false;
            errorMessage = `Nilai minimal adalah ${min}`;
        }
        if (max !== null && numValue > parseFloat(max)) {
            isValid = false;
            errorMessage = `Nilai maksimal adalah ${max}`;
        }
    }
    
    if (!isValid) {
        showInputError(input, errorMessage);
    } else {
        clearInputError(input);
    }
    
    return isValid;
}

function showInputError(input, message) {
    clearInputError(input);
    input.classList.add('border-red-500');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'text-red-500 text-sm mt-1 error-message';
    errorDiv.textContent = message;
    input.parentNode.appendChild(errorDiv);
}

function clearInputError(input) {
    input.classList.remove('border-red-500');
    const errorDiv = input.parentNode.querySelector('.error-message');
    if (errorDiv) {
        errorDiv.remove();
    }
}

function clearFormErrors(form) {
    const errorMessages = form.querySelectorAll('.error-message');
    errorMessages.forEach(msg => msg.remove());
    
    const errorInputs = form.querySelectorAll('.border-red-500');
    errorInputs.forEach(input => input.classList.remove('border-red-500'));
}

function formatCurrencyInput(input) {
    let value = input.value.replace(/\D/g, '');
    if (value) {
        value = parseInt(value).toLocaleString('id-ID');
    }
    input.value = value;
}

// =====================================================
// Category Loading
// =====================================================
async function loadCategoriesByType(type) {
    const categorySelect = document.querySelector('select[name="id_kategori"]');
    if (!categorySelect) return;
    
    categorySelect.innerHTML = '<option value="">Memuat...</option>';
    categorySelect.disabled = true;
    
    try {
        const response = await fetch(`api/categories.php?tipe=${type}`);
        const data = await response.json();
        
        if (data.success) {
            categorySelect.innerHTML = '<option value="">Pilih Kategori</option>';
            data.data.forEach(category => {
                const option = document.createElement('option');
                option.value = category.id_kategori;
                option.textContent = category.nama_kategori;
                categorySelect.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading categories:', error);
        categorySelect.innerHTML = '<option value="">Error memuat kategori</option>';
    } finally {
        categorySelect.disabled = false;
    }
}

// =====================================================
// Alert/Flash Messages
// =====================================================
function initAlerts() {
    // Auto-hide flash messages after 5 seconds
    const flashMessages = document.querySelectorAll('.flash-message');
    flashMessages.forEach(msg => {
        setTimeout(() => {
            msg.style.opacity = '0';
            msg.style.transform = 'translateY(-10px)';
            setTimeout(() => msg.remove(), 300);
        }, 5000);
    });
}

function showAlert(type, message, duration = 5000) {
    const alertContainer = document.getElementById('alert-container') || createAlertContainer();
    
    const colors = {
        success: 'bg-emerald-100 border-emerald-500 text-emerald-700',
        error: 'bg-red-100 border-red-500 text-red-700',
        warning: 'bg-yellow-100 border-yellow-500 text-yellow-700',
        info: 'bg-blue-100 border-blue-500 text-blue-700'
    };
    
    const icons = {
        success: 'bi-check-circle-fill',
        error: 'bi-x-circle-fill',
        warning: 'bi-exclamation-triangle-fill',
        info: 'bi-info-circle-fill'
    };
    
    const alert = document.createElement('div');
    alert.className = `${colors[type]} border-l-4 p-4 rounded-lg mb-4 flex items-center gap-3 animate-fade-in`;
    alert.innerHTML = `
        <i class="bi ${icons[type]} text-lg"></i>
        <span>${message}</span>
        <button type="button" class="ml-auto hover:opacity-70" onclick="this.parentElement.remove()">
            <i class="bi bi-x-lg"></i>
        </button>
    `;
    
    alertContainer.appendChild(alert);
    
    if (duration > 0) {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(() => alert.remove(), 300);
        }, duration);
    }
}

function createAlertContainer() {
    const container = document.createElement('div');
    container.id = 'alert-container';
    container.className = 'fixed top-4 right-4 z-50 max-w-md w-full';
    document.body.appendChild(container);
    return container;
}

// =====================================================
// Dropdown Menus
// =====================================================
function initDropdowns() {
    const dropdownTriggers = document.querySelectorAll('[data-dropdown-toggle]');
    
    dropdownTriggers.forEach(trigger => {
        trigger.addEventListener('click', function(e) {
            e.stopPropagation();
            const dropdownId = this.getAttribute('data-dropdown-toggle');
            const dropdown = document.getElementById(dropdownId);
            
            if (dropdown) {
                // Close other dropdowns
                document.querySelectorAll('.dropdown-menu.active').forEach(d => {
                    if (d !== dropdown) d.classList.remove('active');
                });
                
                dropdown.classList.toggle('active');
            }
        });
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function() {
        document.querySelectorAll('.dropdown-menu.active').forEach(dropdown => {
            dropdown.classList.remove('active');
        });
    });
}

// =====================================================
// Custom Confirm Modal
// =====================================================
function showConfirm(message, onConfirm, onCancel = null) {
    // Create modal overlay
    const overlay = document.createElement('div');
    overlay.id = 'confirm-modal';
    overlay.className = 'fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm animate-fade-in';
    
    overlay.innerHTML = `
        <div class="bg-white rounded-2xl shadow-2xl max-w-sm w-11/12 mx-4 transform animate-fade-in-up">
            <div class="p-6 text-center">
                <div class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center mx-auto mb-4">
                    <i class="bi bi-exclamation-triangle text-amber-500 text-3xl"></i>
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Konfirmasi</h3>
                <p class="text-slate-500 text-sm">${message}</p>
            </div>
            <div class="flex border-t border-slate-100">
                <button id="confirm-cancel" class="flex-1 py-3 text-slate-500 font-medium hover:bg-slate-50 transition-colors rounded-bl-2xl">
                    Batal
                </button>
                <button id="confirm-yes" class="flex-1 py-3 text-red-500 font-semibold hover:bg-red-50 transition-colors border-l border-slate-100 rounded-br-2xl">
                    Ya, Lanjutkan
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(overlay);
    document.body.style.overflow = 'hidden';
    
    // Event listeners
    overlay.querySelector('#confirm-yes').addEventListener('click', () => {
        closeConfirm();
        if (onConfirm) onConfirm();
    });
    
    overlay.querySelector('#confirm-cancel').addEventListener('click', () => {
        closeConfirm();
        if (onCancel) onCancel();
    });
    
    // Close on overlay click
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            closeConfirm();
            if (onCancel) onCancel();
        }
    });
}

function closeConfirm() {
    const modal = document.getElementById('confirm-modal');
    if (modal) {
        modal.style.opacity = '0';
        setTimeout(() => {
            modal.remove();
            document.body.style.overflow = '';
        }, 200);
    }
}

// =====================================================
// Delete Confirmation
// =====================================================
function initDeleteConfirmation() {
    const deleteButtons = document.querySelectorAll('[data-delete-confirm]');
    
    deleteButtons.forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const message = this.getAttribute('data-delete-confirm') || 'Apakah Anda yakin ingin menghapus?';
            const url = this.getAttribute('href') || this.getAttribute('data-delete-url');
            const form = this.closest('form');
            
            showConfirm(message, () => {
                if (url) {
                    window.location.href = url;
                } else if (form) {
                    form.submit();
                }
            });
        });
    });
}


// =====================================================
// Helper Functions
// =====================================================
function formatRupiah(amount) {
    return 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
}

function parseRupiah(rupiahString) {
    return parseInt(rupiahString.replace(/\D/g, '')) || 0;
}

function formatDate(date, format = 'short') {
    const months = [
        'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    const d = new Date(date);
    const day = d.getDate();
    const month = months[d.getMonth()];
    const year = d.getFullYear();
    
    if (format === 'short') {
        return `${day} ${month.substring(0, 3)} ${year}`;
    }
    return `${day} ${month} ${year}`;
}

// =====================================================
// AJAX Helpers
// =====================================================
async function fetchData(url, options = {}) {
    try {
        const response = await fetch(url, {
            ...options,
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        throw error;
    }
}

async function postData(url, data) {
    return fetchData(url, {
        method: 'POST',
        body: JSON.stringify(data)
    });
}

// =====================================================
// Chart.js Initialization
// =====================================================
function initMonthlyChart(canvasId, labels, incomeData, expenseData) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Pemasukan',
                    data: incomeData,
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgb(16, 185, 129)',
                    borderWidth: 1,
                    borderRadius: 8,
                    barPercentage: 0.6
                },
                {
                    label: 'Pengeluaran',
                    data: expenseData,
                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                    borderColor: 'rgb(239, 68, 68)',
                    borderWidth: 1,
                    borderRadius: 8,
                    barPercentage: 0.6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: {
                            family: 'Outfit',
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { family: 'Outfit', size: 14 },
                    bodyFont: { family: 'Outfit', size: 13 },
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': ' + formatRupiah(context.raw);
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: {
                        font: { family: 'Outfit', size: 12 }
                    }
                },
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)'
                    },
                    ticks: {
                        font: { family: 'Outfit', size: 12 },
                        callback: function(value) {
                            if (value >= 1000000) {
                                return 'Rp ' + (value / 1000000) + 'Jt';
                            }
                            return 'Rp ' + (value / 1000) + 'Rb';
                        }
                    }
                }
            }
        }
    });
}

function initDoughnutChart(canvasId, labels, data, colors) {
    const ctx = document.getElementById(canvasId);
    if (!ctx) return;
    
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: labels,
            datasets: [{
                data: data,
                backgroundColor: colors,
                borderWidth: 0,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        usePointStyle: true,
                        padding: 15,
                        font: {
                            family: 'Outfit',
                            size: 12
                        }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(15, 23, 42, 0.9)',
                    titleFont: { family: 'Outfit', size: 14 },
                    bodyFont: { family: 'Outfit', size: 13 },
                    padding: 12,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return context.label + ': ' + formatRupiah(context.raw);
                        }
                    }
                }
            }
        }
    });
}

// =====================================================
// Export for global access
// =====================================================
window.MyTabungan = {
    openModal,
    closeModal,
    showAlert,
    formatRupiah,
    parseRupiah,
    formatDate,
    fetchData,
    postData,
    initMonthlyChart,
    initDoughnutChart
};
