<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$current_admin_page = 'pengaturan';
$page_title = 'Pengaturan Website';
$base_path = '../../';

require_once '../includes/header-admin.php';

$pdo = getConnection();

// Ambil pengaturan dari database
$stmt = $pdo->query("SELECT * FROM pengaturan WHERE `key` IN ('visi', 'misi', 'sejarah', 'alamat', 'telepon', 'email') ORDER BY id ASC");
$pengaturan_list = $stmt->fetchAll();

// Default values
$settings = [
    'visi' => ['id' => 0, 'value' => ''],
    'misi' => ['id' => 0, 'value' => ''],
    'sejarah' => ['id' => 0, 'value' => ''],
    'alamat' => ['id' => 0, 'value' => ''],
    'telepon' => ['id' => 0, 'value' => ''],
    'email' => ['id' => 0, 'value' => ''],
];

// Isi dengan data dari database
foreach ($pengaturan_list as $p) {
    if (isset($settings[$p['key']])) {
        $settings[$p['key']] = [
            'id' => $p['id'],
            'value' => $p['value'] ?? ''
        ];
    }
}

require_once '../includes/sidebar.php';
?>

<main class="main-content">
    
    <!-- Top Bar -->
    <div class="top-bar">
        <div>
            <h1><i class="fa-solid fa-sliders me-2" style="color: #64748b;"></i>Pengaturan Website</h1>
            <p style="color: var(--gray-500); font-size: 0.9rem; margin: 4px 0 0;">
                Kelola visi, misi, sejarah, dan kontak sekolah
            </p>
        </div>
    </div>
    
    <!-- Alert Messages -->
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <i class="fa-solid fa-circle-check"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
    </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <i class="fa-solid fa-circle-exclamation"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
    <?php endif; ?>
    
    <form action="simpan.php" method="POST">
        
        <div class="row g-4">
            
            <!-- Kolom Kiri - Visi Misi Sejarah -->
            <div class="col-lg-7">
                <div class="admin-card">
                    <div class="admin-card-header" style="background: linear-gradient(135deg, #fef9f0, #fdf6ed);">
                        <div>
                            <i class="fa-solid fa-bullseye" style="color: #c8903e;"></i>
                            <span>Visi, Misi & Sejarah</span>
                        </div>
                    </div>
                    <div class="admin-card-body">
                        
                        <!-- Visi -->
                        <div class="mb-4">
                            <label class="form-label-admin">
                                <i class="fa-solid fa-eye me-1" style="color: #2563eb;"></i> Visi Sekolah
                            </label>
                            <textarea name="visi" class="form-control-admin" rows="3" 
                                      placeholder="Masukkan visi sekolah..."><?php echo h($settings['visi']['value']); ?></textarea>
                            <input type="hidden" name="ids[visi]" value="<?php echo $settings['visi']['id']; ?>">
                        </div>
                        
                        <!-- Misi -->
                        <div class="mb-4">
                            <label class="form-label-admin">
                                <i class="fa-solid fa-bullseye me-1" style="color: #c8903e;"></i> Misi Sekolah
                            </label>
                            <textarea name="misi" class="form-control-admin" rows="5" 
                                      placeholder="Masukkan misi sekolah (setiap poin pisahkan dengan enter)..."><?php echo h($settings['misi']['value']); ?></textarea>
                            <small style="color: var(--gray-500);">Setiap baris akan menjadi 1 poin misi</small>
                            <input type="hidden" name="ids[misi]" value="<?php echo $settings['misi']['id']; ?>">
                        </div>
                        
                        <!-- Sejarah -->
                        <div class="mb-0">
                            <label class="form-label-admin">
                                <i class="fa-solid fa-clock-rotate-left me-1" style="color: #059669;"></i> Sejarah Sekolah
                            </label>
                            <textarea name="sejarah" class="form-control-admin" rows="6" 
                                      placeholder="Masukkan sejarah berdirinya sekolah..."><?php echo h($settings['sejarah']['value']); ?></textarea>
                            <input type="hidden" name="ids[sejarah]" value="<?php echo $settings['sejarah']['id']; ?>">
                        </div>
                        
                    </div>
                </div>
            </div>
            
            <!-- Kolom Kanan - Kontak & Alamat -->
            <div class="col-lg-5">
                <div class="admin-card">
                    <div class="admin-card-header" style="background: linear-gradient(135deg, #ecfdf5, #f0fdf4);">
                        <div>
                            <i class="fa-solid fa-address-book" style="color: #059669;"></i>
                            <span>Kontak & Alamat</span>
                        </div>
                    </div>
                    <div class="admin-card-body">
                        
                        <!-- Alamat -->
                        <div class="mb-4">
                            <label class="form-label-admin">
                                <i class="fa-solid fa-location-dot me-1" style="color: #dc2626;"></i> Alamat Sekolah
                            </label>
                            <textarea name="alamat" class="form-control-admin" rows="3" 
                                      placeholder="Masukkan alamat lengkap sekolah..."><?php echo h($settings['alamat']['value']); ?></textarea>
                            <input type="hidden" name="ids[alamat]" value="<?php echo $settings['alamat']['id']; ?>">
                        </div>
                        
                        <!-- Telepon -->
                        <div class="mb-4">
                            <label class="form-label-admin">
                                <i class="fa-solid fa-phone me-1" style="color: #059669;"></i> Nomor Telepon
                            </label>
                            <input type="text" name="telepon" class="form-control-admin" 
                                   value="<?php echo h($settings['telepon']['value']); ?>" 
                                   placeholder="0812-3456-7890">
                            <input type="hidden" name="ids[telepon]" value="<?php echo $settings['telepon']['id']; ?>">
                        </div>
                        
                        <!-- Email -->
                        <div class="mb-4">
                            <label class="form-label-admin">
                                <i class="fa-solid fa-envelope me-1" style="color: #c8903e;"></i> Email Sekolah
                            </label>
                            <input type="email" name="email" class="form-control-admin" 
                                   value="<?php echo h($settings['email']['value']); ?>" 
                                   placeholder="email@sekolah.sch.id">
                            <input type="hidden" name="ids[email]" value="<?php echo $settings['email']['id']; ?>">
                        </div>
                        
                        <!-- Tombol Simpan -->
                        <button type="submit" class="btn btn-save-settings w-100 mt-3">
                            <i class="fa-solid fa-save me-2"></i> Simpan Semua Pengaturan
                        </button>
                        
                    </div>
                </div>
            </div>
            
        </div>
        
    </form>
    
