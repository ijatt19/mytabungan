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

  // === SHARE LINK FEATURE ===
  const shareLinkModal = document.getElementById('shareLinkModal');
  
  if (shareLinkModal) {
    const linkInput = document.getElementById('shareableLinkInput');
    const copyBtn = document.getElementById('copyShareLinkBtn');
    const copySuccessMsg = document.getElementById('copy-success-message');

    // Initialize tooltip if copyBtn exists
    let copyTooltip;
    if (copyBtn) {
      copyTooltip = new bootstrap.Tooltip(copyBtn);
    }

    // Saat modal ditampilkan, panggil fungsi untuk membuat link
    shareLinkModal.addEventListener('show.bs.modal', function () {
      // Reset state
      if (linkInput) linkInput.value = 'Membuat link...';
      if (copySuccessMsg) copySuccessMsg.style.display = 'none';
      if (copyBtn) copyBtn.disabled = true;
      if (copyTooltip) copyTooltip.setContent({ '.tooltip-inner': 'Salin ke clipboard' });

      fetch('includes/generate_share_link.php')
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            if (linkInput) linkInput.value = data.url;
            if (copyBtn) copyBtn.disabled = false;
          } else {
            if (linkInput) linkInput.value = 'Gagal membuat link: ' + data.message;
          }
        })
        .catch(error => {
          console.error('Error:', error);
          if (linkInput) linkInput.value = 'Terjadi kesalahan koneksi.';
        });
    });

    // Fungsi untuk menyalin link ke clipboard
    if (copyBtn) {
      copyBtn.addEventListener('click', function () {
        navigator.clipboard.writeText(linkInput.value).then(() => {
          // Tampilkan pesan sukses
          if (copySuccessMsg) {
            copySuccessMsg.style.display = 'block';
            setTimeout(() => {
              copySuccessMsg.style.display = 'none';
            }, 2000);
          }

          // Ubah tooltip sementara
          if (copyTooltip) {
            copyTooltip.setContent({ '.tooltip-inner': 'Disalin!' });
            setTimeout(() => {
              copyTooltip.setContent({ '.tooltip-inner': 'Salin ke clipboard' });
            }, 2000);
          }
        }).catch(err => {
          console.error('Gagal menyalin: ', err);
        });
      });
    }
  }
});
