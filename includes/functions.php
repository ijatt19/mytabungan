<?php
/**
 * Helper Functions
 * MyTabungan - Personal Finance Management
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Sanitize input string
 * @param string $data
 * @return string
 */
function sanitize(string $data): string {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Get base URL of the application
 * @return string
 */
function getBaseUrl(): string {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $path = dirname($_SERVER['SCRIPT_NAME']);
    return rtrim($protocol . '://' . $host . $path, '/');
}

/**
 * Format number as Indonesian Rupiah
 * @param float|int $amount
 * @return string
 */
function formatRupiah($amount): string {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Format number as short currency (K, M, B)
 * @param float|int $amount
 * @return string
 */
function formatShortCurrency($amount): string {
    if ($amount >= 1000000000) {
        return 'Rp ' . number_format($amount / 1000000000, 1, ',', '.') . 'M';
    } elseif ($amount >= 1000000) {
        return 'Rp ' . number_format($amount / 1000000, 1, ',', '.') . 'Jt';
    } elseif ($amount >= 1000) {
        return 'Rp ' . number_format($amount / 1000, 1, ',', '.') . 'Rb';
    }
    return formatRupiah($amount);
}

/**
 * Format date to Indonesian locale
 * @param string $date
 * @param string $format
 * @return string
 */
function formatTanggal(string $date, string $format = 'long'): string {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    
    $hari = [
        'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
        'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
    ];
    
    $timestamp = strtotime($date);
    
    if ($format === 'long') {
        // Example: Senin, 8 Desember 2025
        $dayName = $hari[date('l', $timestamp)];
        $day = date('j', $timestamp);
        $month = $bulan[(int)date('n', $timestamp)];
        $year = date('Y', $timestamp);
        return "$dayName, $day $month $year";
    } elseif ($format === 'short') {
        // Example: 8 Des 2025
        $day = date('j', $timestamp);
        $month = substr($bulan[(int)date('n', $timestamp)], 0, 3);
        $year = date('Y', $timestamp);
        return "$day $month $year";
    } elseif ($format === 'relative') {
        // Example: Hari ini, Kemarin, 2 hari lalu
        $now = time();
        $diff = floor(($now - $timestamp) / 86400);
        
        if ($diff === 0) return 'Hari ini';
        if ($diff === 1) return 'Kemarin';
        if ($diff < 7) return "$diff hari lalu";
        if ($diff < 30) return floor($diff / 7) . ' minggu lalu';
        if ($diff < 365) return floor($diff / 30) . ' bulan lalu';
        return floor($diff / 365) . ' tahun lalu';
    }
    
    return date('d/m/Y', $timestamp);
}

/**
 * Check if user is logged in
 * @return bool
 */
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Require user to be logged in
 * Redirects to login page if not authenticated
 */
function requireLogin(): void {
    if (!isLoggedIn()) {
        setFlashMessage('error', 'Silakan login terlebih dahulu.');
        redirect('login.php');
        exit;
    }
}

/**
 * Get current user ID
 * @return int|null
 */
function getCurrentUserId(): ?int {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Get current user name
 * @return string|null
 */
function getCurrentUserName(): ?string {
    return $_SESSION['user_name'] ?? null;
}

/**
 * Get current user email
 * @return string|null
 */
function getCurrentUserEmail(): ?string {
    return $_SESSION['user_email'] ?? null;
}

/**
 * Set flash message
 * @param string $type (success, error, warning, info)
 * @param string $message
 */
function setFlashMessage(string $type, string $message): void {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 * @return array|null
 */
function getFlashMessage(): ?array {
    $message = $_SESSION['flash_message'] ?? null;
    unset($_SESSION['flash_message']);
    return $message;
}

/**
 * Display flash message as Toast notification
 * @return string
 */
function displayFlashMessage(): string {
    $flash = getFlashMessage();
    
    if (!$flash) return '';
    
    $styles = [
        'success' => 'bg-emerald-500 text-white',
        'error' => 'bg-red-500 text-white',
        'warning' => 'bg-amber-400 text-amber-900',
        'info' => 'bg-blue-500 text-white'
    ];
    
    $icons = [
        'success' => 'bi-check-circle-fill',
        'error' => 'bi-x-circle-fill',
        'warning' => 'bi-exclamation-triangle-fill',
        'info' => 'bi-info-circle-fill'
    ];
    
    $styleClass = $styles[$flash['type']] ?? $styles['info'];
    $iconClass = $icons[$flash['type']] ?? $icons['info'];
    
    return <<<HTML
    <div id="toast-notification" class="fixed top-4 right-4 z-50 max-w-sm animate-fade-in">
        <div class="$styleClass px-4 py-3 rounded-xl shadow-lg flex items-center gap-3">
            <i class="bi $iconClass text-lg"></i>
            <span class="flex-1 text-sm font-medium">{$flash['message']}</span>
            <button type="button" class="hover:opacity-70" onclick="this.closest('#toast-notification').remove()">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>
    </div>
    <script>
        setTimeout(() => {
            const toast = document.getElementById('toast-notification');
            if (toast) {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-10px)';
                toast.style.transition = 'all 0.3s ease-out';
                setTimeout(() => toast.remove(), 300);
            }
        }, 2000);
    </script>
    HTML;
}

/**
 * Redirect to another page
 * @param string $url
 */
function redirect(string $url): void {
    header("Location: $url");
    exit;
}

/**
 * Generate CSRF token
 * @return string
 */
function generateCsrfToken(): string {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 * @param string $token
 * @return bool
 */
function validateCsrfToken(string $token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Output CSRF hidden input field
 * @return string
 */
function csrfField(): string {
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Calculate percentage
 * @param float $part
 * @param float $total
 * @return float
 */
function calculatePercentage(float $part, float $total): float {
    if ($total === 0.0) return 0;
    return min(100, round(($part / $total) * 100, 1));
}

/**
 * Get month name in Indonesian
 * @param int $month (1-12)
 * @return string
 */
function getMonthName(int $month): string {
    $bulan = [
        1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
    ];
    return $bulan[$month] ?? '';
}

/**
 * Validate email format
 * @param string $email
 * @return bool
 */
function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate password strength
 * @param string $password
 * @return array [valid => bool, message => string]
 */
function validatePassword(string $password): array {
    if (strlen($password) < 6) {
        return ['valid' => false, 'message' => 'Password minimal 6 karakter'];
    }
    return ['valid' => true, 'message' => ''];
}

/**
 * JSON response for AJAX
 * @param bool $success
 * @param string $message
 * @param array $data
 */
function jsonResponse(bool $success, string $message, array $data = []): void {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

/**
 * Get financial health status (Hybrid: considers monthly ratio + balance)
 * @param float $income Monthly income
 * @param float $expense Monthly expense
 * @param float $balance Total balance (optional, default 0)
 * @return array [status, color, icon, message]
 */
function getFinancialHealth(float $income, float $expense, float $balance = 0): array {
    // Priority 1: Negative balance is always critical
    if ($balance < 0) {
        return [
            'status' => 'Kritis',
            'color' => 'red',
            'icon' => 'bi-exclamation-octagon',
            'message' => 'Saldo negatif! Segera kurangi pengeluaran.'
        ];
    }
    
    // No income data
    if ($income === 0.0) {
        if ($balance > 0) {
            return [
                'status' => 'Belum Ada Pemasukan',
                'color' => 'gray',
                'icon' => 'bi-question-circle',
                'message' => 'Belum ada pemasukan bulan ini, saldo masih ada.'
            ];
        }
        return [
            'status' => 'Belum Ada Data',
            'color' => 'gray',
            'icon' => 'bi-question-circle',
            'message' => 'Tambahkan transaksi untuk melihat kondisi keuangan'
        ];
    }
    
    $ratio = $expense / $income;
    $lowBalance = $balance < 500000; // Warning if balance < 500k
    
    if ($ratio <= 0.5 && !$lowBalance) {
        return [
            'status' => 'Sehat',
            'color' => 'emerald',
            'icon' => 'bi-shield-check',
            'message' => 'Keuangan Anda dalam kondisi sangat baik!'
        ];
    } elseif ($ratio <= 0.5 && $lowBalance) {
        return [
            'status' => 'Perlu Perhatian',
            'color' => 'yellow',
            'icon' => 'bi-exclamation-circle',
            'message' => 'Rasio bagus, tapi saldo rendah. Tingkatkan tabungan.'
        ];
    } elseif ($ratio <= 0.75) {
        return [
            'status' => 'Aman',
            'color' => 'teal',
            'icon' => 'bi-check-circle',
            'message' => 'Keuangan Anda cukup stabil'
        ];
    } elseif ($ratio <= 0.9) {
        return [
            'status' => 'Waspada',
            'color' => 'yellow',
            'icon' => 'bi-exclamation-triangle',
            'message' => 'Pengeluaran mendekati batas, kurangi belanja'
        ];
    } else {
        return [
            'status' => 'Bahaya',
            'color' => 'red',
            'icon' => 'bi-x-circle',
            'message' => 'Pengeluaran melebihi pemasukan!'
        ];
    }
}

/**
 * Get pagination data
 * @param int $totalItems Total number of items
 * @param int $perPage Items per page
 * @param int $currentPage Current page number
 * @return array [offset, limit, totalPages, currentPage]
 */
function getPagination(int $totalItems, int $perPage = 10, int $currentPage = 1): array {
    $totalPages = max(1, ceil($totalItems / $perPage));
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    
    return [
        'offset' => $offset,
        'limit' => $perPage,
        'totalPages' => $totalPages,
        'currentPage' => $currentPage,
        'totalItems' => $totalItems,
        'perPage' => $perPage
    ];
}

/**
 * Render pagination HTML
 * @param array $pagination Pagination data from getPagination()
 * @param string $baseUrl Base URL with query params (without page param)
 * @return string HTML pagination
 */
function renderPagination(array $pagination, string $baseUrl = ''): string {
    if ($pagination['totalPages'] <= 1) return '';
    
    $separator = strpos($baseUrl, '?') !== false ? '&' : '?';
    $html = '<div class="flex items-center justify-center gap-2 mt-6">';
    
    // Previous button
    if ($pagination['currentPage'] > 1) {
        $prevPage = $pagination['currentPage'] - 1;
        $html .= '<a href="' . $baseUrl . $separator . 'page=' . $prevPage . '" class="px-3 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors"><i class="bi bi-chevron-left"></i></a>';
    } else {
        $html .= '<span class="px-3 py-2 rounded-lg bg-slate-50 text-slate-300 cursor-not-allowed"><i class="bi bi-chevron-left"></i></span>';
    }
    
    // Page numbers
    $startPage = max(1, $pagination['currentPage'] - 2);
    $endPage = min($pagination['totalPages'], $pagination['currentPage'] + 2);
    
    if ($startPage > 1) {
        $html .= '<a href="' . $baseUrl . $separator . 'page=1" class="px-3 py-2 rounded-lg hover:bg-slate-100 text-slate-600 transition-colors">1</a>';
        if ($startPage > 2) {
            $html .= '<span class="px-2 text-slate-400">...</span>';
        }
    }
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        if ($i == $pagination['currentPage']) {
            $html .= '<span class="px-3 py-2 rounded-lg bg-emerald-500 text-white font-medium">' . $i . '</span>';
        } else {
            $html .= '<a href="' . $baseUrl . $separator . 'page=' . $i . '" class="px-3 py-2 rounded-lg hover:bg-slate-100 text-slate-600 transition-colors">' . $i . '</a>';
        }
    }
    
    if ($endPage < $pagination['totalPages']) {
        if ($endPage < $pagination['totalPages'] - 1) {
            $html .= '<span class="px-2 text-slate-400">...</span>';
        }
        $html .= '<a href="' . $baseUrl . $separator . 'page=' . $pagination['totalPages'] . '" class="px-3 py-2 rounded-lg hover:bg-slate-100 text-slate-600 transition-colors">' . $pagination['totalPages'] . '</a>';
    }
    
    // Next button
    if ($pagination['currentPage'] < $pagination['totalPages']) {
        $nextPage = $pagination['currentPage'] + 1;
        $html .= '<a href="' . $baseUrl . $separator . 'page=' . $nextPage . '" class="px-3 py-2 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 transition-colors"><i class="bi bi-chevron-right"></i></a>';
    } else {
        $html .= '<span class="px-3 py-2 rounded-lg bg-slate-50 text-slate-300 cursor-not-allowed"><i class="bi bi-chevron-right"></i></span>';
    }
    
    $html .= '</div>';
    
    // Info text
    $start = $pagination['offset'] + 1;
    $end = min($pagination['offset'] + $pagination['perPage'], $pagination['totalItems']);
    $html .= '<p class="text-center text-xs text-slate-400 mt-2">Menampilkan ' . $start . '-' . $end . ' dari ' . $pagination['totalItems'] . ' data</p>';
    
    return $html;
}
