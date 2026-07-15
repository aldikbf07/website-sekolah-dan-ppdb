<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$current_admin_page = 'fasilitas';
$page_title = 'Edit Fasilitas';
$base_path = '../../';

require_once '../includes/header-admin.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: index.php'); exit(); }

$pdo = getConnection();
$stmt = $pdo->prepare("SELECT * FROM fasilitas WHERE id = ?");
$stmt->execute([$id]);
$f = $stmt->fetch();
if (!$f) { $_SESSION['error'] = 'Data tidak ditemukan'; header('Location: index.php'); exit(); }

$nama = $f['nama'];
$deskripsi = $f['deskripsi'];
$gambar_lama = $f['gambar'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $gambar = $gambar_lama;
    
    if (empty($nama)) $errors['nama'] = 'Nama wajib diisi';
    
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $result = uploadFile($_FILES['gambar'], '../../uploads/fasilitas');
        if ($result['success']) {
            if ($gambar_lama && file_exists('../../uploads/fasilitas/' . $gambar_lama)) {
                unlink('../../uploads/fasilitas/' . $gambar_lama);
            }
            $gambar = $result['filename'];
        }
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE fasilitas SET nama=?, deskripsi=?, gambar=? WHERE id=?");
        $stmt->execute([$nama, $deskripsi, $gambar, $id]);
        $_SESSION['success'] = 'Fasilitas berhasil diperbarui!';
        header('Location: index.php');
        exit();
    }
}

require_once '../includes/sidebar.php';
?>

<main class="main-content">
    
    <div class="top-bar">
        <div>
            <h1><i class="fa-solid fa-pen-to-square me-2" style="color: #7c3aed;"></i>Edit Fasilitas</h1>
            <p style="color: var(--gray-500); font-size: 0.9rem;"><?php echo h($f['nama']); ?></p>
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
                        <i class="fa-solid fa-pen-to-square" style="color: #7c3aed;"></i>
                        <span>Form Edit Fasilitas</span>
                    </div>
                </div>
                <div class="admin-card-body">
                    <?php if ($gambar_lama): ?>
                    <div class="text-center mb-4">
                        <label class="form-label-admin">Gambar Saat Ini</label>
                        <img src="../../uploads/fasilitas/<?php echo h($gambar_lama); ?>" alt="Gambar" style="max-width: 100%; max-height: 200px; border-radius: 12px;">
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label-admin">Nama Fasilitas <span class="required">*</span></label>
                                <input type="text" name="nama" class="form-control-admin" value="<?php echo h($nama); ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label-admin">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control-admin" rows="4"><?php echo h($deskripsi); ?></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label-admin">Ganti Gambar (Opsional)</label>
                                <input type="file" name="gambar" class="form-control-admin" accept="image/*" 
                                       onchange="previewImage(this, 'previewGambar')">
                                <img id="previewGambar" src="#" alt="Preview" style="display: none; max-height: 200px; border-radius: 10px; margin-top: 10px;">
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

<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    } else { preview.style.display = 'none'; }
}
</script>

<?php require_once '../includes/footer-admin.php'; ?>