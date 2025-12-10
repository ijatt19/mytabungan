<?php
/**
 * Share Financial Report Page
 * MyTabungan - Personal Finance Management
 */

require_once __DIR__ . '/auth/auth.php';
require_once __DIR__ . '/config/database.php';

// Require login
requireLogin();

$pageTitle = 'Bagikan Laporan';
$userId = getCurrentUserId();
$pdo = getConnection();

// Max shares per user
$maxShares = 5;

// Generate unique token
function generateToken(): string {
    return bin2hex(random_bytes(16));
}

// Handle Actions
$action = $_GET['action'] ?? '';

// Delete share
if ($action === 'delete' && isset($_GET['id'])) {
    $shareId = (int) $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM share_token WHERE id_share = ? AND id_pengguna = ?");
    $stmt->execute([$shareId, $userId]);
    setFlashMessage('success', 'Link share berhasil dihapus.');
    redirect('share.php');
}

// Handle POST - Create new share
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check limit
    $stmt = $pdo->prepare("SELECT COUNT(*) as cnt FROM share_token WHERE id_pengguna = ?");
    $stmt->execute([$userId]);
    $currentCount = $stmt->fetch()['cnt'];
    
    if ($currentCount >= $maxShares) {
        setFlashMessage('error', 'Maksimal ' . $maxShares . ' link share. Hapus yang lama terlebih dahulu.');
        redirect('share.php');
    }
    
    $title = sanitize($_POST['title'] ?? 'Laporan Keuangan');
    $expiryDays = (int) ($_POST['expiry_days'] ?? 7);
    
    $token = generateToken();
    $expiresAt = $expiryDays > 0 ? date('Y-m-d H:i:s', strtotime("+{$expiryDays} days")) : null;
    
    try {
        $stmt = $pdo->prepare("INSERT INTO share_token (id_pengguna, token, title, expires_at) VALUES (?, ?, ?, ?)");
        $stmt->execute([$userId, $token, $title, $expiresAt]);
        setFlashMessage('success', 'Link share berhasil dibuat! Salin dan bagikan ke orang lain.');
    } catch (PDOException $e) {
        error_log("Share Error: " . $e->getMessage());
        setFlashMessage('error', 'Gagal membuat link share.');
    }
    redirect('share.php');
}

// Get user's share tokens
$stmt = $pdo->prepare("SELECT * FROM share_token WHERE id_pengguna = ? ORDER BY created_at DESC");
$stmt->execute([$userId]);
$shareTokens = $stmt->fetchAll();

