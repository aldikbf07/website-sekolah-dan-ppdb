<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id) {
    try {
        $pdo = getConnection();
        
        $stmt = $pdo->prepare("SELECT gambar FROM berita WHERE id = ?");
        $stmt->execute([$id]);
        $berita = $stmt->fetch();
        
        if ($berita && $berita['gambar'] && file_exists('../../uploads/berita/' . $berita['gambar'])) {
            unlink('../../uploads/berita/' . $berita['gambar']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM berita WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['success'] = 'Berita berhasil dihapus!';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Gagal menghapus berita';
    }
}

header('Location: index.php');
exit();
?>