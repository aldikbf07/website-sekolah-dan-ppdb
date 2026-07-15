<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$current_admin_page = 'berita';
$page_title = 'Edit Berita';
$base_path = '../../';

require_once '../includes/header-admin.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];

if (!$id) { header('Location: index.php'); exit(); }

try {
    $pdo = getConnection();
    
    // Ambil data berita
    $stmt = $pdo->prepare("SELECT * FROM berita WHERE id = ?");
    $stmt->execute([$id]);
    $berita = $stmt->fetch();
    
    if (!$berita) { $_SESSION['error'] = 'Berita tidak ditemukan'; header('Location: index.php'); exit(); }
    
    // Ambil gambar tambahan
    $stmt = $pdo->prepare("SELECT * FROM berita_gambar WHERE berita_id = ? ORDER BY urutan ASC");
    $stmt->execute([$id]);
    $gambar_lainnya = $stmt->fetchAll();
    
    // Ambil video
    $stmt = $pdo->prepare("SELECT * FROM berita_video WHERE berita_id = ? ORDER BY urutan ASC");
    $stmt->execute([$id]);
    $video_list = $stmt->fetchAll();
    
    $judul = $berita['judul'];
    $isi = $berita['isi'];
    $tanggal = $berita['tanggal'];
    $kategori = $berita['kategori'] ?? 'Umum';
    $status = $berita['status'];
    $gambar_utama = $berita['gambar'];
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $judul = trim($_POST['judul'] ?? '');
        $isi = trim($_POST['isi'] ?? '');
        $tanggal = $_POST['tanggal'] ?? date('Y-m-d');
        $kategori = $_POST['kategori'] ?? 'Umum';
        $status = $_POST['status'] ?? 'draft';
        
        if (empty($judul)) $errors['judul'] = 'Judul wajib diisi';
        if (empty($isi)) $errors['isi'] = 'Isi wajib diisi';
        
        if (empty($errors)) {
            $pdo->beginTransaction();
            
            // Upload gambar utama baru
            if (isset($_FILES['gambar_utama']) && $_FILES['gambar_utama']['error'] === UPLOAD_ERR_OK) {
                $result = uploadFile($_FILES['gambar_utama'], '../../uploads/berita');
                if ($result['success']) {
                    // Hapus gambar lama
                    if ($gambar_utama && file_exists('../../uploads/berita/' . $gambar_utama)) {
                        unlink('../../uploads/berita/' . $gambar_utama);
                    }
                    $gambar_utama = $result['filename'];
                }
            }
            
            // Hapus gambar utama jika dicentang
            if (isset($_POST['hapus_gambar_utama']) && $_POST['hapus_gambar_utama'] == '1') {
                if ($gambar_utama && file_exists('../../uploads/berita/' . $gambar_utama)) {
                    unlink('../../uploads/berita/' . $gambar_utama);
                }
                $gambar_utama = '';
            }
            
            // Update berita
            $stmt = $pdo->prepare("UPDATE berita SET judul=?, isi=?, gambar=?, tanggal=?, kategori=?, status=? WHERE id=?");
            $stmt->execute([$judul, $isi, $gambar_utama, $tanggal, $kategori, $status, $id]);
            
            // Upload gambar tambahan baru
            if (isset($_FILES['gambar_lainnya']) && !empty($_FILES['gambar_lainnya']['name'][0])) {
                $files = $_FILES['gambar_lainnya'];
                $upload_dir = '../../uploads/berita/';
                
                for ($i = 0; $i < count($files['name']); $i++) {
                    if ($files['error'][$i] === UPLOAD_ERR_OK && !empty($files['name'][$i])) {
                        $file = [
                            'name' => $files['name'][$i],
                            'type' => $files['type'][$i],
                            'tmp_name' => $files['tmp_name'][$i],
                            'error' => $files['error'][$i],
                            'size' => $files['size'][$i],
                        ];
                        
                        $result = uploadFile($file, $upload_dir);
                        if ($result['success']) {
                            $caption = $_POST['caption_gambar_baru'][$i] ?? '';
                            $stmt = $pdo->prepare("INSERT INTO berita_gambar (berita_id, gambar, caption, urutan) VALUES (?, ?, ?, ?)");
                            $stmt->execute([$id, $result['filename'], $caption, $i + count($gambar_lainnya)]);
                        }
                    }
                }
            }
            
            // Update caption gambar existing
            if (isset($_POST['caption_gambar_existing'])) {
                foreach ($_POST['caption_gambar_existing'] as $gbr_id => $caption) {
                    $stmt = $pdo->prepare("UPDATE berita_gambar SET caption = ? WHERE id = ? AND berita_id = ?");
                    $stmt->execute([$caption, $gbr_id, $id]);
                }
            }
            
            // Hapus gambar yang dicentang
            if (isset($_POST['hapus_gambar'])) {
                foreach ($_POST['hapus_gambar'] as $gbr_id => $val) {
                    $stmt = $pdo->prepare("SELECT gambar FROM berita_gambar WHERE id = ? AND berita_id = ?");
                    $stmt->execute([$gbr_id, $id]);
                    $gbr = $stmt->fetch();
                    if ($gbr && file_exists('../../uploads/berita/' . $gbr['gambar'])) {
                        unlink('../../uploads/berita/' . $gbr['gambar']);
                    }
                    $stmt = $pdo->prepare("DELETE FROM berita_gambar WHERE id = ? AND berita_id = ?");
                    $stmt->execute([$gbr_id, $id]);
                }
            }
            
            // Simpan video baru
            if (isset($_POST['video_url']) && !empty($_POST['video_url'][0])) {
                $video_urls = $_POST['video_url'];
                $video_platforms = $_POST['video_platform'] ?? [];
                $video_captions = $_POST['video_caption'] ?? [];
                
                for ($i = 0; $i < count($video_urls); $i++) {
                    if (!empty($video_urls[$i])) {
                        $platform = $video_platforms[$i] ?? 'youtube';
                        $caption = $video_captions[$i] ?? '';
                        
                        // Generate embed code
                        $embed_code = '';
                        if ($platform == 'youtube') {
                            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $video_urls[$i], $matches);
                            $video_id = $matches[1] ?? null;
                            if ($video_id) {
                                $embed_code = '<iframe width="100%" height="100%" src="https://www.youtube.com/embed/' . $video_id . '" frameborder="0" allowfullscreen></iframe>';
                            }
                        }
                        
                        $stmt = $pdo->prepare("INSERT INTO berita_video (berita_id, platform, url, embed_code, caption, urutan) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute([$id, $platform, $video_urls[$i], $embed_code, $caption, $i + count($video_list)]);
                    }
                }
            }
            
            // Update caption video existing
            if (isset($_POST['video_caption_existing'])) {
                foreach ($_POST['video_caption_existing'] as $vid_id => $caption) {
                    $stmt = $pdo->prepare("UPDATE berita_video SET caption = ? WHERE id = ? AND berita_id = ?");
                    $stmt->execute([$caption, $vid_id, $id]);
                }
            }
            
            // Hapus video yang dicentang
            if (isset($_POST['hapus_video'])) {
                foreach ($_POST['hapus_video'] as $vid_id => $val) {
                    $stmt = $pdo->prepare("DELETE FROM berita_video WHERE id = ? AND berita_id = ?");
                    $stmt->execute([$vid_id, $id]);
                }
            }
            
            $pdo->commit();
            $_SESSION['success'] = 'Berita berhasil diperbarui!';
            header('Location: index.php');
            exit();
        }
    }
} catch (PDOException $e) {
    if (isset($pdo)) $pdo->rollBack();
    $_SESSION['error'] = 'Terjadi kesalahan: ' . $e->getMessage();
    header('Location: index.php');
    exit();
}

