<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$current_admin_page = 'dashboard';
$page_title = 'Dashboard Admin';
$base_path = '../';

// Include header
require_once '../includes/header-admin.php';

$pdo = getConnection();

// Statistik
$stmt = $pdo->query("SELECT COUNT(*) FROM guru");
$total_guru = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM berita WHERE status = 'published'");
$total_berita = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM galeri");
$total_galeri = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM prestasi");
$total_prestasi = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM fasilitas");
$total_fasilitas = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM pendaftar");
$total_pendaftar = $stmt->fetchColumn();

$stmt = $pdo->query("SELECT COUNT(*) FROM pendaftar WHERE status = 'pending'");
$pending_ppdb = $stmt->fetchColumn();

// Berita terbaru
$stmt = $pdo->query("SELECT * FROM berita ORDER BY created_at DESC LIMIT 5");
$recent_berita = $stmt->fetchAll();

// Pendaftar terbaru
$stmt = $pdo->query("SELECT * FROM pendaftar ORDER BY created_at DESC LIMIT 5");
$recent_pendaftar = $stmt->fetchAll();

// Aktivitas terbaru (gabungan)
$aktivitas = [];
$stmt = $pdo->query("SELECT 'berita' as tipe, judul as nama, created_at FROM berita ORDER BY created_at DESC LIMIT 3");
$aktivitas = $stmt->fetchAll();
$stmt = $pdo->query("SELECT 'pendaftar' as tipe, nama_lengkap as nama, created_at FROM pendaftar ORDER BY created_at DESC LIMIT 3");
$aktivitas = array_merge($aktivitas, $stmt->fetchAll());

// Urutkan aktivitas
usort($aktivitas, function($a, $b) {
    return strtotime($b['created_at']) - strtotime($a['created_at']);
});
$aktivitas = array_slice($aktivitas, 0, 5);
?>

<!-- Include Sidebar -->
<?php require_once '../includes/sidebar.php'; ?>

