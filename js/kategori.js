document.addEventListener("DOMContentLoaded", function () {
  // Fungsi untuk mengecek duplikasi nama kategori via API
  async function checkCategoryName(name, excludeId = 0) {
    if (name.trim() === "") {
      return false;
    }
    const response = await fetch(
      `/kategori/index.php?action=check_name&nama_kategori=${encodeURIComponent(
        name
      )}&exclude_id=${excludeId}`
    );
    const data = await response.json();
    return data.exists;
  }

  // Validasi untuk form Tambah
  const namaTambahInput = document.getElementById("nama_kategori_tambah");
  const tambahWarning = document.getElementById("tambah-warning-nama");
  const tambahSubmitBtn = document.querySelector(
    "#tambahKategoriModal button[type='submit']"
  );

  if (namaTambahInput) {
    namaTambahInput.addEventListener("blur", async function () {
      const nameExists = await checkCategoryName(this.value);
      if (nameExists) {
        this.classList.add("is-invalid");
        tambahWarning.style.display = "block";
        tambahSubmitBtn.disabled = true;
      } else {
        this.classList.remove("is-invalid");
        tambahWarning.style.display = "none";
        tambahSubmitBtn.disabled = false;
      }
    });
  }

  // Script untuk mengisi modal edit & validasi
  const editKategoriModal = document.getElementById("editKategoriModal");
  if (editKategoriModal) {
    editKategoriModal.addEventListener("show.bs.modal", function (event) {
      const button = event.relatedTarget;
      const id = button.getAttribute("data-id");
      const nama = button.getAttribute("data-nama");
      const tipe = button.getAttribute("data-tipe");

      const modalTitle = editKategoriModal.querySelector(".modal-title");
      const modalIdInput = editKategoriModal.querySelector("#edit_id_kategori");
      const modalNamaInput = editKategoriModal.querySelector(
        "#edit_nama_kategori"
      );
      const modalTipeSelect = editKategoriModal.querySelector("#edit_tipe");

      // Reset validasi saat modal dibuka
      modalNamaInput.classList.remove("is-invalid");
      document.getElementById("edit-warning-nama").style.display = "none";
      document.querySelector(
        "#editKategoriModal button[type='submit']"
      ).disabled = false;

      modalTitle.textContent = "Edit Kategori: " + nama;
      modalIdInput.value = id;
      modalNamaInput.value = nama;
      modalTipeSelect.value = tipe;
    });

    // Tambahkan event listener untuk validasi saat edit
    const namaEditInput = document.getElementById("edit_nama_kategori");
    const editWarning = document.getElementById("edit-warning-nama");
    const editSubmitBtn = document.querySelector(
      "#editKategoriModal button[type='submit']"
    );

    namaEditInput.addEventListener("blur", async function () {
      const idKategori = document.getElementById("edit_id_kategori").value;
      const nameExists = await checkCategoryName(this.value, idKategori);
      this.classList.toggle("is-invalid", nameExists);
      editWarning.style.display = nameExists ? "block" : "none";
      editSubmitBtn.disabled = nameExists;
    });
  }
});
