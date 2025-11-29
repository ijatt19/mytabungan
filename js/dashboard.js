document.addEventListener("DOMContentLoaded", function () {
  // === CHART INITIALIZATION ===
  // Cek apakah elemen chart dan object datanya ada
  if (document.getElementById("myChart") && typeof chartData !== "undefined") {
    const ctx = document.getElementById("myChart");

    // Ambil data dari object chartData global (dari dashboard.php)
    const labels = chartData.labels;
    const pemasukanData = chartData.pemasukan;
    const pengeluaranData = chartData.pengeluaran;

    new Chart(ctx, {
      type: "line",
      data: {
        labels: labels,
        datasets: [
          {
            label: "Pemasukan",
            data: pemasukanData,
            borderColor: "#10b981",
            backgroundColor: "rgba(16, 185, 129, 0.1)",
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6,
          },
          {
            label: "Pengeluaran",
            data: pengeluaranData,
            borderColor: "#ef4444",
            backgroundColor: "rgba(239, 68, 68, 0.1)",
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: function(value) {
                return 'Rp ' + value.toLocaleString('id-ID');
              }
            }
          },
        },
        plugins: {
          legend: {
            position: "top",
            labels: {
              usePointStyle: true,
              padding: 15,
            }
          },
          tooltip: {
            mode: 'index',
            intersect: false,
            callbacks: {
              label: function(context) {
                let label = context.dataset.label || '';
                if (label) {
                  label += ': ';
                }
                label += 'Rp ' + context.parsed.y.toLocaleString('id-ID');
                return label;
              }
            }
          }
        },
        interaction: {
          mode: 'nearest',
          axis: 'x',
          intersect: false
        }
      },
    });
  }

  // === DRAGGABLE CAROUSEL INITIALIZATION ===
  enableDragScroll('.draggable-carousel');
});

// Fungsi untuk switch tab wishlist di dashboard
function switchWishlistTab(priority) {
  // Hide all contents
  document.querySelectorAll('.wishlist-tab-content').forEach(el => {
    el.classList.add('hidden');
  });
  
  // Show selected content
  const content = document.getElementById('content-' + priority);
  if (content) {
    content.classList.remove('hidden');
  }
  
  // Update tab styles
  const tabs = ['Tinggi', 'Sedang', 'Rendah'];
  tabs.forEach(tab => {
    const btn = document.getElementById('tab-' + tab);
    if (tab === priority) {
      btn.classList.remove('text-gray-500', 'hover:text-gray-900');
      btn.classList.add('bg-white', 'text-gray-900', 'shadow-sm');
    } else {
      btn.classList.add('text-gray-500', 'hover:text-gray-900');
      btn.classList.remove('bg-white', 'text-gray-900', 'shadow-sm');
    }
  });
}

// Fungsi untuk mengaktifkan drag scroll pada element
function enableDragScroll(selector) {
  const sliders = document.querySelectorAll(selector);
  
  sliders.forEach(slider => {
    let isDown = false;
    let startX;
    let scrollLeft;

    slider.addEventListener('mousedown', (e) => {
      isDown = true;
      slider.classList.add('active'); // Optional: for styling
      startX = e.pageX - slider.offsetLeft;
      scrollLeft = slider.scrollLeft;
    });

    slider.addEventListener('mouseleave', () => {
      isDown = false;
      slider.classList.remove('active');
    });

    slider.addEventListener('mouseup', () => {
      isDown = false;
      slider.classList.remove('active');
    });

    slider.addEventListener('mousemove', (e) => {
      if (!isDown) return;
      e.preventDefault();
      const x = e.pageX - slider.offsetLeft;
      const walk = (x - startX) * 1.5; // Scroll-fast multiplier
      slider.scrollLeft = scrollLeft - walk;
    });
  });
}

// Fungsi untuk generate share link
function generateShareLink() {
  const btn = document.querySelector('button[onclick="generateShareLink()"]');
  const originalText = btn.innerHTML;
  
  // Loading state
  btn.disabled = true;
  btn.innerHTML = '<i class="bi bi-hourglass-split mr-2"></i> Loading...';

  fetch('includes/generate_share_link.php')
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Copy to clipboard
        navigator.clipboard.writeText(data.url).then(() => {
          btn.innerHTML = '<i class="bi bi-check-lg mr-2"></i> Tersalin!';
          btn.classList.remove('bg-white', 'text-gray-700');
          btn.classList.add('bg-green-50', 'text-green-600', 'border-green-200');
          
          setTimeout(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            btn.classList.add('bg-white', 'text-gray-700');
            btn.classList.remove('bg-green-50', 'text-green-600', 'border-green-200');
          }, 3000);
        });
      } else {
        alert('Gagal: ' + data.message);
        btn.innerHTML = originalText;
        btn.disabled = false;
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('Terjadi kesalahan saat membuat link.');
      btn.innerHTML = originalText;
      btn.disabled = false;
    });
}
