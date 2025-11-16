document.addEventListener("DOMContentLoaded", function () {
  const editWishlistModal = document.getElementById("editWishlistModal");
  if (editWishlistModal) {
    editWishlistModal.addEventListener("show.bs.modal", function (event) {
      const button = event.relatedTarget;
      document.getElementById("edit_id_wishlist").value =
        button.getAttribute("data-id");
      document.getElementById("edit_nama_barang").value =
        button.getAttribute("data-nama");
      document.getElementById("edit_harga").value =
        button.getAttribute("data-harga");
      document.getElementById("edit_prioritas").value =
        button.getAttribute("data-prioritas");
    });
  }
});
