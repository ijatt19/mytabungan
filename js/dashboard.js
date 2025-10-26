document.addEventListener("DOMContentLoaded", function () {
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
            borderColor: "#4caf50",
            backgroundColor: "rgba(76, 175, 80, 0.1)",
            fill: true,
            tension: 0.4,
          },
          {
            label: "Pengeluaran",
            data: pengeluaranData,
            borderColor: "#f44336",
            backgroundColor: "rgba(244, 67, 54, 0.1)",
            fill: true,
            tension: 0.4,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
          y: {
            beginAtZero: true,
          },
        },
        plugins: {
          legend: {
            position: "top",
          },
        },
      },
    });
  }
});
