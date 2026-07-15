<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$current_admin_page = 'guru';
$page_title = 'Edit Guru';
$base_path = '../../';

require_once '../includes/header-admin.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$errors = [];

if (!$id) { header('Location: index.php'); exit(); }

try {
    $pdo = getConnection();
    
    $stmt = $pdo->prepare("SELECT * FROM guru WHERE id = ?");
    $stmt->execute([$id]);
    $guru = $stmt->fetch();
    if (!$guru) { $_SESSION['error'] = 'Guru tidak ditemukan'; header('Location: index.php'); exit(); }
    
    // Ambil jabatan yang sudah dipilih
    $stmt = $pdo->prepare("SELECT jabatan_id FROM guru_jabatan WHERE guru_id = ?");
    $stmt->execute([$id]);
    $selected_jabatan = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    // Ambil daftar jabatan
    $stmt = $pdo->query("SELECT * FROM jabatan ORDER BY id ASC");
    $jabatan_list = $stmt->fetchAll();
    
    $nama = $guru['nama'];
    $nip = $guru['nip'] ?? '';
    $mapel = $guru['mapel'] ?? '';
    $foto_lama = $guru['foto'];
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nama = trim($_POST['nama'] ?? '');
        $nip = trim($_POST['nip'] ?? '');
        $mapel = trim($_POST['mapel'] ?? '');
        $new_jabatan = $_POST['jabatan'] ?? [];
        $foto = $foto_lama;
        
        // NIP tidak wajib
        $nip = !empty($nip) ? $nip : null;
        
        if (empty($nama)) $errors['nama'] = 'Nama wajib diisi';
        
        // Validasi NIP duplicate hanya jika diisi
        if (!empty($nip)) {
            $stmt = $pdo->prepare("SELECT id FROM guru WHERE nip = ? AND id != ?");
            $stmt->execute([$nip, $id]);
            if ($stmt->fetch()) $errors['nip'] = 'NIP sudah digunakan guru lain';
        }
        
        // Upload foto baru
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            $result = uploadFile($_FILES['foto'], '../../uploads/guru');
            if ($result['success']) {
                if ($foto_lama && file_exists('../../uploads/guru/' . $foto_lama)) unlink('../../uploads/guru/' . $foto_lama);
                $foto = $result['filename'];
            } else $errors['foto'] = $result['error'];
        }
        
        if (isset($_POST['hapus_foto']) && $_POST['hapus_foto'] == '1') {
            if ($foto_lama && file_exists('../../uploads/guru/' . $foto_lama)) unlink('../../uploads/guru/' . $foto_lama);
            $foto = '';
        }
        
        if (empty($errors)) {
            $pdo->beginTransaction();
            
            // Update guru
            $stmt = $pdo->prepare("UPDATE guru SET nama=?, nip=?, mapel=?, foto=? WHERE id=?");
            $stmt->execute([$nama, $nip, $mapel, $foto, $id]);
            
            // Hapus semua jabatan lama
            $stmt = $pdo->prepare("DELETE FROM guru_jabatan WHERE guru_id = ?");
            $stmt->execute([$id]);
            
            // Insert jabatan baru
            if (!empty($new_jabatan)) {
                $stmt = $pdo->prepare("INSERT INTO guru_jabatan (guru_id, jabatan_id) VALUES (?, ?)");
                foreach ($new_jabatan as $jabatan_id) {
                    $stmt->execute([$id, $jabatan_id]);
                }
            }
            
            $pdo->commit();
            $_SESSION['success'] = 'Data guru berhasil diperbarui!';
            header('Location: index.php');
            exit();
        }
    }
} catch (PDOException $e) {
    if (isset($pdo)) $pdo->rollBack();
    $_SESSION['error'] = 'Terjadi kesalahan';
    header('Location: index.php');
    exit();
}

require_once '../includes/sidebar.php';
?>

