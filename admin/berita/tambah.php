<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$current_admin_page = 'berita';
$page_title = 'Tambah Berita';
$base_path = '../../';

require_once '../includes/header-admin.php';

$errors = [];
$judul = $isi = $tanggal = $kategori = $status = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $judul = trim($_POST['judul'] ?? '');
    $isi = trim($_POST['isi'] ?? '');
    $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
    $kategori = $_POST['kategori'] ?? 'Umum';
    $status = $_POST['status'] ?? 'draft';
    
    if (empty($judul)) $errors['judul'] = 'Judul berita wajib diisi';
    if (empty($isi)) $errors['isi'] = 'Isi berita wajib diisi';
    
    if (empty($errors)) {
        try {
            $pdo = getConnection();
            $pdo->beginTransaction();
            
            // Upload gambar utama
            $gambar_utama = '';
            if (isset($_FILES['gambar_utama']) && $_FILES['gambar_utama']['error'] === UPLOAD_ERR_OK) {
                $result = uploadFile($_FILES['gambar_utama'], '../../uploads/berita');
                if ($result['success']) $gambar_utama = $result['filename'];
            }
            
            // Insert berita
            $stmt = $pdo->prepare("INSERT INTO berita (judul, isi, gambar, tanggal, kategori, status) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$judul, $isi, $gambar_utama, $tanggal, $kategori, $status]);
            $berita_id = $pdo->lastInsertId();
            
            // Upload multi gambar
            if (isset($_FILES['gambar_lainnya']) && !empty($_FILES['gambar_lainnya']['name'][0])) {
                $files = $_FILES['gambar_lainnya'];
                $upload_dir = '../../uploads/berita/';
                
                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_OK) {
                        $file = [
                            'name' => $files['name'][$i],
                            'type' => $files['type'][$i],
                            'tmp_name' => $files['tmp_name'][$i],
                            'error' => $files['error'][$i],
                            'size' => $files['size'][$i],
                        ];
                        
                        $result = uploadFile($file, $upload_dir);
                        if ($result['success']) {
                            $caption = $_POST['caption_gambar'][$i] ?? '';
                            $stmt = $pdo->prepare("INSERT INTO berita_gambar (berita_id, gambar, caption, urutan) VALUES (?, ?, ?, ?)");
                            $stmt->execute([$berita_id, $result['filename'], $caption, $i]);
                        }
                    }
                }
            }
            
            // Simpan video
            if (isset($_POST['video_url']) && !empty($_POST['video_url'][0])) {
                $video_urls = $_POST['video_url'];
                $video_platforms = $_POST['video_platform'] ?? [];
                $video_captions = $_POST['video_caption'] ?? [];
                
                for ($i = 0; $i < count($video_urls); $i++) {
                    if (!empty($video_urls[$i])) {
                        $platform = $video_platforms[$i] ?? 'youtube';
                        $caption = $video_captions[$i] ?? '';
                        
                        // Generate embed code untuk YouTube
                        $embed_code = '';
                        if ($platform == 'youtube') {
                            $video_id = getYoutubeId($video_urls[$i]);
                            if ($video_id) {
                                $embed_code = '<iframe width="100%" height="400" src="https://www.youtube.com/embed/' . $video_id . '" frameborder="0" allowfullscreen></iframe>';
                            }
                        }
                        
                        $stmt = $pdo->prepare("INSERT INTO berita_video (berita_id, platform, url, embed_code, caption, urutan) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$berita_id, $platform, $video_urls[$i], $embed_code, $caption, $i]);
                    }
                }
            }
            
            $pdo->commit();
            $_SESSION['success'] = 'Berita berhasil ditambahkan!';
            header('Location: index.php');
            exit();
            
        } catch (Exception $e) {
            $pdo->rollBack();
            $errors['database'] = 'Gagal menyimpan: ' . $e->getMessage();
        }
    }
}

require_once '../includes/sidebar.php';

