<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id) {
    try {
        $pdo = getConnection();
        
        $stmt = $pdo->prepare("SELECT gambar FROM fasilitas WHERE id = ?");
        $stmt->execute([$id]);
        $f = $stmt->fetch();
        
        if ($f && $f['gambar'] && file_exists('../../uploads/fasilitas/' . $f['gambar'])) {
            unlink('../../uploads/fasilitas/' . $f['gambar']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM fasilitas WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['success'] = 'Fasilitas berhasil dihapus!';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Gagal menghapus data';
    }
}

header('Location: index.php');
exit();
?>