</main>

<style>
/* ============ FORM PENGATURAN ============ */
.form-label-admin {
    display: block;
    font-weight: 600;
    font-size: 0.88rem;
    color: #334155;
    margin-bottom: 6px;
}

.form-control-admin {
    width: 100%;
    padding: 12px 16px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    font-family: 'Outfit', sans-serif;
    font-size: 0.9rem;
    color: #1e293b;
    background: #f8fafc;
    transition: all 0.2s ease;
}

.form-control-admin:focus {
    border-color: #2563eb;
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    background: white;
    outline: none;
}

textarea.form-control-admin {
    resize: vertical;
    min-height: 80px;
}

/* Tombol Simpan */
.btn-save-settings {
    background: linear-gradient(135deg, #1a365d, #1e4d8c, #2563eb);
    background-size: 200% 200%;
    animation: gradientShift 4s ease infinite;
    color: white;
    border: none;
    padding: 14px 28px;
    border-radius: 12px;
    font-family: 'Outfit', sans-serif;
    font-weight: 700;
    font-size: 1rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 15px rgba(26, 54, 93, 0.25);
}

.btn-save-settings:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(26, 54, 93, 0.35);
}

/* Alert */
.alert {
    padding: 14px 18px;
    border-radius: 14px;
    margin-bottom: 20px;
    display: flex;
    align-items: flex-start;
    gap: 12px;
    font-weight: 500;
    font-size: 0.9rem;
}

.alert i { font-size: 1.1rem; margin-top: 2px; }

.alert-success { background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0; }
.alert-danger { background: #fef2f2; color: #dc2626; border: 1px solid #fecaca; }

/* Admin Card */
.admin-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    overflow: hidden;
    margin-bottom: 24px;
    box-shadow: var(--shadow-xs);
}

.admin-card-header {
    padding: 14px 20px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.admin-card-header div {
    display: flex;
    align-items: center;
    gap: 10px;
    font-family: 'Raleway', sans-serif;
    font-weight: 700;
    font-size: 0.95rem;
}

.admin-card-body { padding: 20px; }

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

@media (max-width: 767px) {
    .btn-save-settings {
        padding: 12px 20px;
        font-size: 0.95rem;
    }
}
</style>

<?php require_once '../includes/footer-admin.php'; ?>