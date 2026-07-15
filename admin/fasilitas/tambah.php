<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$current_admin_page = 'fasilitas';
$page_title = 'Tambah Fasilitas';
$base_path = '../../';

require_once '../includes/header-admin.php';

$errors = [];
$nama = $deskripsi = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $gambar = '';
    
    if (empty($nama)) $errors['nama'] = 'Nama fasilitas wajib diisi';
    
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $result = uploadFile($_FILES['gambar'], '../../uploads/fasilitas');
        if ($result['success']) $gambar = $result['filename'];
        else $errors['gambar'] = $result['error'];
    }
    
    if (empty($errors)) {
        $pdo = getConnection();
        $stmt = $pdo->prepare("INSERT INTO fasilitas (nama, deskripsi, gambar) VALUES (?, ?, ?)");
        $stmt->execute([$nama, $deskripsi, $gambar]);
        $_SESSION['success'] = 'Fasilitas berhasil ditambahkan!';
        header('Location: index.php');
        exit();
    }
}

require_once '../includes/sidebar.php';
?>

<main class="main-content">
    
    <div class="top-bar">
        <div>
            <h1><i class="fa-solid fa-plus-circle me-2" style="color: #7c3aed;"></i>Tambah Fasilitas</h1>
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
                        <i class="fa-solid fa-building" style="color: #7c3aed;"></i>
                        <span>Form Fasilitas</span>
                    </div>
                </div>
                <div class="admin-card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label-admin">Nama Fasilitas <span class="required">*</span></label>
                                <input type="text" name="nama" class="form-control-admin <?php echo isset($errors['nama']) ? 'is-invalid' : ''; ?>" 
                                       value="<?php echo h($nama); ?>" placeholder="Contoh: Perpustakaan, Laboratorium Komputer" required>
                                <?php if (isset($errors['nama'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['nama']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label-admin">Deskripsi Fasilitas</label>
                                <textarea name="deskripsi" class="form-control-admin" rows="4" placeholder="Deskripsikan fasilitas ini..."><?php echo h($deskripsi); ?></textarea>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label-admin">Gambar (Opsional)</label>
                                <input type="file" name="gambar" class="form-control-admin" accept="image/*" 
                                       onchange="previewImage(this, 'previewGambar')">
                                <small style="color: var(--gray-500);">Format: JPG, PNG | Maks: 2MB</small>
                            </div>
                            
                            <div class="col-12">
                                <img id="previewGambar" src="#" alt="Preview" style="display: none; max-height: 200px; border-radius: 10px; margin-top: 10px;">
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary-admin">
                                <i class="fa-solid fa-save me-1"></i> Simpan Fasilitas
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
                        <i class="fa-solid fa-list-check" style="color: #7c3aed;"></i>
                        <span>Contoh Fasilitas</span>
                    </div>
                </div>
                <div class="admin-card-body">
                    <ul style="list-style: none; padding: 0; display: flex; flex-direction: column; gap: 8px;">
                        <li style="display: flex; gap: 10px; font-size: 0.9rem;">
                            <span><i class="fa-solid fa-chalkboard"></i></span> Ruang Kelas
                        </li>
                        <li style="display: flex; gap: 10px; font-size: 0.9rem;">
                            <span><i class="fa-solid fa-laptop"></i></span> Laboratorium Komputer
                        </li>
                        <li style="display: flex; gap: 10px; font-size: 0.9rem;">
                            <span><i class="fa-solid fa-book"></i></span> Perpustakaan
                        </li>
                        <li style="display: flex; gap: 10px; font-size: 0.9rem;">
                            <span><i class="fa-solid fa-mosque"></i></span> Musholla
                        </li>
                        <li style="display: flex; gap: 10px; font-size: 0.9rem;">
                            <span><i class="fa-solid fa-hospital"></i></span> UKS
                        </li>
                        <li style="display: flex; gap: 10px; font-size: 0.9rem;">
                            <span><i class="fa-solid fa-futbol"></i></span> Lapangan Olahraga
                        </li>
                        <li style="display: flex; gap: 10px; font-size: 0.9rem;">
                            <span><i class="fa-solid fa-utensils"></i></span> Kantin
                        </li>
                        <li style="display: flex; gap: 10px; font-size: 0.9rem;">
                            <span><i class="fa-solid fa-restroom"></i></span> Toilet
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
</main>

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.style.display = 'none';
    }
}
</script>

<?php require_once '../includes/footer-admin.php'; ?>