// Get viewers for each share token
function getShareViewers($pdo, $shareId) {
    try {
        $stmt = $pdo->prepare("
            SELECT sv.*, p.nama, p.email
            FROM share_viewers sv
            JOIN pengguna p ON sv.id_pengguna = p.id_pengguna
            WHERE sv.id_share = ?
            ORDER BY sv.viewed_at DESC
        ");
        $stmt->execute([$shareId]);
        return $stmt->fetchAll();
    } catch (PDOException $e) {
        return [];
    }
}

// Base URL for share links
$baseShareUrl = getBaseUrl() . '/view_share.php?token=';

// Include layout
include __DIR__ . '/layout/header.php';
include __DIR__ . '/layout/sidebar.php';
?>

<!-- Share Page Content -->
<div class="space-y-6">
    
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">Bagikan Laporan</h2>
            <p class="text-slate-500 mt-1">Buat link untuk berbagi laporan keuangan Anda</p>
        </div>
        <?php if (count($shareTokens) < $maxShares): ?>
        <button onclick="openShareModal()" class="btn btn-primary">
            <i class="bi bi-plus-lg"></i>
            <span>Buat Link Baru</span>
        </button>
        <?php endif; ?>
    </div>
    
    <!-- Info Card -->
    <div class="card p-4 bg-gradient-to-r from-sky-50 to-indigo-50 border border-sky-100">
        <div class="flex gap-4">
            <div class="w-12 h-12 rounded-xl bg-sky-100 flex items-center justify-center flex-shrink-0">
                <i class="bi bi-info-circle text-sky-600 text-xl"></i>
            </div>
            <div>
                <h4 class="font-semibold text-slate-700">Cara Kerja</h4>
                <ul class="text-sm text-slate-600 mt-2 space-y-1">
                    <li>• Buat link share dengan klik tombol "Buat Link Baru"</li>
                    <li>• Salin link dan kirim ke orang yang ingin melihat laporan Anda</li>
                    <li>• Penerima harus login untuk melihat laporan</li>
                    <li>• Link akan kadaluarsa sesuai waktu yang ditentukan</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Share Links List -->
    <div class="card overflow-hidden">
        <div class="p-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-800">Link Aktif</h3>
            <span class="text-sm text-slate-400"><?= count($shareTokens) ?>/<?= $maxShares ?></span>
        </div>
        
        <?php if (empty($shareTokens)): ?>
        <div class="empty-state py-12">
            <i class="bi bi-link-45deg"></i>
            <p class="font-medium text-slate-600 mb-1">Belum ada link share</p>
            <p class="text-sm">Klik "Buat Link Baru" untuk membuat link pertama</p>
        </div>
        <?php else: ?>
        <div class="divide-y divide-slate-100">
            <?php foreach ($shareTokens as $share): 
                $isExpired = $share['expires_at'] && strtotime($share['expires_at']) < time();
                $shareUrl = $baseShareUrl . $share['token'];
                $viewers = getShareViewers($pdo, $share['id_share']);
            ?>
            <div class="p-4 <?= $isExpired ? 'bg-slate-50 opacity-60' : '' ?>">
                <div class="flex flex-col lg:flex-row lg:items-center gap-4">
                    <!-- Info -->
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap">
                            <h4 class="font-semibold text-slate-800"><?= htmlspecialchars($share['title']) ?></h4>
                            <?php if ($isExpired): ?>
                            <span class="badge bg-red-100 text-red-600 text-xs">Kadaluarsa</span>
                            <?php else: ?>
                            <span class="badge bg-emerald-100 text-emerald-600 text-xs">Aktif</span>
                            <?php endif; ?>
                            <?php if (isset($share['view_count']) && $share['view_count'] > 0): ?>
                            <span class="badge bg-sky-100 text-sky-600 text-xs">
                                <i class="bi bi-eye mr-1"></i><?= $share['view_count'] ?> views
                            </span>
                            <?php endif; ?>
                        </div>
                        <p class="text-xs text-slate-400 mt-1 truncate font-mono"><?= $shareUrl ?></p>
                        <div class="flex items-center gap-4 mt-2 text-xs text-slate-500 flex-wrap">
                            <span><i class="bi bi-calendar mr-1"></i>Dibuat: <?= formatTanggal($share['created_at'], 'short') ?></span>
                            <?php if ($share['expires_at']): ?>
                            <span><i class="bi bi-clock mr-1"></i>Kadaluarsa: <?= formatTanggal($share['expires_at'], 'short') ?></span>
                            <?php else: ?>
                            <span><i class="bi bi-infinity mr-1"></i>Tidak kadaluarsa</span>
                            <?php endif; ?>
                            <?php if (isset($share['last_viewed_at']) && $share['last_viewed_at']): ?>
                            <span><i class="bi bi-eye mr-1"></i>Terakhir dilihat: <?= formatTanggal($share['last_viewed_at'], 'short') ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex gap-2">
                        <?php if (count($viewers) > 0): ?>
                        <button onclick="toggleViewers(<?= $share['id_share'] ?>)" class="btn btn-secondary btn-sm" title="Lihat siapa yang melihat">
                            <i class="bi bi-people"></i>
                            <span class="hidden sm:inline"><?= count($viewers) ?></span>
                        </button>
                        <?php endif; ?>
                        <?php if (!$isExpired): ?>
                        <button onclick="copyToClipboard('<?= $shareUrl ?>')" class="btn btn-secondary btn-sm">
                            <i class="bi bi-clipboard"></i>
                            <span class="hidden sm:inline">Salin</span>
                        </button>
                        <a href="<?= $shareUrl ?>" target="_blank" class="btn btn-secondary btn-sm">
                            <i class="bi bi-box-arrow-up-right"></i>
                            <span class="hidden sm:inline">Buka</span>
                        </a>
                        <?php endif; ?>
                        <a href="share.php?action=delete&id=<?= $share['id_share'] ?>" 
                           class="btn btn-danger btn-sm"
                           data-delete-confirm="Yakin ingin menghapus link share ini?">
                            <i class="bi bi-trash"></i>
                        </a>
                    </div>
                </div>
                
                <!-- Viewers List (Hidden by default) -->
                <?php if (count($viewers) > 0): ?>
                <div id="viewers-<?= $share['id_share'] ?>" class="hidden mt-4 pt-4 border-t border-slate-100">
                    <p class="text-xs font-medium text-slate-500 mb-3">
                        <i class="bi bi-people mr-1"></i>Dilihat oleh <?= count($viewers) ?> orang:
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <?php foreach ($viewers as $viewer): ?>
                        <div class="flex items-center gap-3 p-2 bg-slate-50 rounded-lg">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-sky-400 to-indigo-500 flex items-center justify-center text-white text-xs font-bold">
                                <?= strtoupper(substr($viewer['nama'], 0, 1)) ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-slate-700 truncate"><?= htmlspecialchars($viewer['nama']) ?></p>
                                <p class="text-xs text-slate-400"><?= formatTanggal($viewer['viewed_at'], 'short') ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Create Share Modal -->
<div id="shareModal" class="modal-overlay">
    <div class="modal-content p-6 max-w-md">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-xl font-bold text-slate-800">Buat Link Share</h3>
            <button data-modal-close class="p-2 hover:bg-slate-100 rounded-lg transition-colors">
                <i class="bi bi-x-lg text-slate-400"></i>
            </button>
        </div>
        
        <form method="POST" class="space-y-4">
            <div>
                <label class="form-label">Judul (opsional)</label>
                <input type="text" name="title" class="form-input" 
                       placeholder="Laporan Keuangan" value="Laporan Keuangan">
                <p class="text-xs text-slate-400 mt-1">Untuk memudahkan identifikasi link</p>
            </div>
            
            <div>
                <label class="form-label">Masa Berlaku</label>
                <select name="expiry_days" class="form-input">
                    <option value="1">1 Hari</option>
                    <option value="7" selected>7 Hari</option>
                    <option value="30">30 Hari</option>
                    <option value="90">90 Hari</option>
                    <option value="0">Tidak Kadaluarsa</option>
                </select>
            </div>
            
            <div class="p-3 bg-amber-50 rounded-xl border border-amber-100">
                <p class="text-sm text-amber-700">
                    <i class="bi bi-exclamation-triangle mr-1"></i>
                    <strong>Perhatian:</strong> Siapapun yang memiliki link dan sudah login bisa melihat laporan keuangan Anda.
                </p>
            </div>
            
            <div class="flex gap-3 pt-2">
                <button type="button" data-modal-close class="btn btn-secondary flex-1">Batal</button>
                <button type="submit" class="btn btn-primary flex-1">
                    <i class="bi bi-link-45deg"></i>
                    Buat Link
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function openShareModal() {
    document.getElementById('shareModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function copyToClipboard(text) {
    // Modern clipboard API (works on HTTPS)
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(() => {
            showCopyToast();
        }).catch(err => {
            fallbackCopyToClipboard(text);
        });
    } else {
        // Fallback for HTTP
        fallbackCopyToClipboard(text);
    }
}

function fallbackCopyToClipboard(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-9999px';
    textArea.style.top = '-9999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
        showCopyToast();
    } catch (err) {
        alert('Gagal menyalin. Silakan salin manual: ' + text);
    }
    
    document.body.removeChild(textArea);
}

function showCopyToast() {
    const toast = document.createElement('div');
    toast.className = 'fixed top-4 right-4 bg-emerald-500 text-white px-4 py-3 rounded-xl shadow-lg z-50 animate-fade-in';
    toast.innerHTML = '<i class="bi bi-check-circle mr-2"></i>Link berhasil disalin!';
    document.body.appendChild(toast);
    setTimeout(() => {
        toast.classList.add('opacity-0', 'transition-opacity');
        setTimeout(() => toast.remove(), 300);
    }, 2000);
}

// Close modal handlers
document.querySelectorAll('[data-modal-close]').forEach(btn => {
    btn.addEventListener('click', () => {
        document.getElementById('shareModal').classList.remove('active');
        document.body.style.overflow = '';
    });
});

// Toggle viewers list
function toggleViewers(shareId) {
    const viewersList = document.getElementById('viewers-' + shareId);
    if (viewersList) {
        viewersList.classList.toggle('hidden');
    }
}
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>
