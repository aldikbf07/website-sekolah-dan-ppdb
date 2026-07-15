<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id) {
    try {
        $pdo = getConnection();
        
        // Ambil data pendaftar untuk hapus file
        $stmt = $pdo->prepare("SELECT * FROM pendaftar WHERE id = ?");
        $stmt->execute([$id]);
        $pendaftar = $stmt->fetch();
        
        if ($pendaftar) {
            // Hapus file foto
            if ($pendaftar['file_foto'] && file_exists('../../uploads/foto/' . $pendaftar['file_foto'])) {
                unlink('../../uploads/foto/' . $pendaftar['file_foto']);
            }
            
            // Hapus file akta
            if ($pendaftar['file_akta'] && file_exists('../../uploads/akta/' . $pendaftar['file_akta'])) {
                unlink('../../uploads/akta/' . $pendaftar['file_akta']);
            }
            
            // Hapus file KK
            if ($pendaftar['file_kk'] && file_exists('../../uploads/kk/' . $pendaftar['file_kk'])) {
                unlink('../../uploads/kk/' . $pendaftar['file_kk']);
            }
            
            // Hapus dokumen penerimaan jika ada
            $stmt_dok = $pdo->prepare("SELECT * FROM dokumen_penerimaan WHERE pendaftar_id = ?");
            $stmt_dok->execute([$id]);
            $dokumen_list = $stmt_dok->fetchAll();
            
            foreach ($dokumen_list as $dok) {
                if ($dok['file_dokumen'] && file_exists('../../uploads/dokumen/' . $dok['file_dokumen'])) {
                    unlink('../../uploads/dokumen/' . $dok['file_dokumen']);
                }
            }
            
            // Hapus data dokumen
            $stmt = $pdo->prepare("DELETE FROM dokumen_penerimaan WHERE pendaftar_id = ?");
            $stmt->execute([$id]);
            
            // Hapus data pendaftar
            $stmt = $pdo->prepare("DELETE FROM pendaftar WHERE id = ?");
            $stmt->execute([$id]);
            
            $_SESSION['success'] = 'Data pendaftar berhasil dihapus beserta semua berkasnya!';
        } else {
            $_SESSION['error'] = 'Data pendaftar tidak ditemukan';
        }
        
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Gagal menghapus data: ' . $e->getMessage();
    }
} else {
    $_SESSION['error'] = 'ID tidak valid';
}

header('Location: index.php');
exit();
?>