require_once '../includes/sidebar.php';
?>

<main class="main-content">
    
    <div class="top-bar">
        <div>
            <h1><i class="fa-solid fa-pen-to-square me-2" style="color: #c8903e;"></i>Edit Berita</h1>
            <p style="color: var(--gray-500); font-size: 0.9rem;"><?php echo h($berita['judul']); ?></p>
        </div>
        <a href="index.php" class="btn-top-bar" style="background: var(--gray-500);">
            <i class="fa-solid fa-arrow-left me-1"></i> Kembali
        </a>
    </div>
    
    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger">
        <i class="fa-solid fa-circle-exclamation"></i>
        <div><strong>Mohon perbaiki:</strong><ul style="margin:4px 0 0 16px;"><?php foreach($errors as $e): ?><li><?php echo $e; ?></li><?php endforeach; ?></ul></div>
    </div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        
        <!-- Data Utama -->
        <div class="admin-card mb-4">
            <div class="admin-card-header">
                <div><i class="fa-solid fa-pen-to-square" style="color:#c8903e;"></i><span>Data Utama</span></div>
            </div>
            <div class="admin-card-body">
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label-admin">Judul <span class="required">*</span></label>
                        <input type="text" name="judul" class="form-control-admin" value="<?php echo h($judul); ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label-admin">Isi <span class="required">*</span></label>
                        <textarea name="isi" class="form-control-admin" rows="10" required><?php echo h($isi); ?></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-admin">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control-admin" value="<?php echo h($tanggal); ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-admin">Kategori</label>
                        <select name="kategori" class="form-control-admin">
                            <option value="Umum" <?php echo $kategori=='Umum'?'selected':''; ?>>Umum</option>
                            <option value="Akademik" <?php echo $kategori=='Akademik'?'selected':''; ?>>Akademik</option>
                            <option value="Keagamaan" <?php echo $kategori=='Keagamaan'?'selected':''; ?>>Keagamaan</option>
                            <option value="Olahraga" <?php echo $kategori=='Olahraga'?'selected':''; ?>>Olahraga</option>
                            <option value="Seni" <?php echo $kategori=='Seni'?'selected':''; ?>>Seni</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label-admin">Status</label>
                        <select name="status" class="form-control-admin">
                            <option value="draft" <?php echo $status=='draft'?'selected':''; ?>>Draft</option>
                            <option value="published" <?php echo $status=='published'?'selected':''; ?>>Published</option>
                        </select>
                    </div>
                    
                    <!-- Gambar Utama -->
                    <div class="col-12">
                        <label class="form-label-admin">Gambar Utama</label>
                        <?php if ($gambar_utama): ?>
                        <div style="display:flex;align-items:center;gap:16px;margin-bottom:10px;">
                            <img src="../../uploads/berita/<?php echo h($gambar_utama); ?>" style="max-width:300px;max-height:150px;border-radius:10px;object-fit:cover;">
                            <label style="cursor:pointer;color:#dc2626;font-weight:600;">
                                <input type="checkbox" name="hapus_gambar_utama" value="1"> Hapus gambar
                            </label>
                        </div>
                        <?php endif; ?>
                        <input type="file" name="gambar_utama" class="form-control-admin" accept="image/*" onchange="previewImage(this,'previewUtama')">
                        <img id="previewUtama" src="#" style="display:none;max-height:150px;border-radius:10px;margin-top:10px;">
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Multi Gambar -->
        <div class="admin-card mb-4">
            <div class="admin-card-header">
                <div><i class="fa-solid fa-images" style="color:#059669;"></i><span>Galeri Foto (<?php echo count($gambar_lainnya); ?> foto)</span></div>
                <button type="button" class="btn btn-sm" style="background:#059669;color:white;border-radius:8px;" onclick="addGambarInput()">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Gambar
                </button>
            </div>
            <div class="admin-card-body">
                <!-- Existing Images -->
                <?php if (!empty($gambar_lainnya)): ?>
                <div class="mb-4">
                    <label class="form-label-admin">Gambar Saat Ini</label>
                    <div class="row g-3">
                        <?php foreach ($gambar_lainnya as $gbr): ?>
                        <div class="col-md-4">
                            <div style="border:1px solid #e2e8f0;border-radius:10px;overflow:hidden;">
                                <img src="../../uploads/berita/<?php echo h($gbr['gambar']); ?>" style="width:100%;height:140px;object-fit:cover;">
                                <div style="padding:8px 10px;">
                                    <input type="text" name="caption_gambar_existing[<?php echo $gbr['id']; ?>]" class="form-control-admin form-control-sm" value="<?php echo h($gbr['caption'] ?? ''); ?>" placeholder="Caption..." style="font-size:0.8rem;padding:6px 10px;">
                                    <label style="cursor:pointer;color:#dc2626;font-weight:600;font-size:0.8rem;margin-top:6px;display:block;">
                                        <input type="checkbox" name="hapus_gambar[<?php echo $gbr['id']; ?>]" value="1"> Hapus
                                    </label>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- New Images -->
                <div id="gambarContainer">
                    <label class="form-label-admin">Tambah Gambar Baru</label>
                    <div class="gambar-item mb-3">
                        <div class="row g-2">
                            <div class="col-md-6">
                                <input type="file" name="gambar_lainnya[]" class="form-control-admin" accept="image/*">
                            </div>
                            <div class="col-md-5">
                                <input type="text" name="caption_gambar_baru[]" class="form-control-admin" placeholder="Caption...">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-sm btn-danger-custom" onclick="removeGambarItem(this)"><i class="fa-solid fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Video -->
        <div class="admin-card mb-4">
            <div class="admin-card-header">
                <div><i class="fa-solid fa-video" style="color:#dc2626;"></i><span>Video (<?php echo count($video_list); ?> video)</span></div>
                <button type="button" class="btn btn-sm" style="background:#dc2626;color:white;border-radius:8px;" onclick="addVideoInput()">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Video
                </button>
            </div>
            <div class="admin-card-body">
                <!-- Existing Videos -->
                <?php if (!empty($video_list)): ?>
                <div class="mb-4">
                    <label class="form-label-admin">Video Saat Ini</label>
                    <?php foreach ($video_list as $vid): ?>
                    <div class="d-flex align-items-center gap-3 mb-2 p-3" style="border:1px solid #e2e8f0;border-radius:10px;">
                        <div style="flex:1;">
                            <input type="text" name="video_caption_existing[<?php echo $vid['id']; ?>]" class="form-control-admin form-control-sm" value="<?php echo h($vid['caption'] ?? ''); ?>" placeholder="Caption video..." style="font-size:0.8rem;">
                            <small style="color:var(--gray-500);">URL: <?php echo h($vid['url']); ?> | Platform: <?php echo h($vid['platform']); ?></small>
                        </div>
                        <label style="cursor:pointer;color:#dc2626;font-weight:600;white-space:nowrap;">
                            <input type="checkbox" name="hapus_video[<?php echo $vid['id']; ?>]" value="1"> Hapus
                        </label>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
                
                <!-- New Videos -->
                <div id="videoContainer">
                    <label class="form-label-admin">Tambah Video Baru</label>
                    <div class="video-item mb-3">
                        <div class="row g-2">
                            <div class="col-md-3">
                                <select name="video_platform[]" class="form-control-admin">
                                    <option value="youtube">YouTube</option>
                                    <option value="tiktok">TikTok</option>
                                    <option value="instagram">Instagram</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <input type="url" name="video_url[]" class="form-control-admin" placeholder="URL Video...">
                            </div>
                            <div class="col-md-3">
                                <input type="text" name="video_caption[]" class="form-control-admin" placeholder="Caption...">
                            </div>
                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-sm btn-danger-custom" onclick="removeVideoItem(this)"><i class="fa-solid fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-actions">
            <button type="submit" class="btn btn-primary-admin"><i class="fa-solid fa-save me-1"></i> Perbarui Berita</button>
            <a href="index.php" class="btn btn-secondary-admin">Batal</a>
        </div>
        
    </form>
    