// Fungsi untuk ekstrak YouTube ID
function getYoutubeId($url) {
    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $url, $matches);
    return $matches[1] ?? null;
}
?>

<main class="main-content">
    
    <div class="top-bar">
        <div>
            <h1><i class="fa-solid fa-plus-circle me-2" style="color: #c8903e;"></i>Tambah Berita</h1>
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
    
    <form method="POST" enctype="multipart/form-data" id="formBerita">
        
        <!-- Data Utama -->
        <div class="admin-card mb-4">
            <div class="admin-card-header">
                <div>
                    <i class="fa-solid fa-pen-to-square" style="color: #c8903e;"></i>
                    <span>Data Utama Berita</span>
                </div>
            </div>
            <div class="admin-card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label-admin">Judul Berita <span class="required">*</span></label>
                        <input type="text" name="judul" class="form-control-admin" value="<?php echo h($judul); ?>" placeholder="Masukkan judul berita" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label-admin">Isi Berita <span class="required">*</span></label>
                        <textarea name="isi" class="form-control-admin" rows="8" placeholder="Tulis isi berita..." required><?php echo h($isi); ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-admin">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control-admin" value="<?php echo h($tanggal ?: date('Y-m-d')); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-admin">Kategori</label>
                        <select name="kategori" class="form-control-admin">
                            <option value="Umum">Umum</option>
                            <option value="Akademik" <?php echo $kategori == 'Akademik' ? 'selected' : ''; ?>>Akademik</option>
                            <option value="Keagamaan" <?php echo $kategori == 'Keagamaan' ? 'selected' : ''; ?>>Keagamaan</option>
                            <option value="Olahraga" <?php echo $kategori == 'Olahraga' ? 'selected' : ''; ?>>Olahraga</option>
                            <option value="Seni" <?php echo $kategori == 'Seni' ? 'selected' : ''; ?>>Seni</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-admin">Status</label>
                        <select name="status" class="form-control-admin">
                            <option value="draft">Draft</option>
                            <option value="published" <?php echo $status == 'published' ? 'selected' : ''; ?>>Published</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label-admin">Gambar Utama (Featured Image)</label>
                        <input type="file" name="gambar_utama" class="form-control-admin" accept="image/*" onchange="previewImage(this, 'previewUtama')">
                        <img id="previewUtama" src="#" alt="Preview" style="display:none; max-height:200px; border-radius:10px; margin-top:10px;">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Multi Gambar -->
        <div class="admin-card mb-4">
            <div class="admin-card-header">
                <div>
                    <i class="fa-solid fa-images" style="color: #059669;"></i>
                    <span>Gambar Lainnya (Multiple)</span>
                </div>
                <button type="button" class="btn btn-sm" style="background: #059669; color: white; border-radius: 8px;" onclick="addGambarInput()">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Gambar
                </button>
            </div>
            <div class="admin-card-body">
                <div id="gambarContainer">
                    <div class="gambar-item mb-3">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <label class="form-label-admin">Pilih Gambar</label>
                                <input type="file" name="gambar_lainnya[]" class="form-control-admin" accept="image/*">
                            </div>
                            <div class="col-md-5">
                                <label class="form-label-admin">Caption / Keterangan</label>
                                <input type="text" name="caption_gambar[]" class="form-control-admin" placeholder="Deskripsi gambar...">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-sm btn-danger-custom" onclick="removeGambarItem(this)" style="margin-bottom: 0;">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <small style="color: var(--gray-500);">Format: JPG, PNG, GIF | Maks: 2MB per gambar</small>
            </div>
        </div>
        
        <!-- Video -->
        <div class="admin-card mb-4">
            <div class="admin-card-header">
                <div>
                    <i class="fa-solid fa-video" style="color: #dc2626;"></i>
                    <span>Video (YouTube, TikTok, dll)</span>
                </div>
                <button type="button" class="btn btn-sm" style="background: #dc2626; color: white; border-radius: 8px;" onclick="addVideoInput()">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Video
                </button>
            </div>
            <div class="admin-card-body">
                <div id="videoContainer">
                    <div class="video-item mb-3">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <label class="form-label-admin">Platform</label>
                                <select name="video_platform[]" class="form-control-admin">
                                    <option value="youtube">YouTube</option>
                                    <option value="tiktok">TikTok</option>
                                    <option value="instagram">Instagram</option>
                                    <option value="other">Lainnya</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label-admin">URL Video</label>
                                <input type="url" name="video_url[]" class="form-control-admin" placeholder="https://youtube.com/watch?v=...">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label-admin">Caption</label>
                                <input type="text" name="video_caption[]" class="form-control-admin" placeholder="Judul video...">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-sm btn-danger-custom" onclick="removeVideoItem(this)" style="margin-bottom: 0;">
                                    <i class="fa-solid fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <small style="color: var(--gray-500);">Masukkan URL video dari YouTube, TikTok, atau Instagram</small>
            </div>
        </div>
        
        <!-- Tombol Simpan -->
        <div class="form-actions">
            <button type="submit" class="btn btn-primary-admin">
                <i class="fa-solid fa-save me-1"></i> Simpan Berita
            </button>
            <a href="index.php" class="btn btn-secondary-admin">Batal</a>
        </div>
        
    </form>
    