<!-- Main Content -->
<main class="main-content">
    
    <!-- Top Bar -->
    <div class="top-bar">
        <div>
            <h1><i class="fa-solid fa-gauge-high me-2" style="color: var(--primary);"></i>Dashboard</h1>
            <p style="color: var(--gray-500); font-size: 0.9rem; margin: 4px 0 0;">Selamat datang, <strong><?php echo h($_SESSION['admin_nama'] ?? $_SESSION['admin_username'] ?? 'Admin'); ?></strong></p>
        </div>
        <div class="top-bar-actions">
            <span class="top-bar-date">
                <i class="fa-regular fa-calendar me-1"></i> <?php echo date('d M Y'); ?>
            </span>
            <a href="../../index.php" target="_blank" class="btn-top-bar">
                <i class="fa-solid fa-eye me-1"></i> Lihat Web
            </a>
        </div>
    </div>
    
    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon" style="background: #eff6ff; color: #2563eb;">
                <i class="fa-solid fa-chalkboard-user"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?php echo $total_guru; ?></div>
                <div class="stat-label">Total Guru</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #fef9f0; color: #c8903e;">
                <i class="fa-solid fa-newspaper"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?php echo $total_berita; ?></div>
                <div class="stat-label">Berita Published</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #ecfdf5; color: #059669;">
                <i class="fa-solid fa-images"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?php echo $total_galeri; ?></div>
                <div class="stat-label">Foto Galeri</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #fef2f2; color: #dc2626;">
                <i class="fa-solid fa-trophy"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?php echo $total_prestasi; ?></div>
                <div class="stat-label">Prestasi</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #f5f3ff; color: #7c3aed;">
                <i class="fa-solid fa-building"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?php echo $total_fasilitas; ?></div>
                <div class="stat-label">Fasilitas</div>
            </div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon" style="background: #fff7ed; color: #ea580c;">
                <i class="fa-solid fa-graduation-cap"></i>
            </div>
            <div class="stat-info">
                <div class="stat-value"><?php echo $total_pendaftar; ?></div>
                <div class="stat-label">Pendaftar PPDB</div>
            </div>
        </div>
    </div>
    
    <div class="row g-4">
        <!-- Kolom Kiri -->
        <div class="col-lg-7">
            
            <!-- Berita Terbaru -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div>
                        <i class="fa-solid fa-newspaper" style="color: #c8903e;"></i>
                        <span>Berita Terbaru</span>
                    </div>
                    <a href="../berita/" class="btn-sm-link">Lihat Semua →</a>
                </div>
                <div class="admin-card-body" style="padding: 0;">
                    <?php if (!empty($recent_berita)): ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Judul</th>
                                <th>Status</th>
                                <th>Tanggal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_berita as $b): ?>
                            <tr>
                                <td>
                                    <a href="berita/edit.php?id=<?php echo $b['id']; ?>" style="color: var(--gray-800); text-decoration: none; font-weight: 600;">
                                        <?php echo h($b['judul']); ?>
                                    </a>
                                </td>
                                <td>
                                    <?php if ($b['status'] == 'published'): ?>
                                    <span class="badge badge-success">Published</span>
                                    <?php else: ?>
                                    <span class="badge badge-warning">Draft</span>
                                    <?php endif; ?>
                                </td>
                                <td style="color: var(--gray-500); font-size: 0.85rem;"><?php echo date('d/m/Y', strtotime($b['created_at'])); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-newspaper"></i>
                        <p>Belum ada berita</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Aktivitas Terbaru -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div>
                        <i class="fa-solid fa-clock-rotate-left" style="color: #7c3aed;"></i>
                        <span>Aktivitas Terbaru</span>
                    </div>
                </div>
                <div class="admin-card-body">
                    <?php if (!empty($aktivitas)): ?>
                    <div class="activity-list">
                        <?php foreach ($aktivitas as $act): ?>
                        <div class="activity-item">
                            <div class="activity-icon" style="background: <?php echo $act['tipe'] == 'berita' ? '#eff6ff' : '#fff7ed'; ?>; color: <?php echo $act['tipe'] == 'berita' ? '#2563eb' : '#ea580c'; ?>;">
                                <i class="fa-solid <?php echo $act['tipe'] == 'berita' ? 'fa-newspaper' : 'fa-graduation-cap'; ?>"></i>
                            </div>
                            <div class="activity-info">
                                <p class="activity-text">
                                    <?php echo $act['tipe'] == 'berita' ? 'Berita baru' : 'Pendaftar PPDB'; ?>: 
                                    <strong><?php echo h($act['nama']); ?></strong>
                                </p>
                                <span class="activity-time"><?php echo date('d M Y H:i', strtotime($act['created_at'])); ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-clock-rotate-left"></i>
                        <p>Belum ada aktivitas</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
        </div>
        
        <!-- Kolom Kanan -->
        <div class="col-lg-5">
            
            <!-- Pendaftar PPDB Terbaru -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div>
                        <i class="fa-solid fa-graduation-cap" style="color: #ea580c;"></i>
                        <span>Pendaftar PPDB Terbaru</span>
                    </div>
                    <a href="../ppdb/" class="btn-sm-link">Lihat Semua →</a>
                </div>
                <div class="admin-card-body" style="padding: 0;">
                    <?php if (!empty($recent_pendaftar)): ?>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_pendaftar as $p): ?>
                            <tr>
                                <td style="font-weight: 600;"><?php echo h($p['nama_lengkap']); ?></td>
                                <td>
                                    <?php if ($p['status'] == 'diterima'): ?>
                                    <span class="badge badge-success">Diterima</span>
                                    <?php elseif ($p['status'] == 'ditolak'): ?>
                                    <span class="badge badge-danger">Ditolak</span>
                                    <?php else: ?>
                                    <span class="badge badge-warning">Pending</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="empty-state">
                        <i class="fa-solid fa-graduation-cap"></i>
                        <p>Belum ada pendaftar</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <div>
                        <i class="fa-solid fa-bolt" style="color: #f59e0b;"></i>
                        <span>Quick Actions</span>
                    </div>
                </div>
                <div class="admin-card-body">
                    <div class="quick-actions-grid">
                        <a href="../berita/tambah.php" class="quick-action-item" style="background: #eff6ff; color: #2563eb;">
                            <i class="fa-solid fa-plus-circle"></i>
                            <span>Tambah Berita</span>
                        </a>
                        <a href="../guru/tambah.php" class="quick-action-item" style="background: #fef9f0; color: #c8903e;">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>Tambah Guru</span>
                        </a>
                        <a href="../galeri/tambah.php" class="quick-action-item" style="background: #ecfdf5; color: #059669;">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <span>Upload Galeri</span>
                        </a>
                        <a href="../prestasi/tambah.php" class="quick-action-item" style="background: #fef2f2; color: #dc2626;">
                            <i class="fa-solid fa-trophy"></i>
                            <span>Tambah Prestasi</span>
                        </a>
                        <a href="../fasilitas/tambah.php" class="quick-action-item" style="background: #f5f3ff; color: #7c3aed;">
                            <i class="fa-solid fa-building"></i>
                            <span>Tambah Fasilitas</span>
                        </a>
                        <a href="../ppdb/" class="quick-action-item" style="background: #fff7ed; color: #ea580c;">
                            <i class="fa-solid fa-users"></i>
                            <span>Kelola PPDB</span>
                        </a>
                    </div>
                </div>
            </div>
            
        </div>
    </div>
    
</main>

<?php require_once '../includes/footer-admin.php'; ?>