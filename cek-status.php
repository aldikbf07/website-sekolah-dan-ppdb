<?php
$page_title = 'Cek Status Pendaftaran';
$current_page = 'ppdb';
require_once 'config/database.php';
require_once 'includes/header.php';
require_once 'config/wilayah.php';

$status_result = null;
$dokumen = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_pendaftaran = trim($_POST['no_pendaftaran'] ?? '');
    
    if (empty($no_pendaftaran)) {
        $error = 'Mohon isi nomor pendaftaran';
    } else {
        try {
            $pdo = getConnection();
            $stmt = $pdo->prepare("SELECT * FROM pendaftar WHERE no_pendaftaran = ?");
            $stmt->execute([$no_pendaftaran]);
            $status_result = $stmt->fetch();
            
            if (!$status_result) {
                $error = 'Nomor pendaftaran tidak ditemukan. Periksa kembali nomor Anda.';
            } else {
                // Cek dokumen jika diterima
                if ($status_result['status'] == 'diterima') {
                    $stmt = $pdo->prepare("SELECT * FROM dokumen_penerimaan WHERE pendaftar_id = ? ORDER BY created_at DESC LIMIT 1");
                    $stmt->execute([$status_result['id']]);
                    $dokumen = $stmt->fetch();
                }
            }
        } catch (PDOException $e) {
            $error = 'Terjadi kesalahan. Silakan coba lagi.';
        }
    }
}

$kecamatan_nama = $kabupaten_nama = $provinsi_nama = '-';
if ($status_result) {
    $kecamatan_nama = getWilayahName('district', $status_result['kecamatan']);
    $kabupaten_nama = getWilayahName('regency', $status_result['kabupaten']);
    $provinsi_nama = getWilayahName('province', $status_result['provinsi']);
}

// Warna status
$status_colors = [
    'pending' => ['bg' => '#fffbeb', 'text' => '#d97706', 'border' => '#fbbf24', 'icon' => 'fa-solid fa-clock', 'label' => 'Sedang Diproses'],
    'diterima' => ['bg' => '#ecfdf5', 'text' => '#059669', 'border' => '#34d399', 'icon' => 'fa-solid fa-circle-check', 'label' => 'DITERIMA'],
    'ditolak' => ['bg' => '#fef2f2', 'text' => '#dc2626', 'border' => '#f87171', 'icon' => 'fa-solid fa-circle-xmark', 'label' => 'DITOLAK'],
];
?>

<!-- Page Header -->
<section class="page-header-modern page-header-ppdb">
    <div class="page-header-dots"></div>
    <div class="page-header-grid"></div>
    <div class="container">
        <div class="header-breadcrumb" data-aos="fade-down">
            <a href="index.php"><i class="fa-solid fa-house me-1"></i> Beranda</a>
            <span class="separator">›</span>
            <a href="ppdb.php">PPDB</a>
            <span class="separator">›</span>
            <span>Cek Status</span>
        </div>
        <div class="header-icon" data-aos="zoom-in" data-aos-delay="100">
            <i class="fa-solid fa-magnifying-glass"></i>
        </div>
        <h1 class="header-title" data-aos="fade-up" data-aos-delay="200">Cek Status Pendaftaran</h1>
        <p class="header-desc" data-aos="fade-up" data-aos-delay="300">Masukkan nomor pendaftaran untuk melihat status PPDB</p>
    </div>
</section>