<main class="main-content">
    
    <div class="top-bar">
        <div>
            <h1><i class="fa-solid fa-user-pen me-2" style="color: #2563eb;"></i>Edit Guru</h1>
            <p style="color:var(--gray-500);font-size:0.9rem;"><?php echo h($guru['nama']); ?></p>
        </div>
        <a href="index.php" class="btn-top-bar" style="background: var(--gray-500);"><i class="fa-solid fa-arrow-left me-1"></i> Kembali</a>
    </div>
    
    <?php if (!empty($errors)): ?>
    <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i><div><strong>Mohon perbaiki:</strong><ul style="margin:4px 0 0 16px;"><?php foreach($errors as $e): ?><li><?php echo $e; ?></li><?php endforeach; ?></ul></div></div>
    <?php endif; ?>
    
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="admin-card">
                <div class="admin-card-header"><div><i class="fa-solid fa-pen-to-square" style="color:#2563eb;"></i><span>Form Edit Guru</span></div></div>
                <div class="admin-card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label-admin">Nama <span class="required">*</span></label>
                                <input type="text" name="nama" class="form-control-admin" value="<?php echo h($nama); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-admin">
                                    NIP 
                                    <small style="color: var(--gray-500); font-weight: 400;">(opsional, kosongkan jika belum ada)</small>
                                </label>
                                <input type="text" name="nip" class="form-control-admin" value="<?php echo h($nip); ?>" placeholder="Masukkan NIP jika sudah ada">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-admin">Mata Pelajaran</label>
                                <input type="text" name="mapel" class="form-control-admin" value="<?php echo h($mapel); ?>">
                            </div>
                            
                            <!-- Jabatan -->
                            <div class="col-12">
                                <label class="form-label-admin">Jabatan <small style="color:var(--gray-500);">(bisa pilih lebih dari satu)</small></label>
                                <div class="jabatan-grid">
                                    <?php foreach ($jabatan_list as $jab): ?>
                                    <label class="jabatan-checkbox">
                                        <input type="checkbox" name="jabatan[]" value="<?php echo $jab['id']; ?>" 
                                               <?php echo in_array($jab['id'], $selected_jabatan) ? 'checked' : ''; ?>>
                                        <span class="jabatan-label"><?php echo h($jab['nama_jabatan']); ?></span>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                            
                            <div class="col-12">
                                <label class="form-label-admin">Ganti Foto</label>
                                <input type="file" name="foto" class="form-control-admin" accept="image/*" onchange="previewImage(this,'previewFoto')">
                                <img id="previewFoto" src="#" style="display:none;max-height:150px;border-radius:10px;margin-top:10px;">
                            </div>
                            
                            <?php if ($foto_lama): ?>
                            <div class="col-12">
                                <div style="display:flex;align-items:center;gap:16px;">
                                    <img src="../../uploads/guru/<?php echo h($foto_lama); ?>" style="width:80px;height:80px;border-radius:50%;object-fit:cover;">
                                    <label style="cursor:pointer;color:#dc2626;font-weight:600;">
                                        <input type="checkbox" name="hapus_foto" value="1"> Hapus foto
                                    </label>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary-admin"><i class="fa-solid fa-save me-1"></i> Perbarui</button>
                            <a href="index.php" class="btn btn-secondary-admin">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
</main>

<script>
function previewImage(input,id){const p=document.getElementById(id);if(input.files&&input.files[0]){const r=new FileReader();r.onload=function(e){p.src=e.target.result;p.style.display='block';};r.readAsDataURL(input.files[0]);}}
</script>

<style>
.jabatan-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:8px;margin-top:6px;}
.jabatan-checkbox{display:flex;align-items:center;gap:10px;padding:10px 14px;border:1px solid #e2e8f0;border-radius:10px;cursor:pointer;transition:all 0.2s ease;background:#f8fafc;}
.jabatan-checkbox:hover{background:#eff6ff;border-color:#2563eb;}
.jabatan-checkbox input[type="checkbox"]{width:18px;height:18px;accent-color:#2563eb;cursor:pointer;flex-shrink:0;}
.jabatan-checkbox:has(input:checked){background:#eff6ff;border-color:#2563eb;font-weight:600;}
.jabatan-label{font-size:0.88rem;font-weight:500;color:#334155;user-select:none;}
</style>

<?php require_once '../includes/footer-admin.php'; ?>