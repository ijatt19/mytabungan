document.addEventListener("DOMContentLoaded", function () {
  // Auto-submit filter form on change
  const filterForm = document.getElementById("filter-form");
  if (filterForm) {
    const inputs = filterForm.querySelectorAll("input, select");
    inputs.forEach((input) => {
      input.addEventListener("change", function () {
        filterForm.submit();
      });
    });
  }

  // Handle edit modal population
  const editTransaksiModal = document.getElementById("editTransaksiModal");
  if (editTransaksiModal) {
    editTransaksiModal.addEventListener("show.bs.modal", function (event) {
      const button = event.relatedTarget;

      // Extract data from data-* attributes
      const id = button.getAttribute("data-id");
      const tanggal = button.getAttribute("data-tanggal");
      const kategoriId = button.getAttribute("data-kategori-id");
      const jumlah = button.getAttribute("data-jumlah");
      const keterangan = button.getAttribute("data-keterangan");

      // Populate the modal's form fields
      const modal = this;
      modal.querySelector("#edit_id_transaksi").value = id;
      modal.querySelector("#edit_tanggal_transaksi").value = tanggal;
      modal.querySelector("#edit_id_kategori").value = kategoriId;
      modal.querySelector("#edit_jumlah").value = jumlah;
      modal.querySelector("#edit_keterangan").value = keterangan;
    });
  }
});