<!-- Content -->
<section class="section-modern section-white" style="padding-top: 20px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                
                <!-- Form Pencarian -->
                <div class="search-card" data-aos="fade-up">
                    <div class="search-card-body">
                        <div class="search-icon">
                            <i class="fa-solid fa-ticket"></i>
                        </div>
                        <h4>Cek Status Pendaftaran</h4>
                        <p class="search-desc">Masukkan nomor pendaftaran yang Anda dapatkan saat mendaftar</p>
                        
                        <?php if ($error): ?>
                        <div class="alert-modern alert-danger mb-3" style="text-align: left;">
                            <i class="fa-solid fa-circle-exclamation"></i>
                            <span><?php echo $error; ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label-modern text-start d-block">Nomor Pendaftaran</label>
                                <input type="text" name="no_pendaftaran" class="form-modern" 
                                       placeholder="Contoh: PPDB2026-001" 
                                       value="<?php echo h($_POST['no_pendaftaran'] ?? ''); ?>"
                                       style="font-size: 1.1rem; text-align: center; letter-spacing: 1px; font-weight: 600;"
                                       required autofocus>
                                <small class="text-muted mt-1 d-block">Format: PPDB2026-XXX (contoh: PPDB2026-001)</small>
                            </div>
                            <button type="submit" class="btn-modern btn-primary-modern w-100" style="padding: 14px;">
                                <i class="fa-solid fa-search me-2"></i> CEK STATUS
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Hasil Status -->
                <?php if ($status_result): 
                    $s = $status_colors[$status_result['status']];
                ?>
                <div class="status-card" data-aos="zoom-in" style="border-top: 4px solid <?php echo $s['border']; ?>; margin-top: 20px;">
                    <div class="status-card-body">
                        
                        <!-- Status Header -->
                        <div class="status-header">
                            <div class="status-icon-circle" style="background: <?php echo $s['bg']; ?>; color: <?php echo $s['text']; ?>; border: 3px solid <?php echo $s['border']; ?>;">
                                <i class="<?php echo $s['icon']; ?>"></i>
                            </div>
                            <div class="status-info">
                                <h3 style="color: <?php echo $s['text']; ?>;"><?php echo $s['label']; ?></h3>
                                <p>No: <strong><?php echo h($status_result['no_pendaftaran']); ?></strong></p>
                            </div>
                        </div>
                        
                        <!-- Detail Grid -->
                        <div class="detail-grid">
                            <div class="detail-box">
                                <span class="detail-label">Nama Lengkap</span>
                                <span class="detail-value"><?php echo h($status_result['nama_lengkap']); ?></span>
                            </div>
                            <div class="detail-box">
                                <span class="detail-label">Tempat, Tgl Lahir</span>
                                <span class="detail-value"><?php echo h($status_result['tempat_lahir'] . ', ' . date('d/m/Y', strtotime($status_result['tanggal_lahir']))); ?></span>
                            </div>
                            <div class="detail-box">
                                <span class="detail-label">Jenis Kelamin</span>
                                <span class="detail-value"><?php echo h($status_result['jenis_kelamin']); ?></span>
                            </div>
                            <div class="detail-box">
                                <span class="detail-label">Asal TK/RA</span>
                                <span class="detail-value"><?php echo h($status_result['asal_tk'] ?: '-'); ?></span>
                            </div>
                            <div class="detail-box detail-box-full">
                                <span class="detail-label">Alamat</span>
                                <span class="detail-value"><?php echo h($status_result['alamat'] . ', ' . $kecamatan_nama . ', ' . $kabupaten_nama . ', ' . $provinsi_nama); ?></span>
                            </div>
                            <div class="detail-box">
                                <span class="detail-label">Orang Tua</span>
                                <span class="detail-value"><?php echo h($status_result['nama_ayah'] . ' / ' . $status_result['nama_ibu']); ?></span>
                            </div>
                            <div class="detail-box">
                                <span class="detail-label">Tanggal Daftar</span>
                                <span class="detail-value"><?php echo date('d M Y H:i', strtotime($status_result['created_at'])); ?></span>
                            </div>
                        </div>
                        
                        <!-- Status Message -->
                        <?php if ($status_result['status'] == 'diterima'): ?>
                        <div class="status-message" style="background: <?php echo $s['bg']; ?>; border: 2px solid <?php echo $s['border']; ?>;">
                            <h5 style="color: <?php echo $s['text']; ?>;"><i class="fa-solid fa-check-circle"></i> SELAMAT! Putra-putri Anda DITERIMA!</h5>
                            <p style="color: var(--gray-600);">Daftar ulang pada <strong>11 Juli <?php echo date('Y'); ?></strong></p>
                            
                            <?php if ($dokumen && $dokumen['file_dokumen']): ?>
                            <div class="dokumen-box">
                                <p><i class="fa-solid fa-file-pdf me-2" style="color: #dc2626;"></i>Surat Keterangan Diterima</p>
                                <div class="d-flex gap-2 justify-content-center">
                                    <a href="uploads/dokumen/<?php echo h($dokumen['file_dokumen']); ?>" target="_blank" class="btn-modern btn-primary-modern btn-sm" style="padding:6px 14px;font-size:0.82rem;">
                                        <i class="fa-solid fa-eye me-1"></i> Lihat
                                    </a>
                                    <a href="uploads/dokumen/<?php echo h($dokumen['file_dokumen']); ?>" download class="btn-modern btn-outline-modern btn-sm" style="padding:6px 14px;font-size:0.82rem;">
                                        <i class="fa-solid fa-download me-1"></i> Unduh
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>
                            
                            <!-- Tombol Cetak Bukti -->
                            <div class="mt-3">
                                <a href="cetak-bukti.php?no=<?php echo h($status_result['no_pendaftaran']); ?>" 
                                   target="_blank" 
                                   class="btn-modern btn-white-modern btn-sm">
                                    <i class="fa-solid fa-print me-1"></i> Cetak Bukti Pendaftaran
                                </a>
                            </div>
                        </div>
                        
                        <?php elseif ($status_result['status'] == 'pending'): ?>
                        <div class="status-message" style="background: <?php echo $s['bg']; ?>; border: 2px solid <?php echo $s['border']; ?>;">
                            <h5 style="color: <?php echo $s['text']; ?>;"><i class="fa-solid fa-clock"></i> Pendaftaran Sedang Diproses</h5>
                            <p style="color: var(--gray-600);">Cek kembali secara berkala untuk status terbaru.</p>
                            <div class="mt-3">
                                <a href="cetak-bukti.php?no=<?php echo h($status_result['no_pendaftaran']); ?>" 
                                   target="_blank" 
                                   class="btn-modern btn-white-modern btn-sm">
                                    <i class="fa-solid fa-print me-1"></i> Cetak Bukti Pendaftaran
                                </a>
                            </div>
                        </div>
                        
                        <?php elseif ($status_result['status'] == 'ditolak'): ?>
                        <div class="status-message" style="background: <?php echo $s['bg']; ?>; border: 2px solid <?php echo $s['border']; ?>;">
                            <h5 style="color: <?php echo $s['text']; ?>;"><i class="fa-solid fa-times-circle"></i> Mohon Maaf</h5>
                            <p style="color: var(--gray-600);">Hubungi panitia untuk informasi lebih lanjut.</p>
                        </div>
                        <?php endif; ?>
                        
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Link Bantuan -->
                <div class="text-center mt-4 mb-5">
                    <p style="color: var(--gray-500); font-size: 0.9rem; margin-bottom: 12px;">Lupa nomor pendaftaran?</p>
                    <div class="d-flex justify-content-center gap-2 flex-wrap">
                        <a href="ppdb.php#kontak" class="btn-modern btn-outline-modern btn-sm">
                            <i class="fa-solid fa-phone me-1"></i> Hubungi Panitia
                        </a>
                        <a href="ppdb.php" class="btn-modern btn-outline-modern btn-sm">
                            <i class="fa-solid fa-circle-info me-1"></i> Info PPDB
                        </a>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</section>

