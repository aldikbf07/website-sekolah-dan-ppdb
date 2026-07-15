<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';
require_once '../../config/wilayah.php'; // Include fungsi wilayah

$current_admin_page = 'ppdb';
$page_title = 'Detail Pendaftar';
$base_path = '../../';

require_once '../includes/header-admin.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: index.php'); exit(); }

$pdo = getConnection();

// Data pendaftar
$stmt = $pdo->prepare("SELECT * FROM pendaftar WHERE id = ?");
$stmt->execute([$id]);
$p = $stmt->fetch();
if (!$p) { $_SESSION['error'] = 'Data tidak ditemukan'; header('Location: index.php'); exit(); }

// Ambil nama wilayah dari API (dengan cache)
$kecamatan_nama = getWilayahName('district', $p['kecamatan']);
$kabupaten_nama = getWilayahName('regency', $p['kabupaten']);
$provinsi_nama = getWilayahName('province', $p['provinsi']);

// Cek dokumen
$stmt = $pdo->prepare("SELECT * FROM dokumen_penerimaan WHERE pendaftar_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$id]);
$dokumen = $stmt->fetch();

// Navigasi
$stmt = $pdo->prepare("SELECT id, nama_lengkap FROM pendaftar WHERE id < ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$id]);
$prev = $stmt->fetch();

$stmt = $pdo->prepare("SELECT id, nama_lengkap FROM pendaftar WHERE id > ? ORDER BY id ASC LIMIT 1");
$stmt->execute([$id]);
$next = $stmt->fetch();

// Status badge
$status_badge = [
    'pending' => ['class' => 'badge-warning', 'text' => 'Pending', 'color' => '#d97706', 'icon' => 'fa-clock'],
    'diterima' => ['class' => 'badge-success', 'text' => 'Diterima', 'color' => '#059669', 'icon' => 'fa-circle-check'],
    'ditolak' => ['class' => 'badge-danger', 'text' => 'Ditolak', 'color' => '#dc2626', 'icon' => 'fa-circle-xmark'],
];
$sb = $status_badge[$p['status']];

require_once '../includes/sidebar.php';
?>

<main class="main-content">
    
    <!-- Top Bar -->
    <div class="top-bar">
        <div>
            <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 4px;">
                <a href="index.php" style="color: var(--gray-500); text-decoration: none;">
                    <i class="fa-solid fa-arrow-left me-1"></i> Kembali
                </a>
                <span style="color: var(--gray-300);">|</span>
                <h1 style="font-size: 1.2rem; margin: 0;">Detail Pendaftar</h1>
            </div>
            <p style="color: var(--gray-500); font-size: 0.85rem; margin: 0;">
                No: <strong><?php echo h($p['no_pendaftaran']); ?></strong> | 
                Daftar: <?php echo date('d M Y H:i', strtotime($p['created_at'])); ?>
            </p>
        </div>
        <div class="top-bar-actions">
            <?php if ($prev): ?>
            <a href="detail.php?id=<?php echo $prev['id']; ?>" class="btn-top-bar" style="background: var(--gray-500); font-size: 0.8rem;">
                <i class="fa-solid fa-chevron-left"></i> Sebelumnya
            </a>
            <?php endif; ?>
            <?php if ($next): ?>
            <a href="detail.php?id=<?php echo $next['id']; ?>" class="btn-top-bar" style="background: var(--gray-500); font-size: 0.8rem;">
                Selanjutnya <i class="fa-solid fa-chevron-right"></i>
            </a>
            <?php endif; ?>
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
    
    <!-- Action Bar -->
    <div class="action-bar-card">
        <div style="display: flex; align-items: center; gap: 16px;">
            <span style="font-weight: 600; font-size: 0.9rem;">Status:</span>
            <span class="badge <?php echo $sb['class']; ?>" style="font-size: 0.85rem; padding: 6px 16px;">
                <i class="fa-solid <?php echo $sb['icon']; ?> me-1"></i>
                <?php echo $sb['text']; ?>
            </span>
        </div>
        <div style="display: flex; gap: 8px;">
            <?php if ($p['status'] == 'pending'): ?>
            <a href="status.php?id=<?php echo $p['id']; ?>&status=diterima" class="btn btn-sm btn-success-custom" 
               onclick="return confirm('Terima pendaftar ini?')">
                <i class="fa-solid fa-check me-1"></i> Terima
            </a>
            <a href="status.php?id=<?php echo $p['id']; ?>&status=ditolak" class="btn btn-sm btn-danger-custom" 
               onclick="return confirm('Tolak pendaftar ini?')">
                <i class="fa-solid fa-times me-1"></i> Tolak
            </a>
            <?php elseif ($p['status'] == 'diterima'): ?>
            <a href="status.php?id=<?php echo $p['id']; ?>&status=pending" class="btn btn-sm btn-warning-custom">
                <i class="fa-solid fa-rotate-left me-1"></i> Kembalikan ke Pending
            </a>
            <?php if (!$dokumen): ?>
            <a href="generate-dokumen.php?id=<?php echo $p['id']; ?>" class="btn btn-sm btn-purple-custom">
                <i class="fa-solid fa-file-pdf me-1"></i> Buat Surat Diterima
            </a>
            <?php endif; ?>
            <?php elseif ($p['status'] == 'ditolak'): ?>
            <a href="status.php?id=<?php echo $p['id']; ?>&status=pending" class="btn btn-sm btn-warning-custom">
                <i class="fa-solid fa-rotate-left me-1"></i> Kembalikan ke Pending
            </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="row g-4">
        <!-- Kolom Kiri -->
        <div class="col-lg-7">
            
            <!-- Data Siswa -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div>
                        <i class="fa-solid fa-child" style="color: #2563eb;"></i>
                        <span>Data Siswa</span>
                    </div>
                </div>
                <div class="admin-card-body">
                    <table class="detail-table">
                        <tr>
                            <td class="detail-label-col">No. Pendaftaran</td>
                            <td class="detail-value-col" style="color: var(--primary); font-weight: 700;"><?php echo h($p['no_pendaftaran']); ?></td>
                        </tr>
                        <tr>
                            <td class="detail-label-col ">Nama Lengkap</td>
                            <td class="detail-value-col"><?php echo h($p['nama_lengkap']); ?></td>
                        </tr>
                        <tr>
                            <td class="detail-label-col">Tempat, Tgl Lahir</td>
                            <td class="detail-value-col"><?php echo h($p['tempat_lahir'] . ', ' . date('d F Y', strtotime($p['tanggal_lahir']))); ?></td>
                        </tr>
                        <tr>
                            <td class="detail-label-col">Jenis Kelamin</td>
                            <td class="detail-value-col"><?php echo h($p['jenis_kelamin']); ?></td>
                        </tr>
                        <tr>
                            <td class="detail-label-col">Agama</td>
                            <td class="detail-value-col"><?php echo h($p['agama']); ?></td>
                        </tr>
                        <tr>
                            <td class="detail-label-col">Anak Ke-</td>
                            <td class="detail-value-col"><?php echo h($p['anak_ke'] ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <td class="detail-label-col">Status Keluarga</td>
                            <td class="detail-value-col"><?php echo h($p['status_keluarga'] ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <td class="detail-label-col">Asal TK/RA</td>
                            <td class="detail-value-col"><?php echo h($p['asal_tk'] ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <td class="detail-label-col">No. STTB</td>
                            <td class="detail-value-col"><?php echo h($p['no_sttb'] ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <td class="detail-label-col">Tanggal Daftar</td>
                            <td class="detail-value-col"><?php echo date('d M Y H:i', strtotime($p['created_at'])); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Data Orang Tua -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div>
                        <i class="fa-solid fa-people-roof" style="color: #c8903e;"></i>
                        <span>Data Orang Tua</span>
                    </div>
                </div>
                <div class="admin-card-body">
                    <table class="detail-table">
                        <tr><td class="detail-label-col">Nama Ayah</td><td class="detail-value-col"><?php echo h($p['nama_ayah']); ?></td></tr>
                        <tr><td class="detail-label-col">NIK Ayah</td><td class="detail-value-col"><?php echo h($p['nik_ayah'] ?: '-'); ?></td></tr>
                        <tr><td class="detail-label-col">Pekerjaan Ayah</td><td class="detail-value-col"><?php echo h($p['pekerjaan_ayah'] ?: '-'); ?></td></tr>
                        <tr><td class="detail-label-col">Nama Ibu</td><td class="detail-value-col"><?php echo h($p['nama_ibu']); ?></td></tr>
                        <tr><td class="detail-label-col">NIK Ibu</td><td class="detail-value-col"><?php echo h($p['nik_ibu'] ?: '-'); ?></td></tr>
                        <tr><td class="detail-label-col">Pekerjaan Ibu</td><td class="detail-value-col"><?php echo h($p['pekerjaan_ibu'] ?: '-'); ?></td></tr>
                        <tr><td class="detail-label-col">No. HP</td><td class="detail-value-col"><strong><?php echo h($p['no_hp']); ?></strong></td></tr>
                        <tr><td class="detail-label-col">Alamat Ortu</td><td class="detail-value-col"><?php echo h($p['alamat_ortu'] ?: 'Sama dengan alamat siswa'); ?></td></tr>
                    </table>
                </div>
            </div>
            
        </div>
        
        <!-- Kolom Kanan -->
        <div class="col-lg-5">
            
            <!-- Alamat - DENGAN NAMA WILAYAH DARI API -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div>
                        <i class="fa-solid fa-location-dot" style="color: #059669;"></i>
                        <span>Alamat</span>
                    </div>
                </div>
                <div class="admin-card-body">
                    <p style="font-weight: 600; margin-bottom: 12px; font-size: 0.95rem; line-height: 1.6;">
                        <?php echo h($p['alamat']); ?>
                    </p>
                    <table class="detail-table" style="margin: 0;">
                        <tr>
                            <td class="detail-label-col" style="width: 100px;">Kecamatan</td>
                            <td class="detail-value-col"><?php echo h($kecamatan_nama); ?></td>
                        </tr>
                        <tr>
                            <td class="detail-label-col" style="width: 100px;">Kab/kota</td>
                            <td class="detail-value-col"><?php echo h($kabupaten_nama); ?></td>
                        </tr>
                        <tr>
                            <td class="detail-label-col" style="width: 100px;">Provinsi</td>
                            <td class="detail-value-col"><?php echo h($provinsi_nama); ?></td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Berkas -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div>
                        <i class="fa-solid fa-folder-open" style="color: #7c3aed;"></i>
                        <span>Berkas Pendaftaran</span>
                    </div>
                </div>
                <div class="admin-card-body">
                    <div class="berkas-list">
                        <?php if ($p['file_foto']): ?>
                        <div class="berkas-item">
                            <span><i class="fa-solid fa-camera me-2"></i> Pas Foto</span>
                            <a href="../../uploads/foto/<?php echo h($p['file_foto']); ?>" target="_blank" class="btn-action btn-view" style="text-decoration: none;">Lihat</a>
                        </div>
                        <?php else: ?>
                        <div class="berkas-item" style="color: var(--gray-400);"><span><i class="fa-solid fa-camera me-2"></i> Pas Foto</span><small>Tidak ada</small></div>
                        <?php endif; ?>
                        
                        <?php if ($p['file_akta']): ?>
                        <div class="berkas-item">
                            <span><i class="fa-solid fa-file me-2"></i> Akta Kelahiran</span>
                            <a href="../../uploads/akta/<?php echo h($p['file_akta']); ?>" target="_blank" class="btn-action btn-view" style="text-decoration: none;">Lihat</a>
                        </div>
                        <?php else: ?>
                        <div class="berkas-item" style="color: var(--gray-400);"><span><i class="fa-solid fa-file me-2"></i> Akta Kelahiran</span><small>Tidak ada</small></div>
                        <?php endif; ?>
                        
                        <?php if ($p['file_kk']): ?>
                        <div class="berkas-item">
                            <span><i class="fa-solid fa-users me-2"></i> Kartu Keluarga</span>
                            <a href="../../uploads/kk/<?php echo h($p['file_kk']); ?>" target="_blank" class="btn-action btn-view" style="text-decoration: none;">Lihat</a>
                        </div>
                        <?php else: ?>
                        <div class="berkas-item" style="color: var(--gray-400);"><span><i class="fa-solid fa-users me-2"></i> Kartu Keluarga</span><small>Tidak ada</small></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Dokumen Penerimaan -->
            <?php if ($p['status'] == 'diterima'): ?>
            <div class="admin-card" style="border-color: #059669;">
                <div class="admin-card-header" style="background: #ecfdf5;">
                    <div>
                        <i class="fa-solid fa-certificate" style="color: #059669;"></i>
                        <span>Surat Keterangan Diterima</span>
                    </div>
                </div>
                <div class="admin-card-body">
                    <?php if ($dokumen): ?>
                    <div class="text-center">
                        <i class="fa-solid fa-file-pdf fa-3x mb-3" style="color: #dc2626;"></i>
                        <p style="font-weight: 600; margin-bottom: 4px;">No: <?php echo h($dokumen['no_dokumen']); ?></p>
                        <small style="color: var(--gray-500);">Dibuat: <?php echo date('d/m/Y H:i', strtotime($dokumen['created_at'])); ?></small>
                        <div style="display: flex; gap: 8px; justify-content: center; margin-top: 12px;">
                            <a href="../../uploads/dokumen/<?php echo h($dokumen['file_dokumen']); ?>" target="_blank" class="btn btn-sm" style="background: #2563eb; color: white; padding: 6px 14px; border-radius: 8px; text-decoration: none;">
                                <i class="fa-solid fa-eye me-1"></i> Lihat
                            </a>
                            <a href="../../uploads/dokumen/<?php echo h($dokumen['file_dokumen']); ?>" download class="btn btn-sm" style="background: #059669; color: white; padding: 6px 14px; border-radius: 8px; text-decoration: none;">
                                <i class="fa-solid fa-download me-1"></i> Unduh
                            </a>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="text-center">
                        <i class="fa-solid fa-file-circle-plus fa-2x mb-2" style="color: var(--gray-400);"></i>
                        <p style="color: var(--gray-500); margin-bottom: 12px;">Dokumen belum dibuat</p>
                        <a href="generate-dokumen.php?id=<?php echo $p['id']; ?>" class="btn btn-sm" style="background: #7c3aed; color: white; padding: 8px 18px; border-radius: 8px; text-decoration: none; font-weight: 600;">
                            <i class="fa-solid fa-file-pdf me-1"></i> Buat Surat Diterima
                        </a>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>
            
            <!-- Cetak Bukti -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div>
                        <i class="fa-solid fa-print" style="color: #2563eb;"></i>
                        <span>Cetak Bukti</span>
                    </div>
                </div>
                <div class="admin-card-body text-center">
                    <p style="font-size: 0.9rem; color: var(--gray-500); margin-bottom: 12px;">
                        Cetak bukti pendaftaran untuk arsip atau diberikan ke orang tua siswa.
                    </p>
                    <a href="../../cetak-bukti.php?no=<?php echo h($p['no_pendaftaran']); ?>" target="_blank" class="btn btn-sm" style="background: #2563eb; color: white; padding: 8px 18px; border-radius: 8px; text-decoration: none; font-weight: 600; display: inline-flex; align-items: center; gap: 6px;">
                        <i class="fa-solid fa-print"></i> Cetak Bukti Pendaftaran
                    </a>
                </div>
            </div>
            
        </div>
    </div>
    
</main>

<style>
/* Action Bar */
.action-bar-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 16px 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 20px;
}

/* Detail Table */
.detail-table { width: 100%; border-collapse: collapse; }
.detail-table td { padding: 8px 0; border-bottom: 1px solid #f8fafc; font-size: 0.9rem; vertical-align: top; }
.detail-table tr:last-child td { border-bottom: none; }
.detail-label-col { width: 130px; color: #64748b; font-weight: 500; }
.detail-value-col { font-weight: 600; color: #1e293b; }

/* Berkas List */
.berkas-list { display: flex; flex-direction: column; gap: 8px; }
.berkas-item {
    display: flex; justify-content: space-between; align-items: center;
    padding: 10px 14px; background: #f8fafc; border-radius: 10px;
    font-size: 0.9rem; font-weight: 500;
}

/* Badge */
.badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
.badge-success { background: #ecfdf5; color: #059669; }
.badge-warning { background: #fffbeb; color: #d97706; }
.badge-danger { background: #fef2f2; color: #dc2626; }

/* Custom Buttons */
.btn-success-custom { background: #059669; color: white; padding: 8px 18px; border-radius: 8px; text-decoration: none; font-weight: 600; border: none; cursor: pointer; }
.btn-danger-custom { background: #dc2626; color: white; padding: 8px 18px; border-radius: 8px; text-decoration: none; font-weight: 600; border: none; cursor: pointer; }
.btn-warning-custom { background: #d97706; color: white; padding: 8px 18px; border-radius: 8px; text-decoration: none; font-weight: 600; border: none; cursor: pointer; }
.btn-purple-custom { background: #7c3aed; color: white; padding: 8px 18px; border-radius: 8px; text-decoration: none; font-weight: 600; border: none; cursor: pointer; }

.btn-success-custom:hover { background: #047857; color: white; }
.btn-danger-custom:hover { background: #b91c1c; color: white; }
.btn-warning-custom:hover { background: #b45309; color: white; }
.btn-purple-custom:hover { background: #6d28d9; color: white; }

@media (max-width: 767px) {
    .action-bar-card { flex-direction: column; align-items: flex-start; }
    .detail-label-col { width: 100px; font-size: 0.82rem; }
    .detail-value-col { font-size: 0.85rem; }
}
</style>

<?php require_once '../includes/footer-admin.php'; ?>