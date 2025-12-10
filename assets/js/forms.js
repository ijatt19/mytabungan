/**
 * MyTabungan - Forms Module
 * Form validation and currency formatting
 */

(function() {
    'use strict';

    function init() {
        // Currency input formatting
        document.querySelectorAll('[data-currency]').forEach(input => {
            input.addEventListener('input', () => formatCurrencyInput(input));
            input.addEventListener('blur', () => formatCurrencyInput(input));
        });

        // Form validation
        document.querySelectorAll('form[data-validate]').forEach(form => {
            form.addEventListener('submit', e => {
                if (!validateForm(form)) {
                    e.preventDefault();
                }
            });
        });
    }

    function formatCurrencyInput(input) {
        let value = input.value.replace(/[^\d]/g, '');
        if (value) {
            input.value = new Intl.NumberFormat('id-ID').format(parseInt(value));
        }
    }

    function validateForm(form) {
        let isValid = true;
        clearFormErrors(form);

        form.querySelectorAll('[required]').forEach(input => {
            if (!validateInput(input)) isValid = false;
        });

        return isValid;
    }

    function validateInput(input) {
        const value = input.value.trim();
        
        // Required check
        if (input.hasAttribute('required') && !value) {
            showInputError(input, 'Field ini wajib diisi');
            return false;
        }

        // Email validation
        if (input.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                showInputError(input, 'Format email tidak valid');
                return false;
            }
        }

        // Min length
        const minLength = input.getAttribute('minlength');
        if (minLength && value.length < parseInt(minLength)) {
            showInputError(input, `Minimal ${minLength} karakter`);
            return false;
        }

        return true;
    }

    function showInputError(input, message) {
        input.classList.add('border-red-500');
        const error = document.createElement('span');
        error.className = 'text-red-500 text-xs mt-1 input-error';
        error.textContent = message;
        input.parentNode.appendChild(error);
    }

    function clearFormErrors(form) {
        form.querySelectorAll('.input-error').forEach(el => el.remove());
        form.querySelectorAll('.border-red-500').forEach(el => el.classList.remove('border-red-500'));
    }

    function clearInputError(input) {
        input.classList.remove('border-red-500');
        const error = input.parentNode.querySelector('.input-error');
        if (error) error.remove();
    }

    // Export functions
    window.validateForm = validateForm;
    window.formatCurrencyInput = formatCurrencyInput;

    document.addEventListener('DOMContentLoaded', init);
})();
