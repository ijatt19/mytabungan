// Skrip global untuk aplikasi

// Fungsi untuk menampilkan toast sukses
function showSuccessToast(message) {
  const toastEl = document.getElementById("successToast");
  const toastBody = document.getElementById("successToastBody");
  toastBody.textContent = message;
  const toast = new bootstrap.Toast(toastEl);
  toast.show();
}

document.addEventListener("DOMContentLoaded", function () {
  // Handle konfirmasi modal dinamis
  const konfirmasiModal = document.getElementById("konfirmasiModal");
  if (konfirmasiModal) {
    konfirmasiModal.addEventListener("show.bs.modal", function (event) {
      const button = event.relatedTarget; // Tombol yang memicu modal
      const url = button.getAttribute("data-url"); // Ambil URL dari atribut data-url

      const konfirmasiBtn = konfirmasiModal.querySelector(
        "#konfirmasiModalButton"
      );
      konfirmasiBtn.setAttribute("href", url); // Set URL ke tombol 'Hapus' di modal
    });
  }

  // Handle konfirmasi untuk form submit
  const formKonfirmasiModal = document.getElementById("formKonfirmasiModal");
  if (formKonfirmasiModal) {
    const konfirmasiBtn = formKonfirmasiModal.querySelector(
      "#formKonfirmasiButton"
    );
    let formToSubmit = null;

    // Cari semua form dengan atribut data-confirm
    document.querySelectorAll('form[data-confirm="true"]').forEach((form) => {
      form.addEventListener("submit", function (event) {
        event.preventDefault(); // Cegah submit langsung
        formToSubmit = this; // Simpan form yang akan di-submit
        const modal = new bootstrap.Modal(formKonfirmasiModal);
        modal.show();
      });
    });

    // Tambahkan event listener ke tombol konfirmasi di dalam modal
    konfirmasiBtn.addEventListener("click", function () {
      if (formToSubmit) {
        formToSubmit.submit(); // Submit form yang sudah disimpan
      }
    });
  }
});
