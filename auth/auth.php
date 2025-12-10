<?php
/**
 * Authentication Logic
 * MyTabungan - Personal Finance Management
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

/**
 * Register a new user
 * @param string $nama
 * @param string $email
 * @param string $password
 * @return array [success, message, user_id]
 */
function registerUser(string $nama, string $email, string $password): array {
    $pdo = getConnection();
    
    // Validate inputs
    $nama = sanitize($nama);
    $email = sanitize($email);
    
    if (empty($nama) || empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'Semua field harus diisi', 'user_id' => null];
    }
    
    if (!isValidEmail($email)) {
        return ['success' => false, 'message' => 'Format email tidak valid', 'user_id' => null];
    }
    
    $passwordValidation = validatePassword($password);
    if (!$passwordValidation['valid']) {
        return ['success' => false, 'message' => $passwordValidation['message'], 'user_id' => null];
    }
    
    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id_pengguna FROM pengguna WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Email sudah terdaftar', 'user_id' => null];
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert new user
    try {
        $stmt = $pdo->prepare("INSERT INTO pengguna (nama, email, password) VALUES (?, ?, ?)");
        $stmt->execute([$nama, $email, $hashedPassword]);
        
        $userId = $pdo->lastInsertId();
        
        return ['success' => true, 'message' => 'Registrasi berhasil! Silakan login.', 'user_id' => $userId];
        
    } catch (PDOException $e) {
        error_log("Registration Error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Terjadi kesalahan. Silakan coba lagi.', 'user_id' => null];
    }
}

/**
 * Login user
 * @param string $email
 * @param string $password
 * @return array [success, message, user]
 */
function loginUser(string $email, string $password): array {
    $pdo = getConnection();
    
    $email = sanitize($email);
    
    if (empty($email) || empty($password)) {
        return ['success' => false, 'message' => 'Email dan password harus diisi', 'user' => null];
    }
    
    // Find user by email
    $stmt = $pdo->prepare("SELECT * FROM pengguna WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if (!$user) {
        return ['success' => false, 'message' => 'Email atau password salah', 'user' => null];
    }
    
    // Verify password
    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Email atau password salah', 'user' => null];
    }
    
    // Create session
    $_SESSION['user_id'] = $user['id_pengguna'];
    $_SESSION['user_name'] = $user['nama'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['logged_in_at'] = time();
    
    // Regenerate session ID for security
    session_regenerate_id(true);
    
    return ['success' => true, 'message' => 'Login berhasil!', 'user' => $user];
}

/**
 * Logout user
 */
function logoutUser(): void {
    // Clear all session data
    $_SESSION = [];
    
    // Delete session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy session
    session_destroy();
}

/**
 * Get user by ID
 * @param int $userId
 * @return array|null
 */
function getUserById(int $userId): ?array {
    $pdo = getConnection();
    
    $stmt = $pdo->prepare("SELECT id_pengguna, nama, email, created_at FROM pengguna WHERE id_pengguna = ?");
    $stmt->execute([$userId]);
    
    return $stmt->fetch() ?: null;
}

/**
 * Update user profile
 * @param int $userId
 * @param string $nama
 * @param string $email
 * @return array [success, message]
 */
