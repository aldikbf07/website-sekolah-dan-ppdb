<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$status = $_GET['status'] ?? '';

if ($id && in_array($status, ['diterima', 'ditolak', 'pending'])) {
    $pdo = getConnection();
    $stmt = $pdo->prepare("UPDATE pendaftar SET status = ? WHERE id = ?");
    $stmt->execute([$status, $id]);
}

header('Location: index.php');
exit();
?>