/**
 * MyTabungan - Core JavaScript Module
 * Base utilities and DOM ready initialization
 */

// =====================================================
// Helper Functions
// =====================================================

const MyTabungan = {
    // Format number as Indonesian Rupiah
    formatRupiah(amount) {
        return new Intl.NumberFormat('id-ID', {
            style: 'currency',
            currency: 'IDR',
            minimumFractionDigits: 0
        }).format(amount);
    },

    // Parse rupiah string to number
    parseRupiah(rupiahString) {
        return parseInt(rupiahString.replace(/[^\d]/g, '')) || 0;
    },

    // Format date
    formatDate(date, format = 'short') {
        const options = format === 'long' 
            ? { day: 'numeric', month: 'long', year: 'numeric' }
            : { day: 'numeric', month: 'short', year: 'numeric' };
        return new Date(date).toLocaleDateString('id-ID', options);
    },

    // AJAX GET request
    async fetchData(url, options = {}) {
        try {
            const response = await fetch(url, {
                method: 'GET',
                headers: { 'Content-Type': 'application/json', ...options.headers },
                ...options
            });
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return await response.json();
        } catch (error) {
            console.error('Fetch error:', error);
            throw error;
        }
    },

    // AJAX POST request
    async postData(url, data) {
        return this.fetchData(url, {
            method: 'POST',
            body: data instanceof FormData ? data : JSON.stringify(data)
        });
    }
};

// Export for global access
window.MyTabungan = MyTabungan;
