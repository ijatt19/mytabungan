<?php
/**
 * Database Configuration
 * MyTabungan - Personal Finance Management
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_NAME', 'tabungan_my');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

/**
 * Get PDO Database Connection
 * @return PDO|null
 */
function getConnection(): ?PDO {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];
            
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
            
        } catch (PDOException $e) {
            // Log error in production, show for development
            error_log("Database Connection Error: " . $e->getMessage());
            
            if (defined('DEBUG_MODE') && DEBUG_MODE) {
                die("Database Connection Failed: " . $e->getMessage());
            } else {
                die("Database Connection Failed. Please check your configuration.");
            }
        }
    }
    
    return $pdo;
}

/**
 * Close Database Connection
 */
function closeConnection(): void {
    // PDO connection closes automatically when script ends
    // This function is for explicit closure if needed
}
