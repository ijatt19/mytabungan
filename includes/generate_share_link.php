<?php
require_once __DIR__ . '/../auth/cek_masuk.php';
require_once __DIR__ . '/../config/koneksi.php';

header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Terjadi kesalahan.'];

try {
    $id_pengguna = $_SESSION['id_pengguna'];

    // 1. Generate a secure, unique token
    $token = bin2hex(random_bytes(32));

    // 2. Store the token in the database
    // Sebaiknya, hapus token lama jika ada untuk pengguna ini agar tidak menumpuk
    $sql_delete_old = "DELETE FROM share_links WHERE id_pengguna = ?";
    $stmt_delete = $pdo->prepare($sql_delete_old);
    $stmt_delete->execute([$id_pengguna]);
    
    // Masukkan token baru
    $sql_insert = "INSERT INTO share_links (id_pengguna, token) VALUES (?, ?)";
    $stmt_insert = $pdo->prepare($sql_insert);
    
    if ($stmt_insert->execute([$id_pengguna, $token])) {
        // 3. Construct the full URL
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https" : "http";
        $host = $_SERVER['HTTP_HOST'];
        
        // Base path logic: current script is in /includes/, and laporan_publik.php is ALSO in /includes/
        $current_dir = dirname($_SERVER['PHP_SELF']); // e.g., /tabung/includes
        
        // Ensure no trailing slashes
        $current_dir = rtrim($current_dir, '/\\');
        
        $share_url = "{$protocol}://{$host}{$current_dir}/laporan_publik.php?token={$token}";

        $response['success'] = true;
        $response['url'] = $share_url;
        $response['message'] = 'Link berhasil dibuat.';
    } else {
        $response['message'] = 'Gagal menyimpan token ke database.';
    }

} catch (PDOException $e) {
    // Sebaiknya tidak menampilkan error detail ke user di production
    $response['message'] = 'Database error.'; 
    // Untuk debugging: error_log($e->getMessage());
} catch (Exception $e) {
    $response['message'] = 'Terjadi kesalahan internal.';
    // Untuk debugging: error_log($e->getMessage());
}

echo json_encode($response);
exit;