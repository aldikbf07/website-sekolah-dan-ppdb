<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id) {
    try {
        $pdo = getConnection();
        
        $stmt = $pdo->prepare("SELECT gambar FROM galeri WHERE id = ?");
        $stmt->execute([$id]);
        $galeri = $stmt->fetch();
        
        if ($galeri && $galeri['gambar'] && file_exists('../../uploads/galeri/' . $galeri['gambar'])) {
            unlink('../../uploads/galeri/' . $galeri['gambar']);
        }
        
        $stmt = $pdo->prepare("DELETE FROM galeri WHERE id = ?");
        $stmt->execute([$id]);
        
        $_SESSION['success'] = 'Foto berhasil dihapus!';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Gagal menghapus foto';
    }
}

header('Location: index.php');
exit();
?>