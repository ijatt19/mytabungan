<nav id="sidebarMenu" class="fixed top-0 left-0 z-50 h-full w-64 bg-white/95 backdrop-blur-xl border-r border-gray-100 shadow-2xl transition-transform duration-300 -translate-x-full md:translate-x-0">
    <div class="h-full flex flex-col">
        <!-- Logo Section -->
        <a href="/dashboard.php" class="flex items-center justify-center h-20 border-b border-gray-100 no-underline group">
            <div class="flex items-center gap-3 px-6">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-600 flex items-center justify-center text-white shadow-lg shadow-emerald-200 group-hover:scale-110 transition-transform duration-300">
                    <i class="bi bi-wallet2 text-xl"></i>
                </div>
                <span class="text-xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent">MyTabungan</span>
            </div>
        </a>

        <!-- Navigation Links -->
        <ul class="flex flex-col gap-1.5 p-4 flex-1 overflow-y-auto">
            <li class="px-2">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider px-3 mb-2 block">Menu Utama</span>
            </li>
            
            <li>
                <a class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group <?php echo (strpos($_SERVER['REQUEST_URI'], 'dashboard.php') !== false) ? 'bg-emerald-50 text-emerald-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-emerald-600'; ?>" href="/dashboard.php">
                    <i class="bi bi-grid-1x2 mr-3 text-lg transition-transform group-hover:scale-110 <?php echo (strpos($_SERVER['REQUEST_URI'], 'dashboard.php') !== false) ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500'; ?>"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group <?php echo (strpos($_SERVER['REQUEST_URI'], '/transaksi/') !== false) ? 'bg-emerald-50 text-emerald-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-emerald-600'; ?>" href="/transaksi/index.php">
                    <i class="bi bi-arrow-left-right mr-3 text-lg transition-transform group-hover:scale-110 <?php echo (strpos($_SERVER['REQUEST_URI'], '/transaksi/') !== false) ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500'; ?>"></i>
                    Transaksi
                </a>
            </li>
            <li>
                <a class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group <?php echo (strpos($_SERVER['REQUEST_URI'], '/kategori/') !== false) ? 'bg-emerald-50 text-emerald-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-emerald-600'; ?>" href="/kategori/index.php">
                    <i class="bi bi-tags mr-3 text-lg transition-transform group-hover:scale-110 <?php echo (strpos($_SERVER['REQUEST_URI'], '/kategori/') !== false) ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500'; ?>"></i>
                    Kategori
                </a>
            </li>
            <li>
                <a class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group <?php echo (strpos($_SERVER['REQUEST_URI'], '/wishlist/') !== false) ? 'bg-emerald-50 text-emerald-700 shadow-sm' : 'text-gray-600 hover:bg-gray-50 hover:text-emerald-600'; ?>" href="/wishlist/index.php">
                    <i class="bi bi-gem mr-3 text-lg transition-transform group-hover:scale-110 <?php echo (strpos($_SERVER['REQUEST_URI'], '/wishlist/') !== false) ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500'; ?>"></i>
                    Wishlist
                </a>
            </li>
        </ul>

        <!-- Footer Actions -->
        <div class="p-4 border-t border-gray-100 bg-gray-50/50">
            <ul class="flex flex-col gap-1.5">
                <li>
                    <a class="flex items-center px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200 group <?php echo (strpos($_SERVER['REQUEST_URI'], 'profile.php') !== false) ? 'bg-emerald-50 text-emerald-700 shadow-sm' : 'text-gray-600 hover:bg-white hover:text-emerald-600 hover:shadow-sm'; ?>" href="/profile.php">
                        <i class="bi bi-person-circle mr-3 text-lg transition-transform group-hover:scale-110 <?php echo (strpos($_SERVER['REQUEST_URI'], 'profile.php') !== false) ? 'text-emerald-600' : 'text-gray-400 group-hover:text-emerald-500'; ?>"></i>
                        Profile
                    </a>
                </li>
                <li>
                    <a class="flex items-center px-4 py-3 rounded-xl text-sm font-medium text-red-600 hover:bg-red-50 hover:text-red-700 transition-all duration-200 group" href="/auth/logout.php">
                        <i class="bi bi-box-arrow-right mr-3 text-lg text-red-400 group-hover:text-red-600 transition-transform group-hover:translate-x-1"></i>
                        Keluar
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>