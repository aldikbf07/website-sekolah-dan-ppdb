<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$current_admin_page = 'prestasi';
$page_title = 'Edit Prestasi';
$base_path = '../../';

require_once '../includes/header-admin.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: index.php'); exit(); }

$pdo = getConnection();
$stmt = $pdo->prepare("SELECT * FROM prestasi WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) { $_SESSION['error'] = 'Data tidak ditemukan'; header('Location: index.php'); exit(); }

$judul = $p['judul'];
$deskripsi = $p['deskripsi'];
$tanggal = $p['tanggal'];
$kategori = $p['kategori'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $kategori = $_POST['kategori'] ?? 'Akademik';
    
    if (!empty($judul)) {
        $stmt = $pdo->prepare("UPDATE prestasi SET judul=?, deskripsi=?, tanggal=?, kategori=? WHERE id=?");
        $stmt->execute([$judul, $deskripsi, $tanggal, $kategori, $id]);
        $_SESSION['success'] = 'Prestasi berhasil diperbarui!';
        header('Location: index.php');
        exit();
    }
}

require_once '../includes/sidebar.php';
?>

<main class="main-content">
    
    <div class="top-bar">
        <div>
            <h1><i class="fa-solid fa-pen-to-square me-2" style="color: #dc2626;"></i>Edit Prestasi</h1>
            <p style="color: var(--gray-500); font-size: 0.9rem;"><?php echo h($p['judul']); ?></p>
        </div>
        <a href="index.php" class="btn-top-bar" style="background: var(--gray-500);">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
    
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="admin-card">
                <div class="admin-card-header">
                    <div>
                        <i class="fa-solid fa-pen-to-square" style="color: #dc2626;"></i>
                        <span>Form Edit Prestasi</span>
                    </div>
                </div>
                <div class="admin-card-body">
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label-admin">Judul <span class="required">*</span></label>
                                <input type="text" name="judul" class="form-control-admin" value="<?php echo h($judul); ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label-admin">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control-admin" rows="5"><?php echo h($deskripsi); ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-admin">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control-admin" value="<?php echo h($tanggal); ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-admin">Kategori</label>
                                <select name="kategori" class="form-control-admin">
                                    <option value="Akademik" <?php echo $kategori == 'Akademik' ? 'selected' : ''; ?>>Akademik</option>
                                    <option value="Non-Akademik" <?php echo $kategori == 'Non-Akademik' ? 'selected' : ''; ?>>Non-Akademik</option>
                                    <option value="Olahraga" <?php echo $kategori == 'Olahraga' ? 'selected' : ''; ?>>Olahraga</option>
                                    <option value="Seni" <?php echo $kategori == 'Seni' ? 'selected' : ''; ?>>Seni</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary-admin">
                                <i class="fa-solid fa-save me-1"></i> Perbarui
                            </button>
                            <a href="index.php" class="btn btn-secondary-admin">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
</main>

<?php require_once '../includes/footer-admin.php'; ?>