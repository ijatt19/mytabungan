<?php
/**
 * Privacy Policy Page
 * MyTabungan - Personal Finance Management
 */

require_once __DIR__ . '/auth/auth.php';

// Redirect to dashboard if logged in
if (isLoggedIn()) {
    $backUrl = 'dashboard.php';
} else {
    $backUrl = 'index.php';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kebijakan Privasi - MyTabungan</title>
    <meta name="description" content="Kebijakan Privasi dan perlindungan data pengguna MyTabungan">
    
    <!-- Compiled Tailwind CSS -->
    <link rel="stylesheet" href="assets/css/styles.css">
    
    <!-- Google Fonts - Outfit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <style>
        
        .prose h2 {
            margin-top: 2rem;
            margin-bottom: 1rem;
            font-size: 1.25rem;
            font-weight: 600;
            color: #1e293b;
        }
        
        .prose p {
            margin-bottom: 1rem;
            color: #475569;
            line-height: 1.7;
        }
        
        .prose ul {
            margin-bottom: 1rem;
            padding-left: 1.5rem;
            list-style-type: disc;
        }
        
        .prose li {
            margin-bottom: 0.5rem;
            color: #475569;
        }
        
        .prose strong {
            color: #1e293b;
        }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">
    
    <!-- Header -->
    <header class="bg-white border-b border-slate-100 sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between">
            <a href="<?= $backUrl ?>" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center">
                    <i class="bi bi-wallet2 text-white text-lg"></i>
                </div>
                <span class="font-bold text-xl bg-gradient-to-r from-emerald-600 to-teal-600 bg-clip-text text-transparent">
                    MyTabungan
                </span>
            </a>
            <a href="<?= $backUrl ?>" class="text-slate-500 hover:text-emerald-600 transition-colors flex items-center gap-2">
                <i class="bi bi-arrow-left"></i>
                <span>Kembali</span>
            </a>
        </div>
    </header>
    
    <!-- Content -->
    <main class="max-w-4xl mx-auto px-4 py-12">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-8 md:p-12">
            
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-slate-800 mb-2">Kebijakan Privasi</h1>
                <p class="text-slate-500">Terakhir diperbarui: <?= date('d F Y') ?></p>
            </div>
            
            <div class="prose max-w-none">
                
                <p>
                    MyTabungan berkomitmen untuk melindungi privasi Anda. Kebijakan Privasi ini menjelaskan bagaimana kami mengumpulkan, menggunakan, dan melindungi informasi pribadi Anda saat menggunakan aplikasi kami.
                </p>
                
                <h2>1. Informasi yang Kami Kumpulkan</h2>
                <p>
                    Kami mengumpulkan beberapa jenis informasi untuk menyediakan dan meningkatkan layanan kami:
                </p>
                <ul>
                    <li><strong>Informasi Akun:</strong> Nama, alamat email, dan password terenkripsi saat Anda mendaftar</li>
                    <li><strong>Data Keuangan:</strong> Transaksi pemasukan dan pengeluaran yang Anda catat, kategori, wishlist, dan target tabungan</li>
                    <li><strong>Data Penggunaan:</strong> Waktu akses, fitur yang digunakan, dan preferensi aplikasi</li>
                    <li><strong>Informasi Perangkat:</strong> Jenis browser, sistem operasi, dan alamat IP untuk keamanan</li>
                </ul>
                
                <h2>2. Bagaimana Kami Menggunakan Data Anda</h2>
                <p>
                    Data yang kami kumpulkan digunakan untuk:
                </p>
                <ul>
                    <li>Menyediakan dan memelihara layanan MyTabungan</li>
                    <li>Menampilkan statistik dan grafik keuangan Anda</li>
                    <li>Mengamankan akun Anda dari akses tidak sah</li>
                    <li>Mengirimkan notifikasi penting terkait akun (jika diperlukan)</li>
                    <li>Meningkatkan dan mengembangkan fitur baru</li>
                    <li>Menganalisis penggunaan untuk optimasi performa</li>
                </ul>
                
                <h2>3. Keamanan Data</h2>
                <p>
                    Kami mengambil langkah-langkah keamanan yang serius untuk melindungi data Anda:
                </p>
                <ul>
                    <li><strong>Enkripsi Password:</strong> Password Anda di-hash menggunakan algoritma bcrypt yang aman</li>
                    <li><strong>Koneksi Aman:</strong> Semua data ditransmisikan melalui protokol HTTPS</li>
                    <li><strong>Akses Terbatas:</strong> Hanya Anda yang dapat mengakses data keuangan Anda</li>
                    <li><strong>Backup Berkala:</strong> Data di-backup secara berkala untuk mencegah kehilangan</li>
                </ul>
                
                <h2>4. Pembagian Data</h2>
                <p>
                    <strong>Kami TIDAK menjual data Anda kepada pihak ketiga.</strong> Data Anda hanya dibagikan dalam kondisi berikut:
                </p>
                <ul>
                    <li><strong>Fitur Berbagi:</strong> Saat Anda secara eksplisit membagikan laporan keuangan melalui fitur "Bagikan"</li>
                    <li><strong>Kewajiban Hukum:</strong> Jika diwajibkan oleh hukum atau proses hukum yang sah</li>
                    <li><strong>Perlindungan Hak:</strong> Untuk melindungi keamanan pengguna atau publik</li>
                </ul>
                
                <h2>5. Penyimpanan Data</h2>
                <p>
                    Data Anda disimpan di server yang aman selama akun Anda aktif. Jika Anda menghapus akun:
                </p>
                <ul>
                    <li>Semua data keuangan Anda akan dihapus secara permanen</li>
                    <li>Proses penghapusan akan selesai dalam waktu 30 hari</li>
                    <li>Beberapa data anonim mungkin dipertahankan untuk analisis agregat</li>
                </ul>
                
                <h2>6. Hak Anda</h2>
                <p>
                    Anda memiliki hak-hak berikut terkait data pribadi Anda:
                </p>
                <ul>
                    <li><strong>Akses:</strong> Melihat semua data yang kami simpan tentang Anda</li>
                    <li><strong>Koreksi:</strong> Memperbarui atau memperbaiki data yang tidak akurat</li>
                    <li><strong>Penghapusan:</strong> Meminta penghapusan akun dan data Anda</li>
                    <li><strong>Ekspor:</strong> Mengunduh salinan data Anda dalam format yang dapat dibaca</li>
                    <li><strong>Pembatasan:</strong> Membatasi penggunaan data tertentu</li>
                </ul>
                
                <h2>7. Cookie dan Teknologi Pelacakan</h2>
                <p>
                    MyTabungan menggunakan cookie untuk:
                </p>
                <ul>
                    <li>Menjaga sesi login Anda tetap aktif</li>
                    <li>Mengingat preferensi tampilan Anda</li>
                    <li>Meningkatkan keamanan akun</li>
                </ul>
                <p>
                    Kami tidak menggunakan cookie untuk iklan atau pelacakan pihak ketiga.
                </p>
                
                <h2>8. Privasi Anak-anak</h2>
                <p>
                    MyTabungan tidak ditujukan untuk anak-anak di bawah usia 13 tahun. Kami tidak secara sengaja mengumpulkan data pribadi dari anak-anak. Jika Anda mengetahui bahwa anak Anda telah memberikan data kepada kami, silakan hubungi kami untuk menghapusnya.
                </p>
                
                <h2>9. Perubahan Kebijakan</h2>
                <p>
                    Kami dapat memperbarui Kebijakan Privasi ini dari waktu ke waktu. Perubahan signifikan akan diberitahukan melalui:
                </p>
                <ul>
                    <li>Pemberitahuan di dalam aplikasi</li>
                    <li>Email ke alamat terdaftar Anda</li>
                    <li>Perubahan tanggal "Terakhir diperbarui" di halaman ini</li>
                </ul>
                
                <h2>10. Hubungi Kami</h2>
                <p>
                    Jika Anda memiliki pertanyaan atau kekhawatiran tentang Kebijakan Privasi ini atau praktik data kami, silakan hubungi kami:
                </p>
                <ul>
                    <li><strong>Email:</strong> <a href="mailto:privacy@mytabungan.com" class="text-emerald-600 hover:underline">privacy@mytabungan.com</a></li>
                    <li><strong>Formulir Kontak:</strong> Tersedia di dalam aplikasi</li>
                </ul>
                
                <div class="mt-8 p-4 bg-emerald-50 rounded-xl border border-emerald-100">
                    <p class="text-emerald-700 mb-0 flex items-start gap-2">
                        <i class="bi bi-shield-check text-xl"></i>
                        <span><strong>Komitmen Kami:</strong> Privasi Anda adalah prioritas utama kami. Data keuangan Anda adalah milik Anda, dan kami berkomitmen untuk menjaganya dengan standar keamanan tertinggi.</span>
                    </p>
                </div>
                
            </div>
            
            <div class="mt-12 pt-8 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="terms.php" class="inline-flex items-center gap-2 px-6 py-3 border-2 border-emerald-500 text-emerald-600 font-semibold rounded-xl hover:bg-emerald-50 transition-colors">
                    <i class="bi bi-file-text"></i>
                    Baca Syarat & Ketentuan
                </a>
                <a href="<?= $backUrl ?>" class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-500 text-white font-semibold rounded-xl hover:bg-emerald-600 transition-colors">
                    <i class="bi bi-arrow-left"></i>
                    Kembali ke Beranda
                </a>
            </div>
            
        </div>
    </main>
    
    <!-- Footer -->
    <footer class="py-8 text-center text-slate-400 text-sm">
        &copy; <?= date('Y') ?> MyTabungan. Kelola Keuangan dengan Mudah.
    </footer>
    
</body>
</html>
