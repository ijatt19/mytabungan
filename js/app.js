document.addEventListener("DOMContentLoaded", function () {
  // Inisialisasi semua tooltip
  const tooltipTriggerList = [].slice.call(
    document.querySelectorAll('[data-bs-toggle="tooltip"]')
  );
  tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
  });

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
    const toast = new bootstrap.Toast(successToastEl, { delay: 3000 }); // Sukses 3 detik
    toast.show();
  }
}

// Fungsi untuk menampilkan toast error
function showErrorToast(message) {
  const errorToastEl = document.getElementById("errorToast");
  const errorToastBody = document.getElementById("errorToastBody");
  if (errorToastEl && errorToastBody) {
    errorToastBody.textContent = message;
    const toast = new bootstrap.Toast(errorToastEl, { delay: 1500 }); // Error 1.5 detik
    toast.show();
  }
}
