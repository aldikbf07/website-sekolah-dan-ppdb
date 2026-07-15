<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$current_admin_page = 'galeri';
$page_title = 'Upload Foto';
$base_path = '../../';

require_once '../includes/header-admin.php';

$errors = [];
$deskripsi = $kategori = $tanggal = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $kategori = $_POST['kategori'] ?? 'Kegiatan';
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $gambar = '';
    
    if (!isset($_FILES['gambar']) || $_FILES['gambar']['error'] === UPLOAD_ERR_NO_FILE) {
        $errors['gambar'] = 'Foto wajib diupload';
    } else {
        $result = uploadFile($_FILES['gambar'], '../../uploads/galeri');
        if ($result['success']) $gambar = $result['filename'];
        else $errors['gambar'] = $result['error'];
    }
    
    if (empty($errors)) {
        $pdo = getConnection();
        $stmt = $pdo->prepare("INSERT INTO galeri (gambar, deskripsi, kategori, tanggal) VALUES (?, ?, ?, ?)");
        $stmt->execute([$gambar, $deskripsi, $kategori, $tanggal]);
        $_SESSION['success'] = 'Foto berhasil diupload!';
        header('Location: index.php');
        exit();
    }
}

require_once '../includes/sidebar.php';
?>

<main class="main-content">
    
    <div class="top-bar">
        <div>
            <h1><i class="fa-solid fa-cloud-arrow-up me-2" style="color: #059669;"></i>Upload Foto</h1>
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
                        <i class="fa-solid fa-image" style="color: #059669;"></i>
                        <span>Form Upload</span>
                    </div>
                </div>
                <div class="admin-card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label-admin">Pilih Foto <span class="required">*</span></label>
                                <div class="upload-area" onclick="document.getElementById('gambar').click()" id="uploadArea">
                                    <i class="fa-solid fa-cloud-arrow-up" style="font-size: 2.5rem; color: #94a3b8;"></i>
                                    <p style="font-weight: 600; margin: 8px 0 4px;">Klik untuk memilih foto</p>
                                    <small style="color: #94a3b8;">JPG, PNG, atau GIF (Maks: 2MB)</small>
                                </div>
                                <input type="file" id="gambar" name="gambar" class="form-control-admin <?php echo isset($errors['gambar']) ? 'is-invalid' : ''; ?>" 
                                       accept="image/*" required onchange="previewImage(this, 'previewFoto'); updateUploadArea();" style="margin-top: 10px;">
                                <?php if (isset($errors['gambar'])): ?>
                                <div class="invalid-feedback"><?php echo $errors['gambar']; ?></div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="col-12">
                                <img id="previewFoto" src="#" alt="Preview" style="display: none; max-height: 300px; border-radius: 10px; width: 100%; object-fit: contain;">
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label-admin">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control-admin" rows="3" placeholder="Deskripsikan foto ini..."><?php echo h($deskripsi); ?></textarea>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label-admin">Kategori</label>
                                <select name="kategori" class="form-control-admin">
                                    <option value="Kegiatan">Kegiatan</option>
                                    <option value="Pembelajaran" <?php echo $kategori == 'Pembelajaran' ? 'selected' : ''; ?>>Pembelajaran</option>
                                    <option value="Upacara" <?php echo $kategori == 'Upacara' ? 'selected' : ''; ?>>Upacara</option>
                                    <option value="Keagamaan" <?php echo $kategori == 'Keagamaan' ? 'selected' : ''; ?>>Keagamaan</option>
                                    <option value="Olahraga" <?php echo $kategori == 'Olahraga' ? 'selected' : ''; ?>>Olahraga</option>
                                    <option value="Seni" <?php echo $kategori == 'Seni' ? 'selected' : ''; ?>>Seni</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label-admin">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control-admin" value="<?php echo h($tanggal ?: date('Y-m-d')); ?>">
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary-admin">
                                <i class="fa-solid fa-upload me-1"></i> Upload Foto
                            </button>
                            <a href="index.php" class="btn btn-secondary-admin">Batal</a>
                        </div>
                    </form>
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

function updateUploadArea() {
    const input = document.getElementById('gambar');
    const area = document.getElementById('uploadArea');
    if (input.files && input.files[0]) {
        area.style.borderColor = '#059669';
        area.style.background = '#ecfdf5';
    }
}
</script>

<?php require_once '../includes/footer-admin.php'; ?>