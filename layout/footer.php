    </main>
</div>

<!-- Footer Copyright (Desktop only, fixed at bottom) -->
<footer class="hidden lg:block fixed bottom-0 left-0 right-0 lg:left-64 py-4 text-center text-sm text-slate-400 bg-white/80 backdrop-blur-sm border-t border-slate-100 z-30">
    <p>© <?= date('Y') ?> <span class="font-medium text-emerald-600">MyTabungan</span>. Izzat Fakhar Assyakur</p>
</footer>

<!-- Mobile Bottom Navigation -->
<nav class="mobile-nav lg:hidden flex justify-around items-center">
    <?php
    $mobileNavItems = [
        ['name' => 'Beranda', 'icon' => 'bi-house', 'activeIcon' => 'bi-house-fill', 'href' => 'index.php', 'page' => 'index'],
        ['name' => 'Transaksi', 'icon' => 'bi-arrow-left-right', 'activeIcon' => 'bi-arrow-left-right', 'href' => 'transaksi.php', 'page' => 'transaksi'],
        ['name' => 'Kategori', 'icon' => 'bi-tags', 'activeIcon' => 'bi-tags-fill', 'href' => 'kategori.php', 'page' => 'kategori'],
        ['name' => 'Wishlist', 'icon' => 'bi-heart', 'activeIcon' => 'bi-heart-fill', 'href' => 'wishlist.php', 'page' => 'wishlist'],
    ];
    
    foreach ($mobileNavItems as $item):
        $isActive = $currentPage === $item['page'];
    ?>
    <a href="<?= $item['href'] ?>" class="mobile-nav-item <?= $isActive ? 'active' : '' ?>">
        <i class="bi <?= $isActive ? $item['activeIcon'] : $item['icon'] ?>"></i>
        <span><?= $item['name'] ?></span>
    </a>
    <?php endforeach; ?>
</nav>

<!-- Core JavaScript Modules -->
<script src="assets/js/core.js"></script>
<script src="assets/js/sidebar.js"></script>
<script src="assets/js/modal.js"></script>
<script src="assets/js/forms.js"></script>
<script src="assets/js/ui.js"></script>
<script src="assets/js/charts.js"></script>

<?php if (isset($pageScripts) && is_array($pageScripts)): ?>
    <?php foreach ($pageScripts as $script): ?>
    <script src="<?= $script ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
