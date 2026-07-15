<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id) {
    try {
        $pdo = getConnection();
        
        // Ambil data untuk hapus foto
        $stmt = $pdo->prepare("SELECT foto FROM guru WHERE id = ?");
        $stmt->execute([$id]);
        $guru = $stmt->fetch();
        
        // Hapus file foto
        if ($guru && $guru['foto'] && file_exists('../../uploads/guru/' . $guru['foto'])) {
            unlink('../../uploads/guru/' . $guru['foto']);
        }
        
        // Hapus data
        $stmt = $pdo->prepare("DELETE FROM guru WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['success'] = 'Data guru berhasil dihapus!';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Gagal menghapus data: ' . $e->getMessage();
    }
}

header('Location: index.php');
exit();
?>