function updateUserProfile(int $userId, string $nama, string $email): array {
    $pdo = getConnection();
    
    $nama = sanitize($nama);
    $email = sanitize($email);
    
    if (empty($nama) || empty($email)) {
        return ['success' => false, 'message' => 'Nama dan email harus diisi'];
    }
    
    if (!isValidEmail($email)) {
        return ['success' => false, 'message' => 'Format email tidak valid'];
    }
    
    // Check if email is taken by another user
    $stmt = $pdo->prepare("SELECT id_pengguna FROM pengguna WHERE email = ? AND id_pengguna != ?");
    $stmt->execute([$email, $userId]);
    
    if ($stmt->fetch()) {
        return ['success' => false, 'message' => 'Email sudah digunakan'];
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE pengguna SET nama = ?, email = ? WHERE id_pengguna = ?");
        $stmt->execute([$nama, $email, $userId]);
        
        // Update session
        $_SESSION['user_name'] = $nama;
        $_SESSION['user_email'] = $email;
        
        return ['success' => true, 'message' => 'Profil berhasil diperbarui'];
        
    } catch (PDOException $e) {
        error_log("Profile Update Error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Terjadi kesalahan'];
    }
}

/**
 * Update user password
 * @param int $userId
 * @param string $currentPassword
 * @param string $newPassword
 * @return array [success, message]
 */
function updateUserPassword(int $userId, string $currentPassword, string $newPassword): array {
    $pdo = getConnection();
    
    if (empty($currentPassword) || empty($newPassword)) {
        return ['success' => false, 'message' => 'Password lama dan baru harus diisi'];
    }
    
    $passwordValidation = validatePassword($newPassword);
    if (!$passwordValidation['valid']) {
        return ['success' => false, 'message' => $passwordValidation['message']];
    }
    
    // Get current user
    $stmt = $pdo->prepare("SELECT password FROM pengguna WHERE id_pengguna = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user || !password_verify($currentPassword, $user['password'])) {
        return ['success' => false, 'message' => 'Password lama tidak sesuai'];
    }
    
    // Check if new password is same as old password
    if (password_verify($newPassword, $user['password'])) {
        return ['success' => false, 'message' => 'Password baru tidak boleh sama dengan password lama'];
    }
    
    // Update password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("UPDATE pengguna SET password = ? WHERE id_pengguna = ?");
        $stmt->execute([$hashedPassword, $userId]);
        
        return ['success' => true, 'message' => 'Password berhasil diperbarui'];
        
    } catch (PDOException $e) {
        error_log("Password Update Error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Terjadi kesalahan'];
    }
}

/**
 * Check if email exists in database
 * @param string $email
 * @return array [exists, user_id, nama]
 */
function checkEmailExists(string $email): array {
    $pdo = getConnection();
    $email = sanitize($email);
    
    if (empty($email) || !isValidEmail($email)) {
        return ['exists' => false, 'user_id' => null, 'nama' => null];
    }
    
    $stmt = $pdo->prepare("SELECT id_pengguna, nama FROM pengguna WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();
    
    if ($user) {
        return ['exists' => true, 'user_id' => $user['id_pengguna'], 'nama' => $user['nama']];
    }
    
    return ['exists' => false, 'user_id' => null, 'nama' => null];
}

/**
 * Reset password by email (for forgot password feature)
 * @param string $email
 * @param string $newPassword
 * @return array [success, message]
 */
function resetPasswordByEmail(string $email, string $newPassword): array {
    $pdo = getConnection();
    $email = sanitize($email);
    
    // Validate email
    if (empty($email) || !isValidEmail($email)) {
        return ['success' => false, 'message' => 'Email tidak valid'];
    }
    
    // Validate new password
    $passwordValidation = validatePassword($newPassword);
    if (!$passwordValidation['valid']) {
        return ['success' => false, 'message' => $passwordValidation['message']];
    }
    
    // Check if email exists
    $emailCheck = checkEmailExists($email);
    if (!$emailCheck['exists']) {
        return ['success' => false, 'message' => 'Email tidak ditemukan'];
    }
    
    // Update password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    
    try {
        $stmt = $pdo->prepare("UPDATE pengguna SET password = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, $email]);
        
        return ['success' => true, 'message' => 'Password berhasil direset'];
        
    } catch (PDOException $e) {
        error_log("Password Reset Error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Terjadi kesalahan'];
    }
}

/**
 * Delete user account and all associated data
 * @param int $userId
 * @param string $password Current password for verification
 * @return array [success, message]
 */
function deleteUserAccount(int $userId, string $password): array {
    $pdo = getConnection();
    
    if (empty($password)) {
        return ['success' => false, 'message' => 'Password harus diisi untuk konfirmasi'];
    }
    
    // Get user and verify password
    $stmt = $pdo->prepare("SELECT password FROM pengguna WHERE id_pengguna = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!$user) {
        return ['success' => false, 'message' => 'User tidak ditemukan'];
    }
    
    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Password salah'];
    }
    
    try {
        // Delete share viewers (ignore if table doesn't exist)
        try {
            $stmt = $pdo->prepare("DELETE FROM share_viewers WHERE id_pengguna = ?");
            $stmt->execute([$userId]);
        } catch (PDOException $e) {
            // Table might not exist, ignore
        }
        
        // Delete share tokens
        try {
            $stmt = $pdo->prepare("DELETE FROM share_token WHERE id_pengguna = ?");
            $stmt->execute([$userId]);
        } catch (PDOException $e) {
            // Ignore if fails
        }
        
        // Delete transactions
        $stmt = $pdo->prepare("DELETE FROM transaksi WHERE id_pengguna = ?");
        $stmt->execute([$userId]);
        
        // Delete wishlist
        $stmt = $pdo->prepare("DELETE FROM wishlist WHERE id_pengguna = ?");
        $stmt->execute([$userId]);
        
        // Delete categories
        $stmt = $pdo->prepare("DELETE FROM kategori WHERE id_pengguna = ?");
        $stmt->execute([$userId]);
        
        // Delete user
        $stmt = $pdo->prepare("DELETE FROM pengguna WHERE id_pengguna = ?");
        $stmt->execute([$userId]);
        
        // Destroy session
        session_destroy();
        
        return ['success' => true, 'message' => 'Akun berhasil dihapus'];
        
    } catch (PDOException $e) {
        error_log("Delete Account Error: " . $e->getMessage());
        return ['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()];
    }
}
