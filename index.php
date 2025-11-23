<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyTabungan - Aplikasi Manajemen Keuangan Pribadi</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="css/landing.css" rel="stylesheet">
</head>
<body>

    <!-- Modern Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light navbar-modern sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-wallet2 text-success"></i>
                <span>MyTabungan</span>
            </a>
            <div class="d-flex gap-2">
                <a href="auth/login.php" class="btn btn-outline-success btn-modern">Masuk</a>
                <a href="auth/register.php" class="btn btn-success btn-modern">Daftar Gratis</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero-section text-center">
        <div class="container">
            <div class="hero-content">
                <div class="hero-icon">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <h1 class="hero-title">Kelola Keuangan Pribadi dengan Mudah</h1>
                <p class="hero-subtitle">Catat pemasukan, lacak pengeluaran, dan capai tujuan finansial Anda dengan antarmuka yang modern dan intuitif. Mulai perjalanan finansial Anda hari ini!</p>
                <a href="auth/register.php" class="btn btn-success btn-lg btn-modern px-5 py-3">
                    <i class="bi bi-rocket-takeoff me-2"></i>Mulai Sekarang, Gratis!
                </a>
            </div>
        </div>
    </header>

    <!-- Statistics Section -->
    <section class="stats-section">
        <div class="container">
            <div class="row text-center g-4">
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number">3</div>
                        <div class="stat-label">Langkah Mudah</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number">100%</div>
                        <div class="stat-label">Gratis Selamanya</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="stat-item">
                        <div class="stat-number">24/7</div>
                        <div class="stat-label">Akses Kapan Saja</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="fitur" class="py-5" style="padding: 6rem 0 !important; background: #fafafa;">
        <div class="container">
            <div class="section-header text-center">
                <h2 class="section-title">Fitur Unggulan Kami</h2>
                <p class="section-subtitle">Semua yang Anda butuhkan untuk mengambil kendali atas keuangan Anda dalam satu platform</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4 fade-in-section">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="bi bi-pie-chart-fill"></i>
                        </div>
                        <h5 class="feature-title">Dashboard Interaktif</h5>
                        <p class="feature-text">Dapatkan gambaran lengkap kondisi keuangan Anda secara real-time, dari pemasukan, pengeluaran, hingga saldo akhir dengan visualisasi grafik yang menarik.</p>
                    </div>
                </div>
                <div class="col-md-4 fade-in-section" style="transition-delay: 0.1s;">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="bi bi-gem"></i>
                        </div>
                        <h5 class="feature-title">Manajemen Impian</h5>
                        <p class="feature-text">Lacak progres untuk mencapai tujuan finansial Anda dengan progress bar dan filter prioritas yang canggih. Wujudkan impian Anda!</p>
                    </div>
                </div>
                <div class="col-md-4 fade-in-section" style="transition-delay: 0.2s;">
                    <div class="feature-card text-center">
                        <div class="feature-icon">
                            <i class="bi bi-arrow-down-up"></i>
                        </div>
                        <h5 class="feature-title">Manajemen Transaksi</h5>
                        <p class="feature-text">Catat setiap transaksi dengan mudah, kelompokkan berdasarkan kategori, dan lihat riwayatnya kapan saja. Kendali penuh di tangan Anda.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works Section -->
    <section class="py-5" style="padding: 6rem 0 !important;">
        <div class="container">
            <div class="section-header text-center">
                <h2 class="section-title">Cara Kerja</h2>
                <p class="section-subtitle">Mulai kelola keuangan Anda hanya dengan 3 langkah sederhana</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4 fade-in-section">
                    <div class="step-card">
                        <div class="step-number">1</div>
                        <h5 class="step-title">Daftar Gratis</h5>
                        <p class="feature-text">Buat akun Anda dalam hitungan detik. Tanpa biaya, tanpa kartu kredit.</p>
                    </div>
                </div>
                <div class="col-md-4 fade-in-section" style="transition-delay: 0.1s;">
                    <div class="step-card">
                        <div class="step-number">2</div>
                        <h5 class="step-title">Catat Transaksi</h5>
                        <p class="feature-text">Mulai mencatat pemasukan dan pengeluaran Anda setiap hari.</p>
                    </div>
                </div>
                <div class="col-md-4 fade-in-section" style="transition-delay: 0.2s;">
                    <div class="step-card">
                        <div class="step-number">3</div>
                        <h5 class="step-title">Pantau & Capai Tujuan</h5>
                        <p class="feature-text">Lihat analisis keuangan Anda dan capai target finansial dengan mudah.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section text-center">
        <div class="container position-relative" style="z-index: 1;">
            <h2 class="cta-title">Siap Mengambil Kendali Keuangan Anda?</h2>
            <p class="mb-4" style="font-size: 1.2rem; opacity: 0.95;">Bergabunglah sekarang dan mulai perjalanan menuju kebebasan finansial</p>
            <a href="auth/register.php" class="btn btn-cta">
                <i class="bi bi-stars me-2"></i>Mulai Gratis Sekarang
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-modern">
        <div class="container">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-wallet2 text-success"></i> MyTabungan
                    </h5>
                    <p class="text-secondary">Aplikasi manajemen keuangan pribadi yang membantu Anda mencapai kebebasan finansial.</p>
                </div>
                <div class="col-md-3 mb-3">
                    <h6 class="fw-bold mb-3">Menu</h6>
                    <div class="footer-links d-flex flex-column gap-2">
                        <a href="#fitur">Fitur</a>
                        <a href="auth/login.php">Masuk</a>
                        <a href="auth/register.php">Daftar</a>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <h6 class="fw-bold mb-3">Hubungi Kami</h6>
                    <div class="footer-links d-flex gap-3">
                        <a href="#"><i class="bi bi-facebook fs-5"></i></a>
                        <a href="#"><i class="bi bi-twitter fs-5"></i></a>
                        <a href="#"><i class="bi bi-instagram fs-5"></i></a>
                        <a href="#"><i class="bi bi-linkedin fs-5"></i></a>
                    </div>
                </div>
            </div>
            <hr class="my-4" style="opacity: 0.1;">
            <div class="text-center text-secondary">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> MyTabungan. All rights reserved.</p>
                <p class="mb-0 small mt-2">Dibuat oleh Izzat Fakhar Assyakur | 221011400803 | 07TPLP020</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/index.js"></script>
</body>
</html>