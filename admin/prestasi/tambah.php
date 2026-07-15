<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$current_admin_page = 'prestasi';
$page_title = 'Tambah Prestasi';
$base_path = '../../';

require_once '../includes/header-admin.php';

$errors = [];
$judul = $deskripsi = $tanggal = $kategori = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $kategori = $_POST['kategori'] ?? 'Akademik';
    
    if (empty($judul)) $errors['judul'] = 'Judul prestasi wajib diisi';
    
    if (empty($errors)) {
        $pdo = getConnection();
        $stmt = $pdo->prepare("INSERT INTO prestasi (judul, deskripsi, tanggal, kategori) VALUES (?, ?, ?, ?)");
        $stmt->execute([$judul, $deskripsi, $tanggal, $kategori]);
        $_SESSION['success'] = 'Prestasi berhasil ditambahkan!';
        header('Location: index.php');
        exit();
    }
}

require_once '../includes/sidebar.php';
?>

<main class="main-content">
    
    <div class="top-bar">
        <div>
            <h1><i class="fa-solid fa-plus-circle me-2" style="color: #dc2626;"></i>Tambah Prestasi</h1>
        </div>
        <a href="index.php" class="btn-top-bar" style="background: var(--gray-500);">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
    
    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <i class="fa-solid fa-circle-exclamation"></i>
        <div>
            <strong>Mohon perbaiki kesalahan:</strong>
            <ul style="margin: 4px 0 0 16px;">
                <?php foreach ($errors as $e): ?><li><?php echo $e; ?></li><?php endforeach; ?>
            </ul>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="admin-card">
                <div class="admin-card-header">
                    <div>
                        <i class="fa-solid fa-medal" style="color: #dc2626;"></i>
                        <span>Form Prestasi</span>
                    </div>
                </div>
                <div class="admin-card-body">
                    <form method="POST">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label-admin">Judul Prestasi <span class="required">*</span></label>
                                <input type="text" name="judul" class="form-control-admin <?php echo isset($errors['judul']) ? 'is-invalid' : ''; ?>" 
                                       value="<?php echo h($judul); ?>" placeholder="Contoh: Juara 1 Lomba Matematika Tingkat Kabupaten" required>
                                <?php if (isset($errors['judul'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['judul']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label-admin">Deskripsi Prestasi</label>
                                <textarea name="deskripsi" class="form-control-admin" rows="5" placeholder="Detail prestasi yang diraih..."><?php echo h($deskripsi); ?></textarea>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label-admin">Tanggal Perolehan</label>
                                <input type="date" name="tanggal" class="form-control-admin" value="<?php echo h($tanggal ?: date('Y-m-d')); ?>">
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label-admin">Kategori</label>
                                <select name="kategori" class="form-control-admin">
                                    <option value="Akademik">Akademik</option>
                                    <option value="Non-Akademik" <?php echo $kategori == 'Non-Akademik' ? 'selected' : ''; ?>>Non-Akademik</option>
                                    <option value="Olahraga" <?php echo $kategori == 'Olahraga' ? 'selected' : ''; ?>>Olahraga</option>
                                    <option value="Seni" <?php echo $kategori == 'Seni' ? 'selected' : ''; ?>>Seni</option>
                                    <option value="Keagamaan" <?php echo $kategori == 'Keagamaan' ? 'selected' : ''; ?>>Keagamaan</option>
                                    <option value="Lainnya" <?php echo $kategori == 'Lainnya' ? 'selected' : ''; ?>>Lainnya</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary-admin">
                                <i class="fa-solid fa-save me-1"></i> Simpan Prestasi
                            </button>
                            <a href="index.php" class="btn btn-secondary-admin">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="admin-card">
                <div class="admin-card-header">
                    <div>
                        <i class="fa-solid fa-lightbulb" style="color: #f59e0b;"></i>
                        <span>Tips Menulis Prestasi</span>
                    </div>
                </div>
                <div class="admin-card-body">
                    <ul style="list-style: none; padding: 0; display: flex; flex-direction: column; gap: 10px;">
                        <li style="display: flex; gap: 10px;">
                            <i class="fa-solid fa-check-circle" style="color: #059669; margin-top: 3px;"></i>
                            <span style="font-size: 0.9rem;">Tulis judul yang spesifik</span>
                        </li>
                        <li style="display: flex; gap: 10px;">
                            <i class="fa-solid fa-check-circle" style="color: #059669; margin-top: 3px;"></i>
                            <span style="font-size: 0.9rem;">Sebutkan tingkat (sekolah/kecamatan/kabupaten)</span>
                        </li>
                        <li style="display: flex; gap: 10px;">
                            <i class="fa-solid fa-check-circle" style="color: #059669; margin-top: 3px;"></i>
                            <span style="font-size: 0.9rem;">Tambahkan nama siswa yang berprestasi</span>
                        </li>
                        <li style="display: flex; gap: 10px;">
                            <i class="fa-solid fa-check-circle" style="color: #059669; margin-top: 3px;"></i>
                            <span style="font-size: 0.9rem;">Cantumkan tanggal perolehan</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
</main>

<?php require_once '../includes/footer-admin.php'; ?>