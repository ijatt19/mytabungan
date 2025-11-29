<?php

/**
 * Format angka ke mata uang Rupiah
 */
function formatRupiah($angka) {
    return 'Rp ' . number_format($angka, 0, ',', '.');
}

/**
 * Format tanggal ke format Indonesia (dd M Y)
 */
function formatTanggal($tanggal) {
    return date('d M Y', strtotime($tanggal));
}

/**
 * Membersihkan input user
 */
function cleanInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Menampilkan pesan flash (sukses/error) dari session
 */
function displayFlashMessages() {
    if (isset($_SESSION['pesan_sukses'])) {
        echo '<div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded shadow-sm flex justify-between items-center" role="alert">';
        echo '<div><p class="font-bold">Berhasil!</p><p>' . $_SESSION['pesan_sukses'] . '</p></div>';
        echo '<button onclick="this.parentElement.remove();" class="text-green-700 hover:text-green-900"><i class="bi bi-x-lg"></i></button>';
        echo '</div>';
        unset($_SESSION['pesan_sukses']);
    }

    if (isset($_SESSION['pesan_error'])) {
        echo '<div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded shadow-sm flex justify-between items-center" role="alert">';
        echo '<div><p class="font-bold">Error!</p><p>' . $_SESSION['pesan_error'] . '</p></div>';
        echo '<button onclick="this.parentElement.remove();" class="text-red-700 hover:text-red-900"><i class="bi bi-x-lg"></i></button>';
        echo '</div>';
        unset($_SESSION['pesan_error']);
    }
}

/**
 * Menampilkan opsi kategori dengan grouping (optgroup)
 */
function render_kategori_options($daftar_kategori, $selected_id = null) {
    $grouped_kategori = [];
    foreach ($daftar_kategori as $k) {
        $grouped_kategori[$k['tipe']][] = $k;
    }

    foreach ($grouped_kategori as $tipe => $kategoris) {
        echo '<optgroup label="' . htmlspecialchars($tipe) . '">';
        foreach ($kategoris as $k) {
            $selected = ($selected_id == $k['id_kategori']) ? 'selected' : '';
            echo "<option value='{$k['id_kategori']}' {$selected}>" . htmlspecialchars($k['nama_kategori']) . "</option>";
        }
        echo '</optgroup>';
    }
}