</main>

<script>
// Preview gambar utama
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(input.files[0]);
    }
}

// Tambah input gambar
function addGambarInput() {
    const container = document.getElementById('gambarContainer');
    const newItem = document.createElement('div');
    newItem.className = 'gambar-item mb-3';
    newItem.innerHTML = `
        <div class="row g-2">
            <div class="col-md-6">
                <label class="form-label-admin">Pilih Gambar</label>
                <input type="file" name="gambar_lainnya[]" class="form-control-admin" accept="image/*">
            </div>
            <div class="col-md-5">
                <label class="form-label-admin">Caption / Keterangan</label>
                <input type="text" name="caption_gambar[]" class="form-control-admin" placeholder="Deskripsi gambar...">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-sm btn-danger-custom" onclick="removeGambarItem(this)">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(newItem);
}

// Hapus input gambar
function removeGambarItem(btn) {
    const item = btn.closest('.gambar-item');
    if (document.querySelectorAll('.gambar-item').length > 1) {
        item.remove();
    }
}

// Tambah input video
function addVideoInput() {
    const container = document.getElementById('videoContainer');
    const newItem = document.createElement('div');
    newItem.className = 'video-item mb-3';
    newItem.innerHTML = `
        <div class="row g-2">
            <div class="col-md-3">
                <label class="form-label-admin">Platform</label>
                <select name="video_platform[]" class="form-control-admin">
                    <option value="youtube">YouTube</option>
                    <option value="tiktok">TikTok</option>
                    <option value="instagram">Instagram</option>
                    <option value="other">Lainnya</option>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label-admin">URL Video</label>
                <input type="url" name="video_url[]" class="form-control-admin" placeholder="https://youtube.com/watch?v=...">
            </div>
            <div class="col-md-3">
                <label class="form-label-admin">Caption</label>
                <input type="text" name="video_caption[]" class="form-control-admin" placeholder="Judul video...">
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="button" class="btn btn-sm btn-danger-custom" onclick="removeVideoItem(this)">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
        </div>
    `;
    container.appendChild(newItem);
}

// Hapus input video
function removeVideoItem(btn) {
    const item = btn.closest('.video-item');
    if (document.querySelectorAll('.video-item').length > 1) {
        item.remove();
    }
}
</script>

<style>
.btn-danger-custom {
    background: #dc2626; color: white; padding: 8px 18px; border-radius: 8px; 
    text-decoration: none; font-weight: 600; border: none; cursor: pointer;
    display: inline-flex; align-items: center; justify-content: center;
    width: 38px; height: 38px;
}
.btn-danger-custom:hover { background: #b91c1c; }
</style>

<?php require_once '../includes/footer-admin.php'; ?>