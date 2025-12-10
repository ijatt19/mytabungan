/**
 * MyTabungan - Charts Module
 * Chart.js initialization for monthly trends and doughnut charts
 */

(function() {
    'use strict';

    const chartDefaults = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { position: 'bottom', labels: { padding: 20, usePointStyle: true } }
        }
    };

    function initMonthlyChart(canvasId, labels, incomeData, expenseData) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        return new Chart(ctx, {
            type: 'bar',
            data: {
                labels,
                datasets: [
                    {
                        label: 'Pemasukan',
                        data: incomeData,
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderColor: '#10b981',
                        borderWidth: 1,
                        borderRadius: 6,
                        barPercentage: 0.8,
                        categoryPercentage: 0.7
                    },
                    {
                        label: 'Pengeluaran',
                        data: expenseData,
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderColor: '#ef4444',
                        borderWidth: 1,
                        borderRadius: 6,
                        barPercentage: 0.8,
                        categoryPercentage: 0.7
                    }
                ]
            },
            options: {
                ...chartDefaults,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: v => 'Rp' + (v / 1000000).toFixed(0) + 'jt'
                        },
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }

    function initDoughnutChart(canvasId, labels, data, colors) {
        const ctx = document.getElementById(canvasId);
        if (!ctx) return null;

        return new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: colors,
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                ...chartDefaults,
                cutout: '65%'
            }
        });
    }

    // Export functions
    window.initMonthlyChart = initMonthlyChart;
    window.initDoughnutChart = initDoughnutChart;
    
    // Also add to MyTabungan object
    if (window.MyTabungan) {
        window.MyTabungan.initMonthlyChart = initMonthlyChart;
        window.MyTabungan.initDoughnutChart = initDoughnutChart;
    }
})();
