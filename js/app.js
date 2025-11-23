document.addEventListener("DOMContentLoaded", function () {
  // Inisialisasi semua tooltip
  const tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

  // Sidebar toggle - Smooth animations
  const sidebarToggleBtn = document.getElementById("sidebarToggleBtn");
  const sidebar = document.getElementById("sidebarMenu");
  const sidebarOverlay = document.getElementById("sidebarOverlay");

  if (sidebarToggleBtn && sidebar && sidebarOverlay) {
    // Open sidebar
    function openSidebar() {
      document.body.style.overflow = 'hidden'; // Prevent body scroll on mobile
      sidebarOverlay.classList.add("show");
      sidebar.classList.add("show");
    }

    // Close sidebar
    function closeSidebar() {
      document.body.style.overflow = '';
      sidebar.classList.remove("show");
      sidebarOverlay.classList.remove("show");
    }

    // Toggle sidebar
    sidebarToggleBtn.addEventListener("click", function () {
      if (sidebar.classList.contains("show")) {
        closeSidebar();
      } else {
        openSidebar();
      }
    });

    // Close sidebar when clicking overlay
    sidebarOverlay.addEventListener("click", closeSidebar);

    // Close sidebar with ESC key
    document.addEventListener("keydown", function (e) {
      if (e.key === "Escape" && sidebar.classList.contains("show")) {
        closeSidebar();
      }
    });

    // Close sidebar when window resizes to desktop
    let resizeTimer;
    window.addEventListener("resize", function () {
      clearTimeout(resizeTimer);
      resizeTimer = setTimeout(function () {
        if (window.innerWidth >= 768 && sidebar.classList.contains("show")) {
          closeSidebar();
        }
      }, 250);
    });
  }

  // Logika untuk modal konfirmasi
  const konfirmasiModal = document.getElementById("konfirmasiModal");
  if (konfirmasiModal) {
    konfirmasiModal.addEventListener("show.bs.modal", function (event) {
      const button = event.relatedTarget;
      const url = button.getAttribute("data-url");
      const form = konfirmasiModal.querySelector("#formKonfirmasi");
      if (form) {
        form.action = url;
      }
    });
  }
});

// Fungsi untuk menampilkan toast sukses
function showSuccessToast(message) {
  const successToastEl = document.getElementById("successToast");
  const successToastBody = document.getElementById("successToastBody");
  if (successToastEl && successToastBody) {
    successToastBody.textContent = message;
    const toast = new bootstrap.Toast(successToastEl, { 
      delay: 4000, // Success 4 detik
      animation: true 
    });
    toast.show();
  }
}

// Fungsi untuk menampilkan toast error
function showErrorToast(message) {
  const errorToastEl = document.getElementById("errorToast");
  const errorToastBody = document.getElementById("errorToastBody");
  if (errorToastEl && errorToastBody) {
    errorToastBody.textContent = message;
    const toast = new bootstrap.Toast(errorToastEl, { 
      delay: 5000, // Error 5 detik (lebih lama agar user bisa baca)
      animation: true 
    });
    toast.show();
  }
}
