<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$current_admin_page = 'guru';
$page_title = 'Tambah Guru';
$base_path = '../../';

require_once '../includes/header-admin.php';

$errors = [];
$nama = $nip = $mapel = '';
$selected_jabatan = [];

$pdo = getConnection();
$stmt = $pdo->query("SELECT * FROM jabatan ORDER BY id ASC");
$jabatan_list = $stmt->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $nip = trim($_POST['nip'] ?? '');
    $mapel = trim($_POST['mapel'] ?? '');
    $selected_jabatan = $_POST['jabatan'] ?? [];
    $foto = '';
    
    // NIP tidak wajib
    $nip = !empty($nip) ? $nip : null;
    
    if (empty($nama)) $errors['nama'] = 'Nama wajib diisi';
    
    // Validasi NIP hanya jika diisi
    if (!empty($nip)) {
        $stmt_check = $pdo->prepare("SELECT id FROM guru WHERE nip = ?");
        $stmt_check->execute([$nip]);
        if ($stmt_check->fetch()) {
            $errors['nip'] = 'NIP sudah terdaftar';
        }
    }
    
    // Upload foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $result = uploadFile($_FILES['foto'], '../../uploads/guru');
        if ($result['success']) $foto = $result['filename'];
        else $errors['foto'] = $result['error'];
    }
    
    if (empty($errors)) {
        try {
            $pdo->beginTransaction();
            
            // Insert guru dengan NIP nullable
            $stmt = $pdo->prepare("INSERT INTO guru (nama, nip, mapel, foto) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nama, $nip, $mapel, $foto]);
            $guru_id = $pdo->lastInsertId();
            
            // Insert jabatan
            if (!empty($selected_jabatan)) {
                $stmt = $pdo->prepare("INSERT INTO guru_jabatan (guru_id, jabatan_id) VALUES (?, ?)");
                foreach ($selected_jabatan as $jabatan_id) {
                    $stmt->execute([$guru_id, $jabatan_id]);
                }
            }
            
            $pdo->commit();
            $_SESSION['success'] = 'Guru berhasil ditambahkan!';
            header('Location: index.php');
            exit();
            
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors['database'] = 'Gagal menyimpan data';
        }
    }
}

require_once '../includes/sidebar.php';
?>

<main class="main-content">
    
    <div class="top-bar">
        <div>
            <h1><i class="fa-solid fa-user-plus me-2" style="color: #2563eb;"></i>Tambah Guru</h1>
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
    
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="admin-card">
                <div class="admin-card-header">
                    <div><i class="fa-solid fa-user-pen" style="color: #2563eb;"></i><span>Form Data Guru</span></div>
                </div>
                <div class="admin-card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row g-3">
                            <!-- Nama -->
                            <div class="col-md-6">
                                <label class="form-label-admin">Nama Lengkap <span class="required">*</span></label>
                                <input type="text" name="nama" class="form-control-admin <?php echo isset($errors['nama'])?'is-invalid':''; ?>" 
                                       value="<?php echo h($nama); ?>" placeholder="Masukkan nama lengkap dengan gelar" required>
                                <?php if(isset($errors['nama'])): ?><div class="invalid-feedback"><?php echo $errors['nama']; ?></div><?php endif; ?>
                            </div>
                            
                            <!-- NIP (Optional) -->
                            <div class="col-md-6">
                                <label class="form-label-admin">
                                    NIP 
                                    <small style="color: var(--gray-500); font-weight: 400;">(opsional)</small>
                                </label>
                                <input type="text" name="nip" class="form-control-admin <?php echo isset($errors['nip'])?'is-invalid':''; ?>" 
                                       value="<?php echo h($nip); ?>" placeholder="Masukkan NIP jika sudah ada">
                                <?php if(isset($errors['nip'])): ?><div class="invalid-feedback"><?php echo $errors['nip']; ?></div><?php endif; ?>
                                <small style="color: var(--gray-500);">
                                    <i class="fa-solid fa-circle-info me-1"></i> Kosongkan jika belum memiliki NIP
                                </small>
                            </div>
                            
                            <!-- Mapel -->
                            <div class="col-md-6">
                                <label class="form-label-admin">Mata Pelajaran</label>
                                <input type="text" name="mapel" class="form-control-admin" value="<?php echo h($mapel); ?>" 
                                       placeholder="Contoh: Matematika, PJOK, PAI">
                            </div>
                            
                            <!-- Jabatan Ganda -->
                            <div class="col-12">
                                <label class="form-label-admin">
                                    Jabatan 
                                    <small style="color: var(--gray-500); font-weight: 400;">(bisa pilih lebih dari satu)</small>
                                </label>
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
                            
                            <!-- Foto -->
                            <div class="col-12">
                                <label class="form-label-admin">Foto (Opsional)</label>
                                <input type="file" name="foto" class="form-control-admin" accept="image/*" onchange="previewImage(this,'previewFoto')">
                                <img id="previewFoto" src="#" style="display:none;max-height:150px;border-radius:10px;margin-top:10px;">
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn btn-primary-admin"><i class="fa-solid fa-save me-1"></i> Simpan</button>
                            <a href="index.php" class="btn btn-secondary-admin">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="admin-card">
                <div class="admin-card-header"><div><i class="fa-solid fa-circle-info" style="color:#2563eb;"></i><span>Panduan</span></div></div>
                <div class="admin-card-body">
                    <ul style="list-style:none;padding:0;display:flex;flex-direction:column;gap:10px;">
                        <li style="display:flex;gap:10px;"><i class="fa-solid fa-check-circle" style="color:#059669;margin-top:3px;"></i><span style="font-size:0.9rem;">Nama wajib diisi lengkap dengan gelar</span></li>
                        <li style="display:flex;gap:10px;"><i class="fa-solid fa-check-circle" style="color:#059669;margin-top:3px;"></i><span style="font-size:0.9rem;">NIP <strong>boleh dikosongkan</strong> jika belum ada</span></li>
                        <li style="display:flex;gap:10px;"><i class="fa-solid fa-check-circle" style="color:#059669;margin-top:3px;"></i><span style="font-size:0.9rem;">Bisa pilih <strong>lebih dari satu jabatan</strong></span></li>
                        <li style="display:flex;gap:10px;"><i class="fa-solid fa-check-circle" style="color:#059669;margin-top:3px;"></i><span style="font-size:0.9rem;">Mapel diisi jika mengajar mapel khusus</span></li>
                        <li style="display:flex;gap:10px;"><i class="fa-solid fa-check-circle" style="color:#059669;margin-top:3px;"></i><span style="font-size:0.9rem;">Upload foto dengan format JPG/PNG</span></li>
                    </ul>
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