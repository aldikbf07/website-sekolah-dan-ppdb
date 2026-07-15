<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit();
}

// Data dari form
$visi = trim($_POST['visi'] ?? '');
$misi = trim($_POST['misi'] ?? '');
$sejarah = trim($_POST['sejarah'] ?? '');
$alamat = trim($_POST['alamat'] ?? '');
$telepon = trim($_POST['telepon'] ?? '');
$email = trim($_POST['email'] ?? '');
$ids = $_POST['ids'] ?? [];

$data = [
    'visi' => $visi,
    'misi' => $misi,
    'sejarah' => $sejarah,
    'alamat' => $alamat,
    'telepon' => $telepon,
    'email' => $email,
];

try {
    $pdo = getConnection();
    
    $updated = 0;
    
    foreach ($data as $key => $value) {
        $id = isset($ids[$key]) ? (int)$ids[$key] : 0;
        
        if ($id > 0) {
            // Update existing
            $stmt = $pdo->prepare("UPDATE pengaturan SET `value` = ? WHERE id = ?");
            $stmt->execute([$value, $id]);
            $updated += $stmt->rowCount();
        } else {
            // Insert new
            $stmt = $pdo->prepare("INSERT INTO pengaturan (`key`, `value`) VALUES (?, ?) ON DUPLICATE KEY UPDATE `value` = VALUES(`value`)");
            $stmt->execute([$key, $value]);
        }
    }
    
    $_SESSION['success'] = " Pengaturan berhasil disimpan!";
    
} catch (PDOException $e) {
    $_SESSION['error'] = " <i class='fa-solid fa-exclamation-triangle'></i> Gagal menyimpan: " . $e->getMessage();
}

header('Location: index.php');
exit();
?>