<style>
/* Search Card */
.search-card {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 16px;
    box-shadow: var(--shadow-sm);
    margin-bottom: 20px;
}

.search-card .search-card-body {
    padding: 32px;
    text-align: center;
}

.search-card .search-icon {
    width: 64px;
    height: 64px;
    background: var(--primary-light);
    border-radius: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 1.6rem;
    color: var(--primary);
    margin-bottom: 14px;
}

.search-card h4 {
    font-family: var(--font-heading);
    font-weight: 700;
    font-size: 1.3rem;
    margin-bottom: 6px;
}

.search-card .search-desc {
    color: var(--gray-500);
    font-size: 0.9rem;
    margin-bottom: 20px;
}

/* Status Card */
.status-card {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
}

.status-card .status-card-body {
    padding: 28px;
}

.status-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 20px;
    padding-bottom: 20px;
    border-bottom: 1px solid var(--gray-200);
}

.status-icon-circle {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    flex-shrink: 0;
}

.status-info h3 {
    font-size: 1.3rem;
    margin-bottom: 2px;
}

.status-info p {
    font-size: 0.85rem;
    color: var(--gray-500);
    margin: 0;
}

/* Detail Grid */
.detail-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 20px;
}

.detail-box {
    background: var(--gray-50);
    border: 1px solid var(--gray-200);
    border-radius: 10px;
    padding: 10px 14px;
}

.detail-box-full {
    grid-column: 1 / -1;
}

.detail-label {
    display: block;
    font-size: 0.7rem;
    font-weight: 600;
    color: var(--gray-400);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 2px;
}

.detail-value {
    display: block;
    font-weight: 600;
    font-size: 0.88rem;
    color: var(--gray-800);
    line-height: 1.3;
}

/* Status Message */
.status-message {
    padding: 16px 20px;
    border-radius: 14px;
    text-align: center;
    margin-top: 16px;
}

.status-message h5 {
    font-size: 1rem;
    margin-bottom: 6px;
}

.status-message p {
    font-size: 0.88rem;
    margin: 0;
    line-height: 1.5;
}

/* Dokumen Box */
.dokumen-box {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 12px;
    padding: 14px 18px;
    text-align: center;
    margin-top: 12px;
}

.dokumen-box p {
    font-weight: 600;
    font-size: 0.9rem;
    color: var(--gray-700);
    margin-bottom: 10px;
}

/* Responsive */
@media (max-width: 767px) {
    .search-card .search-card-body {
        padding: 24px 20px;
    }
    
    .search-card .search-icon {
        width: 52px;
        height: 52px;
        font-size: 1.3rem;
    }
    
    .status-card .status-card-body {
        padding: 20px;
    }
    
    .detail-grid {
        grid-template-columns: 1fr;
        gap: 8px;
    }
    
    .status-header {
        flex-direction: column;
        text-align: center;
        gap: 10px;
    }
}

@media (max-width: 575px) {
    .search-card .search-card-body {
        padding: 20px 16px;
    }
    
    .status-icon-circle {
        width: 52px;
        height: 52px;
        font-size: 1.4rem;
    }
}
</style>

<?php require_once 'includes/footer.php'; ?>