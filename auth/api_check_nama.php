<?php
require_once __DIR__ . '/../config/koneksi.php';

header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_pengguna'])) {
    echo json_encode(['exists' => false, 'error' => 'Unauthorized']);
    exit;
}

$id_pengguna = $_SESSION['id_pengguna'];
$nama_kategori = $_GET['nama_kategori'] ?? '';
$exclude_id = $_GET['exclude_id'] ?? 0;

$sql = "SELECT id_kategori FROM kategori WHERE nama_kategori = ? AND id_pengguna = ?";
$params = [$nama_kategori, $id_pengguna];

if ($exclude_id > 0) {
    $sql .= " AND id_kategori != ?";
    $params[] = $exclude_id;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
echo json_encode(['exists' => $stmt->fetch() !== false]);
exit;