</main>

<script>
function previewImage(input,id){const p=document.getElementById(id);if(input.files&&input.files[0]){const r=new FileReader();r.onload=function(e){p.src=e.target.result;p.style.display='block';};r.readAsDataURL(input.files[0]);}}
function addGambarInput(){const c=document.getElementById('gambarContainer');const d=document.createElement('div');d.className='gambar-item mb-3';d.innerHTML='<div class="row g-2"><div class="col-md-6"><input type="file" name="gambar_lainnya[]" class="form-control-admin" accept="image/*"></div><div class="col-md-5"><input type="text" name="caption_gambar_baru[]" class="form-control-admin" placeholder="Caption..."></div><div class="col-md-1 d-flex align-items-end"><button type="button" class="btn btn-sm btn-danger-custom" onclick="removeGambarItem(this)"><i class="fa-solid fa-times"></i></button></div></div>';c.appendChild(d);}
function removeGambarItem(b){const i=b.closest('.gambar-item');if(document.querySelectorAll('.gambar-item').length>1)i.remove();}
function addVideoInput(){const c=document.getElementById('videoContainer');const d=document.createElement('div');d.className='video-item mb-3';d.innerHTML='<div class="row g-2"><div class="col-md-3"><select name="video_platform[]" class="form-control-admin"><option value="youtube">YouTube</option><option value="tiktok">TikTok</option><option value="instagram">Instagram</option></select></div><div class="col-md-5"><input type="url" name="video_url[]" class="form-control-admin" placeholder="URL Video..."></div><div class="col-md-3"><input type="text" name="video_caption[]" class="form-control-admin" placeholder="Caption..."></div><div class="col-md-1 d-flex align-items-end"><button type="button" class="btn btn-sm btn-danger-custom" onclick="removeVideoItem(this)"><i class="fa-solid fa-times"></i></button></div></div>';c.appendChild(d);}
function removeVideoItem(b){const i=b.closest('.video-item');if(document.querySelectorAll('.video-item').length>1)i.remove();}
</script>

<style>
.btn-danger-custom{background:#dc2626;color:white;padding:8px;border-radius:8px;border:none;cursor:pointer;display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;}
.btn-danger-custom:hover{background:#b91c1c;}
</style>

<?php require_once '../includes/footer-admin.php'; ?>