<?php
/**
 * Terms & Conditions Page
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
    <title>Syarat & Ketentuan - MyTabungan</title>
    <meta name="description" content="Syarat dan Ketentuan penggunaan aplikasi MyTabungan">
    
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
                <h1 class="text-3xl font-bold text-slate-800 mb-2">Syarat & Ketentuan</h1>
                <p class="text-slate-500">Terakhir diperbarui: <?= date('d F Y') ?></p>
            </div>
            
            <div class="prose max-w-none">
                
                <p>
                    Selamat datang di MyTabungan. Dengan menggunakan aplikasi ini, Anda menyetujui untuk terikat dengan syarat dan ketentuan berikut. Harap baca dengan seksama sebelum menggunakan layanan kami.
                </p>
                
                <h2>1. Penerimaan Ketentuan</h2>
                <p>
                    Dengan mengakses atau menggunakan MyTabungan, Anda menyatakan bahwa Anda telah membaca, memahami, dan menyetujui untuk terikat dengan Syarat & Ketentuan ini. Jika Anda tidak menyetujui ketentuan ini, mohon untuk tidak menggunakan aplikasi kami.
                </p>
                
                <h2>2. Deskripsi Layanan</h2>
                <p>
                    MyTabungan adalah aplikasi pencatatan keuangan pribadi yang memungkinkan Anda untuk:
                </p>
                <ul>
                    <li>Mencatat transaksi pemasukan dan pengeluaran</li>
                    <li>Membuat dan mengelola kategori keuangan</li>
                    <li>Melihat statistik dan grafik keuangan</li>
                    <li>Membuat wishlist dan target tabungan</li>
                    <li>Membagikan laporan keuangan kepada pihak tertentu</li>
                </ul>
                
                <h2>3. Akun Pengguna</h2>
                <p>
                    Untuk menggunakan MyTabungan, Anda perlu membuat akun dengan menyediakan informasi yang akurat dan lengkap. Anda bertanggung jawab untuk:
                </p>
                <ul>
                    <li>Menjaga kerahasiaan password akun Anda</li>
                    <li>Semua aktivitas yang terjadi melalui akun Anda</li>
                    <li>Memberitahu kami segera jika ada penggunaan tidak sah</li>
                </ul>
                
                <h2>4. Penggunaan yang Diizinkan</h2>
                <p>
                    Anda setuju untuk menggunakan MyTabungan hanya untuk tujuan yang sah dan sesuai dengan ketentuan ini. Anda dilarang untuk:
                </p>
                <ul>
                    <li>Menggunakan layanan untuk tujuan ilegal atau tidak sah</li>
                    <li>Mencoba mengakses akun pengguna lain tanpa izin</li>
                    <li>Mengganggu atau merusak operasi layanan</li>
                    <li>Menyebarkan virus atau kode berbahaya</li>
                    <li>Menggunakan bot atau skrip otomatis tanpa izin</li>
                </ul>
                
                <h2>5. Data dan Privasi</h2>
                <p>
                    Penggunaan data pribadi Anda diatur oleh <a href="privacy.php" class="text-emerald-600 hover:underline">Kebijakan Privasi</a> kami. Dengan menggunakan MyTabungan, Anda menyetujui pengumpulan dan penggunaan data sesuai dengan kebijakan tersebut.
                </p>
                
                <h2>6. Hak Kekayaan Intelektual</h2>
                <p>
                    Semua konten, desain, logo, dan elemen visual MyTabungan dilindungi oleh hak cipta dan hak kekayaan intelektual lainnya. Anda tidak diperkenankan untuk menyalin, memodifikasi, atau mendistribusikan materi kami tanpa izin tertulis.
                </p>
                
                <h2>7. Batasan Tanggung Jawab</h2>
                <p>
                    MyTabungan disediakan "sebagaimana adanya" tanpa jaminan apapun. Kami tidak bertanggung jawab atas:
                </p>
                <ul>
                    <li>Kerugian finansial akibat keputusan berdasarkan data di aplikasi</li>
                    <li>Kehilangan data akibat kegagalan sistem atau server</li>
                    <li>Gangguan layanan karena pemeliharaan atau masalah teknis</li>
                    <li>Kerugian tidak langsung, insidental, atau konsekuensial</li>
                </ul>
                
                <h2>8. Perubahan Layanan</h2>
                <p>
                    Kami berhak untuk mengubah, menangguhkan, atau menghentikan layanan kapan saja tanpa pemberitahuan sebelumnya. Kami juga dapat memperbarui Syarat & Ketentuan ini dari waktu ke waktu. Penggunaan berkelanjutan setelah perubahan berarti Anda menyetujui ketentuan yang diperbarui.
                </p>
                
                <h2>9. Penghentian Akun</h2>
                <p>
                    Kami berhak untuk menangguhkan atau menghentikan akun Anda jika Anda melanggar ketentuan ini atau terlibat dalam perilaku yang merugikan pengguna lain atau layanan kami.
                </p>
                
                <h2>10. Hukum yang Berlaku</h2>
                <p>
                    Syarat & Ketentuan ini diatur oleh dan ditafsirkan sesuai dengan hukum Republik Indonesia. Setiap sengketa yang timbul akan diselesaikan melalui musyawarah mufakat atau melalui pengadilan yang berwenang di Indonesia.
                </p>
                
                <h2>11. Hubungi Kami</h2>
                <p>
                    Jika Anda memiliki pertanyaan tentang Syarat & Ketentuan ini, silakan hubungi kami melalui email di <a href="mailto:support@mytabungan.com" class="text-emerald-600 hover:underline">support@mytabungan.com</a>.
                </p>
                
            </div>
            
            <div class="mt-12 pt-8 border-t border-slate-100 text-center">
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
