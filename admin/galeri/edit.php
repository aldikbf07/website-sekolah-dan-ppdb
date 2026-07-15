<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$current_admin_page = 'galeri';
$page_title = 'Edit Galeri';
$base_path = '../../';

require_once '../includes/header-admin.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: index.php'); exit(); }

$pdo = getConnection();
$stmt = $pdo->prepare("SELECT * FROM galeri WHERE id = ?");
$stmt->execute([$id]);
$galeri = $stmt->fetch();
if (!$galeri) { $_SESSION['error'] = 'Data tidak ditemukan'; header('Location: index.php'); exit(); }

$deskripsi = $galeri['deskripsi'];
$kategori = $galeri['kategori'];
$tanggal = $galeri['tanggal'];
$gambar_lama = $galeri['gambar'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $deskripsi = trim($_POST['deskripsi'] ?? '');
    $kategori = $_POST['kategori'] ?? 'Kegiatan';
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $gambar = $gambar_lama;
    
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === UPLOAD_ERR_OK) {
        $result = uploadFile($_FILES['gambar'], '../../uploads/galeri');
        if ($result['success']) {
            if ($gambar_lama && file_exists('../../uploads/galeri/' . $gambar_lama)) {
                unlink('../../uploads/galeri/' . $gambar_lama);
            }
            $gambar = $result['filename'];
        }
    }
    
    $stmt = $pdo->prepare("UPDATE galeri SET gambar=?, deskripsi=?, kategori=?, tanggal=? WHERE id=?");
    $stmt->execute([$gambar, $deskripsi, $kategori, $tanggal, $id]);
    $_SESSION['success'] = 'Data galeri berhasil diperbarui!';
    header('Location: index.php');
    exit();
}

require_once '../includes/sidebar.php';
?>

<main class="main-content">
    
    <div class="top-bar">
        <div>
            <h1><i class="fa-solid fa-pen-to-square me-2" style="color: #059669;"></i>Edit Galeri</h1>
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
                        <i class="fa-solid fa-pen-to-square" style="color: #059669;"></i>
                        <span>Form Edit</span>
                    </div>
                </div>
                <div class="admin-card-body">
                    <?php if ($gambar_lama): ?>
                    <div class="text-center mb-4">
                        <label class="form-label-admin">Foto Saat Ini</label>
                        <img src="../../uploads/galeri/<?php echo h($gambar_lama); ?>" alt="Foto" style="max-width: 100%; max-height: 300px; border-radius: 12px;">
                    </div>
                    <?php endif; ?>
                    
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label-admin">Ganti Foto (Opsional)</label>
                                <input type="file" name="gambar" class="form-control-admin" accept="image/*" 
                                       onchange="previewImage(this, 'previewFoto')">
                                <img id="previewFoto" src="#" alt="Preview" style="display: none; max-height: 200px; border-radius: 10px; margin-top: 10px;">
                            </div>
                            <div class="col-12">
                                <label class="form-label-admin">Deskripsi</label>
                                <textarea name="deskripsi" class="form-control-admin" rows="3"><?php echo h($deskripsi); ?></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-admin">Kategori</label>
                                <select name="kategori" class="form-control-admin">
                                    <option value="Kegiatan" <?php echo $kategori == 'Kegiatan' ? 'selected' : ''; ?>>Kegiatan</option>
                                    <option value="Pembelajaran" <?php echo $kategori == 'Pembelajaran' ? 'selected' : ''; ?>>Pembelajaran</option>
                                    <option value="Keagamaan" <?php echo $kategori == 'Keagamaan' ? 'selected' : ''; ?>>Keagamaan</option>
                                    <option value="Olahraga" <?php echo $kategori == 'Olahraga' ? 'selected' : ''; ?>>Olahraga</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-admin">Tanggal</label>
                                <input type="date" name="tanggal" class="form-control-admin" value="<?php echo h($tanggal); ?>">
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