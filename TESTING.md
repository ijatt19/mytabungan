# Black Box Test Cases - MyTabungan

Berikut adalah daftar test case untuk pengujian Black Box pada aplikasi MyTabungan.

## 1. Modul Autentikasi (Authentication)

| ID | Skenario Pengujian | Langkah-langkah | Data Masukan | Hasil yang Diharapkan | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **AUTH-01** | Login dengan data valid | 1. Buka halaman Login<br>2. Masukkan email & password valid<br>3. Klik tombol "Masuk" | Email: `user@example.com`<br>Pass: `password123` | Redirect ke Dashboard, pesan sukses muncul | |
| **AUTH-02** | Login dengan email tidak terdaftar | 1. Buka halaman Login<br>2. Masukkan email belum terdaftar<br>3. Klik tombol "Masuk" | Email: `invalid@test.com`<br>Pass: `password123` | Tetap di halaman Login, pesan error "Email tidak ditemukan" | |
| **AUTH-03** | Login dengan password salah | 1. Buka halaman Login<br>2. Masukkan email valid & password salah<br>3. Klik tombol "Masuk" | Email: `user@example.com`<br>Pass: `wrongpass` | Tetap di halaman Login, pesan error "Password salah" | |
| **AUTH-04** | Registrasi akun baru | 1. Buka halaman Register<br>2. Isi Nama, Email, Password<br>3. Klik tombol "Daftar" | Nama: `Test User`<br>Email: `new@test.com`<br>Pass: `123456` | Redirect ke Login, pesan sukses "Registrasi berhasil" | |
| **AUTH-05** | Registrasi dengan email yang sudah ada | 1. Buka halaman Register<br>2. Isi email yang sudah terdaftar<br>3. Klik tombol "Daftar" | Email: `user@example.com` | Tetap di halaman Register, pesan error "Email sudah terdaftar" | |
| **AUTH-06** | Logout | 1. Login ke aplikasi<br>2. Klik tombol "Keluar" di sidebar/profil | - | Redirect ke halaman Login, session dihapus | |

## 2. Modul Dashboard

| ID | Skenario Pengujian | Langkah-langkah | Data Masukan | Hasil yang Diharapkan | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **DASH-01** | Menampilkan ringkasan saldo | 1. Login sebagai user<br>2. Lihat kartu ringkasan di atas | - | Menampilkan Total Saldo, Pemasukan, dan Pengeluaran dengan benar | |
| **DASH-02** | Menampilkan grafik | 1. Lihat bagian grafik | - | Grafik Pemasukan vs Pengeluaran muncul dan datanya sesuai | |
| **DASH-03** | Widget Quick Access | 1. Coba klik tombol "Tambah Transaksi" di dashboard | - | Modal Tambah Transaksi muncul | |

## 3. Modul Transaksi (Transactions)

| ID | Skenario Pengujian | Langkah-langkah | Data Masukan | Hasil yang Diharapkan | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **TRX-01** | Tambah Pemasukan | 1. Buka menu Transaksi<br>2. Klik "Tambah Transaksi"<br>3. Pilih Kategori Pemasukan, isi jumlah, tgl<br>4. Simpan | Kategori: Gaji<br>Jumlah: 5000000 | Data tersimpan, muncul di tabel, saldo bertambah | |
| **TRX-02** | Tambah Pengeluaran | 1. Buka menu Transaksi<br>2. Klik "Tambah Transaksi"<br>3. Pilih Kategori Pengeluaran, isi jumlah, tgl<br>4. Simpan | Kategori: Makan<br>Jumlah: 50000 | Data tersimpan, muncul di tabel, saldo berkurang | |
| **TRX-03** | Edit Transaksi | 1. Klik tombol Edit pada salah satu transaksi<br>2. Ubah jumlah/keterangan<br>3. Simpan | Jumlah: 60000 | Data terupdate di tabel dan perhitungan saldo | |
| **TRX-04** | Hapus Transaksi | 1. Klik tombol Hapus pada transaksi<br>2. Konfirmasi di modal | - | Data hilang dari tabel, saldo disesuaikan kembali | |
| **TRX-05** | Filter Transaksi | 1. Pilih rentang tanggal atau kategori di filter<br>2. Lihat hasil tabel | Tanggal: 01-30 Nov | Tabel hanya menampilkan transaksi sesuai filter | |

## 4. Modul Kategori (Categories)

| ID | Skenario Pengujian | Langkah-langkah | Data Masukan | Hasil yang Diharapkan | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **CAT-01** | Tambah Kategori Baru | 1. Buka menu Kategori<br>2. Klik "Tambah Kategori"<br>3. Isi nama & pilih tipe (Masuk/Keluar)<br>4. Simpan | Nama: Bonus<br>Tipe: Pemasukan | Kategori baru muncul di list | |
| **CAT-02** | Edit Kategori | 1. Edit salah satu kategori<br>2. Ubah nama<br>3. Simpan | Nama: Bonus Tahunan | Nama kategori berubah | |
| **CAT-03** | Hapus Kategori | 1. Hapus kategori yang belum dipakai transaksi<br>2. Konfirmasi | - | Kategori terhapus | |

## 5. Modul Wishlist (Impian)

| ID | Skenario Pengujian | Langkah-langkah | Data Masukan | Hasil yang Diharapkan | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **WISH-01** | Tambah Wishlist | 1. Buka menu Wishlist<br>2. Klik "Tambah Impian"<br>3. Isi nama barang, harga, prioritas<br>4. Simpan | Barang: Laptop<br>Harga: 15000000 | Item muncul di list dengan progress bar awal | |
| **WISH-02** | Edit Wishlist | 1. Edit item wishlist<br>2. Ubah harga target<br>3. Simpan | Harga: 14000000 | Data terupdate, persentase progress menyesuaikan | |
| **WISH-03** | Tandai Tercapai | 1. Klik tombol/status "Tercapai" atau edit status<br>2. Simpan | Status: Selesai | Item ditandai selesai/tercapai | |

## 6. Modul Profil

| ID | Skenario Pengujian | Langkah-langkah | Data Masukan | Hasil yang Diharapkan | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **PROF-01** | Update Profil | 1. Buka menu Profil<br>2. Ubah Nama Lengkap<br>3. Simpan | Nama: User Update | Nama profil berubah di header dan halaman profil | |
| **PROF-02** | Ganti Password | 1. Buka menu Profil<br>2. Isi password lama & baru<br>3. Simpan | Pass Baru: `newpass123` | Password berhasil diubah, bisa login dengan password baru | |

## 7. Responsivitas (UI/UX)

| ID | Skenario Pengujian | Langkah-langkah | Data Masukan | Hasil yang Diharapkan | Status |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **RESP-01** | Tampilan Mobile | 1. Buka aplikasi di layar HP (width < 768px)<br>2. Cek Sidebar | - | Sidebar tersembunyi, muncul saat tombol hamburger diklik | |
| **RESP-02** | Tabel di Mobile | 1. Buka halaman Transaksi di HP<br>2. Cek tabel data | - | Tabel bisa di-scroll horizontal (tidak merusak layout) | |
| **RESP-03** | Grid Layout | 1. Buka Dashboard/Wishlist di HP | - | Card tersusun vertikal (1 kolom) agar mudah dibaca | |
