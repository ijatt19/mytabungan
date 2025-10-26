<nav id="sidebarMenu" class="d-md-block sidebar collapse" style="width: 250px;">
    <div class="position-sticky">
        <a href="/dashboard.php" class="sidebar-header d-flex align-items-center justify-content-center p-3 text-decoration-none">
            <i class="bi bi-wallet2 fs-4 me-2 text-success"></i>
            <span class="fs-5 fw-bold">MyTabungan</span>
        </a>

        <ul class="nav flex-column pt-3">
            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'dashboard.php') !== false) ? 'active' : ''; ?>" href="/dashboard.php">
                    <i class="bi bi-grid-1x2 me-3"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/transaksi/') !== false) ? 'active' : ''; ?>" href="/transaksi/index.php">
                    <i class="bi bi-arrow-left-right me-3"></i> Transaksi
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], '/kategori/') !== false) ? 'active' : ''; ?>" href="/kategori/index.php">
                    <i class="bi bi-tags me-3"></i> Kategori
                </a>
            </li>
        </ul>

        <div class="sidebar-footer mt-auto">
            <hr class="mx-3">
            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link <?php echo (strpos($_SERVER['REQUEST_URI'], 'profile.php') !== false) ? 'active' : ''; ?>" href="/profile.php">
                        <i class="bi bi-person me-3"></i> Profile
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/auth/logout.php">
                        <i class="bi bi-box-arrow-right me-3"></i> Keluar
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>