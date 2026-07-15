<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("DELETE FROM prestasi WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['success'] = 'Prestasi berhasil dihapus!';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Gagal menghapus data';
    }
}

header('Location: index.php');
exit();
?>