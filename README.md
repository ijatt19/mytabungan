# MyTabungan - Aplikasi Manajemen Keuangan Pribadi

MyTabungan adalah aplikasi web sederhana yang dirancang untuk membantu pengguna mengelola keuangan pribadi mereka. Aplikasi ini memungkinkan pengguna untuk mencatat pemasukan dan pengeluaran, mengelola kategori transaksi, dan melacak progres untuk mencapai tujuan finansial (wishlist).

Aplikasi ini dibangun dengan PHP native, MySQL, dan Bootstrap 5, dengan fokus pada antarmuka yang bersih, modern, dan mudah digunakan.

## ✨ Fitur Utama

### 1. Dashboard Interaktif

Halaman utama setelah login yang memberikan gambaran lengkap kondisi keuangan Anda secara sekilas.

- **Kartu Ringkasan Finansial**: Menampilkan total **Pemasukan**, **Pengeluaran**, dan **Saldo Akhir** secara _real-time_.
- **Carousel Impian Terdekat**: Menampilkan daftar _wishlist_ prioritas dalam bentuk kartu modern. Dilengkapi **filter tab** untuk melihat impian berdasarkan prioritas ('Tinggi', 'Sedang', 'Rendah').
- **Grafik Tren Keuangan**: Visualisasi data transaksi bulanan dalam bentuk grafik garis, membandingkan tren pemasukan dan pengeluaran dari hari ke hari.
- **Daftar Transaksi Terakhir**: Menampilkan 5 transaksi terbaru untuk _review_ cepat.

### 2. Manajemen Wishlist (Impian)

Fitur andalan untuk melacak tujuan finansial Anda dengan antarmuka yang modern dan fungsional.

- **Tampilan Kartu Modern**: Setiap impian ditampilkan dalam kartu informatif, lengkap dengan _progress bar_, target harga, dan _badge_ prioritas berwarna.
- **Filter dan Pencarian Canggih**: Cari impian berdasarkan nama, atau filter berdasarkan **status** ('Aktif' / 'Selesai') dan **prioritas**.
- **Aksi Cepat**:
  - **Tandai Selesai**: Ubah status impian yang telah tercapai dengan satu klik.
  - **Edit via Modal**: Edit detail impian (nama, harga, prioritas) melalui _pop-up_ (modal) tanpa perlu meninggalkan halaman.
  - **Hapus Impian**: Hapus impian yang sudah tidak relevan.
- **Pagination**: Daftar impian yang panjang akan otomatis terbagi menjadi beberapa halaman.

### 3. Manajemen Transaksi & Kategori

Inti dari pencatatan keuangan yang terstruktur.

- **CRUD Kategori**: Tambah, edit, dan hapus kategori untuk **Pemasukan** (misal: Gaji, Bonus) dan **Pengeluaran** (misal: Makanan, Transportasi).
- **CRUD Transaksi**: Catat setiap transaksi, pilih kategori yang sesuai, tambahkan keterangan, dan atur tanggalnya.

### 4. Sistem Autentikasi Aman

Menjaga data keuangan Anda tetap pribadi dan aman.

- **Login & Registrasi**: Sistem pendaftaran dan login berbasis **email** dan _password_ yang di-hash.
- **Lupa Password**: Alur untuk mereset _password_ jika pengguna lupa.
- **Proteksi Halaman**: Hanya pengguna yang sudah login yang dapat mengakses data dan fitur aplikasi.

## 🛠️ Teknologi yang Digunakan

- **Backend**: PHP 8.x (Native)
- **Database**: MySQL / MariaDB
- **Frontend**:
  - HTML5
  - CSS3
  - JavaScript (ES6)
  - [Bootstrap 5.3](https://getbootstrap.com/)
  - [Bootstrap Icons](https://icons.getbootstrap.com/)
  - [Chart.js](https://www.chartjs.org/) untuk visualisasi data.
- **Server**: XAMPP (Apache)

## 🚀 Instalasi dan Setup

Ikuti langkah-langkah berikut untuk menjalankan aplikasi ini di lingkungan lokal Anda.

1.  **Clone Repository**

    ```bash
    git clone https://github.com/username/mytabungan.git
    cd mytabungan
    ```

    Atau, cukup unduh dan letakkan folder proyek di direktori `htdocs` XAMPP Anda.

2.  **Setup Database**

    - Buka **phpMyAdmin** atau _database client_ pilihan Anda.
    - Buat database baru dengan nama `tabung_db`.
    - Pilih database `tabung_db`, lalu buka tab **Import**.
    - Unggah dan jalankan file `mytabungan_dummy.sql` yang ada di root proyek untuk membuat struktur tabel dan mengisi data contoh.

3.  **Konfigurasi Koneksi**

    - Buka file `config/koneksi.php`.
    - Pastikan detail koneksi (host, nama database, user, password) sudah sesuai dengan pengaturan server lokal Anda.

    ```php
    $host = 'localhost';
    $db   = 'tabung_db'; // Pastikan nama database sesuai
    $user = 'root';      // User default XAMPP
    $pass = '';          // Password default XAMPP (kosong)
    ```

4.  **Jalankan Aplikasi**
    - Pastikan server Apache dan MySQL di XAMPP Control Panel sudah berjalan.
    - Buka browser dan akses aplikasi melalui URL:
    ```
    http://localhost/tabung/
    ```
    (Asumsi nama folder proyek adalah `tabung`).

## 🔑 Akun Demo

Anda dapat langsung login menggunakan akun demo yang sudah tersedia setelah mengimpor data dummy.

- **Email**: `izzat@example.com`
- **Password**: `password123`

## 📂 Struktur Folder

```
/
├── auth/             # Skrip autentikasi (login, register, logout)
├── config/           # File koneksi database
├── css/              # File styling CSS kustom
├── dashboard/        # Komponen-komponen untuk halaman dashboard
├── includes/         # Skrip PHP yang dapat digunakan kembali (misal: chart_data)
├── js/               # File JavaScript kustom
├── kategori/         # Modul manajemen kategori
├── layout/           # Bagian layout (header, footer, sidebar)
├── modal/            # Komponen modal Bootstrap
├── transaksi/        # Modul manajemen transaksi
├── wishlist/         # Modul manajemen wishlist
└── index.php         # Halaman utama
```
