<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$current_admin_page = 'ppdb';
$page_title = 'Manajemen PPDB';
$base_path = '../../';

require_once '../includes/header-admin.php';

$pdo = getConnection();

// Search & Filter
$search = trim($_GET['search'] ?? '');
$status_filter = trim($_GET['status'] ?? '');

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Build query
$where = "WHERE 1=1";
$params = [];

if (!empty($search)) {
    $where .= " AND (nama_lengkap LIKE ? OR no_pendaftaran LIKE ? OR no_hp LIKE ? OR asal_tk LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

if (!empty($status_filter) && in_array($status_filter, ['pending', 'diterima', 'ditolak'])) {
    $where .= " AND status = ?";
    $params[] = $status_filter;
}

// Hitung statistik
$stmt = $pdo->query("SELECT COUNT(*) FROM pendaftar");
$total_all = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT COUNT(*) FROM pendaftar WHERE status = 'pending'");
$total_pending = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT COUNT(*) FROM pendaftar WHERE status = 'diterima'");
$total_diterima = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT COUNT(*) FROM pendaftar WHERE status = 'ditolak'");
$total_ditolak = $stmt->fetchColumn();

// Total data sesuai filter
$stmt = $pdo->prepare("SELECT COUNT(*) FROM pendaftar $where");
$stmt->execute($params);
$total_data = $stmt->fetchColumn();
$total_pages = ceil($total_data / $limit);

// Ambil data
$query_params = array_merge($params, [$limit, $offset]);
$stmt = $pdo->prepare("SELECT * FROM pendaftar $where ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->execute($query_params);
$pendaftar_list = $stmt->fetchAll();

require_once '../includes/sidebar.php';
?>

<main class="main-content">
    
    <!-- Top Bar -->
    <div class="top-bar">
        <div>
            <h1><i class="fa-solid fa-graduation-cap me-2" style="color: #ea580c;"></i>Manajemen PPDB</h1>
            <p style="color: var(--gray-500); font-size: 0.9rem; margin: 4px 0 0;">
                Total: <strong><?php echo $total_all; ?></strong> | 
                <span style="color: #d97706;">Pending: <?php echo $total_pending; ?></span> | 
                <span style="color: #059669;">Diterima: <?php echo $total_diterima; ?></span> | 
                <span style="color: #dc2626;">Ditolak: <?php echo $total_ditolak; ?></span>
            </p>
        </div>
        <div class="top-bar-actions">
            <a href="../../ppdb.php" target="_blank" class="btn-top-bar" style="background: var(--gray-500);">
                <i class="fa-solid fa-eye me-1"></i> Lihat Halaman
            </a>
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
    
    <!-- Stats -->
    <div class="stats-mini-grid">
        <div class="stat-mini-card" style="border-left: 4px solid #ea580c;">
            <div class="stat-mini-icon" style="background: #fff7ed; color: #ea580c;">
                <i class="fa-solid fa-users"></i>
            </div>
            <div><h4><?php echo $total_all; ?></h4><span>Total Pendaftar</span></div>
        </div>
        <div class="stat-mini-card" style="border-left: 4px solid #d97706;">
            <div class="stat-mini-icon" style="background: #fffbeb; color: #d97706;">
                <i class="fa-solid fa-clock"></i>
            </div>
            <div><h4><?php echo $total_pending; ?></h4><span>Pending</span></div>
        </div>
        <div class="stat-mini-card" style="border-left: 4px solid #059669;">
            <div class="stat-mini-icon" style="background: #ecfdf5; color: #059669;">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div><h4><?php echo $total_diterima; ?></h4><span>Diterima</span></div>
        </div>
        <div class="stat-mini-card" style="border-left: 4px solid #dc2626;">
            <div class="stat-mini-icon" style="background: #fef2f2; color: #dc2626;">
                <i class="fa-solid fa-circle-xmark"></i>
            </div>
            <div><h4><?php echo $total_ditolak; ?></h4><span>Ditolak</span></div>
        </div>
    </div>
    
    <!-- Search & Filter -->
    <div class="search-filter-bar">
        <form method="GET" class="search-form">
            <div class="search-input-group">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" value="<?php echo h($search); ?>" placeholder="Cari nama, no pendaftaran, no HP, atau asal TK...">
                <?php if (!empty($status_filter)): ?>
                <input type="hidden" name="status" value="<?php echo h($status_filter); ?>">
                <?php endif; ?>
                <button type="submit">Cari</button>
            </div>
        </form>
        
        <div class="filter-buttons">
            <a href="index.php<?php echo !empty($search) ? '?search='.urlencode($search) : ''; ?>" 
               class="filter-btn <?php echo empty($status_filter) ? 'active' : ''; ?>">
                Semua
            </a>
            <a href="?status=pending<?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?>" 
               class="filter-btn <?php echo $status_filter == 'pending' ? 'active' : ''; ?>" 
               style="<?php echo $status_filter == 'pending' ? 'background: #d97706; border-color: #d97706;' : ''; ?>">
                Pending
            </a>
            <a href="?status=diterima<?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?>" 
               class="filter-btn <?php echo $status_filter == 'diterima' ? 'active' : ''; ?>" 
               style="<?php echo $status_filter == 'diterima' ? 'background: #059669; border-color: #059669;' : ''; ?>">
                Diterima
            </a>
            <a href="?status=ditolak<?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?>" 
               class="filter-btn <?php echo $status_filter == 'ditolak' ? 'active' : ''; ?>" 
               style="<?php echo $status_filter == 'ditolak' ? 'background: #dc2626; border-color: #dc2626;' : ''; ?>">
                Ditolak
            </a>
            
            <?php if (!empty($search) || !empty($status_filter)): ?>
            <a href="index.php" class="filter-clear"><i class="fa-solid fa-times"></i> Reset</a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Result Info -->
    <div class="result-info">
        Menampilkan <strong><?php echo count($pendaftar_list); ?></strong> dari <strong><?php echo $total_data; ?></strong> pendaftar
        <?php if (!empty($search)): ?> untuk "<strong><?php echo h($search); ?></strong>"<?php endif; ?>
    </div>
    
    <!-- Table -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <i class="fa-solid fa-list" style="color: #ea580c;"></i>
                <span>Daftar Pendaftar</span>
            </div>
            <span class="badge badge-info"><?php echo $total_data; ?> Data</span>
        </div>
        <div class="admin-card-body" style="padding: 0;">
            <?php if (!empty($pendaftar_list)): ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">No. Daftar</th>
                            <th width="20%">Nama</th>
                            <th width="10%">Asal TK</th>
                            <th width="12%">No. HP</th>
                            <th width="12%">Tanggal</th>
                            <th width="10%">Status</th>
                            <th width="19%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pendaftar_list as $i => $p): ?>
                        <tr>
                            <td><?php echo ($page - 1) * $limit + $i + 1; ?></td>
                            <td><small style="font-weight: 600; color: var(--primary);"><?php echo h($p['no_pendaftaran']); ?></small></td>
                            <td><strong><?php echo h($p['nama_lengkap']); ?></strong></td>
                            <td><?php echo h($p['asal_tk'] ?: '-'); ?></td>
                            <td><?php echo h($p['no_hp']); ?></td>
                            <td style="color: var(--gray-500); font-size: 0.85rem;"><?php echo date('d/m/Y', strtotime($p['created_at'])); ?></td>
                            <td>
                                <?php if ($p['status'] == 'pending'): ?>
                                <span class="badge badge-warning">Pending</span>
                                <?php elseif ($p['status'] == 'diterima'): ?>
                                <span class="badge badge-success">Diterima</span>
                                <?php else: ?>
                                <span class="badge badge-danger">Ditolak</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="detail.php?id=<?php echo $p['id']; ?>" class="btn-action btn-view" title="Detail">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a href="status.php?id=<?php echo $p['id']; ?>&status=diterima" class="btn-action btn-edit" title="Terima" 
                                       onclick="return confirm('Terima pendaftar ini?')">
                                        <i class="fa-solid fa-check"></i>
                                    </a>
                                    <a href="status.php?id=<?php echo $p['id']; ?>&status=ditolak" class="btn-action btn-delete" title="Tolak" 
                                       onclick="return confirm('Tolak pendaftar ini?')">
                                        <i class="fa-solid fa-times"></i>
                                    </a>
                                    <a href="hapus.php?id=<?php echo $p['id']; ?>" class="btn-action btn-delete" title="Hapus" 
                                       onclick="return confirm('Yakin hapus Pendaftar ini?')">
                                        <i class="fa-solid fa-trash"></i>
                                </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="pagination-bar">
                <?php 
                $qs = '';
                if (!empty($search)) $qs .= '&search=' . urlencode($search);
                if (!empty($status_filter)) $qs .= '&status=' . urlencode($status_filter);
                
                if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1 . $qs; ?>" class="page-btn"><i class="fa-solid fa-chevron-left"></i></a>
                <?php endif; ?>
                
                <?php 
                $start = max(1, $page - 2);
                $end = min($total_pages, $page + 2);
                
                if ($start > 1): ?>
                <a href="?page=1<?php echo $qs; ?>" class="page-btn">1</a>
                <?php if ($start > 2): ?><span class="page-dots">...</span><?php endif; ?>
                <?php endif; ?>
                
                <?php for ($i = $start; $i <= $end; $i++): ?>
                <a href="?page=<?php echo $i . $qs; ?>" class="page-btn <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                
                <?php if ($end < $total_pages): ?>
                <?php if ($end < $total_pages - 1): ?><span class="page-dots">...</span><?php endif; ?>
                <a href="?page=<?php echo $total_pages . $qs; ?>" class="page-btn"><?php echo $total_pages; ?></a>
                <?php endif; ?>
                
                <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1 . $qs; ?>" class="page-btn"><i class="fa-solid fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <?php else: ?>
            <div class="empty-state">
                <i class="fa-solid fa-graduation-cap"></i>
                <p>
                    <?php if (!empty($search)): ?>
                        Tidak ada pendaftar untuk "<strong><?php echo h($search); ?></strong>"
                    <?php else: ?>
                        Belum ada pendaftar
                    <?php endif; ?>
                </p>
                <?php if (!empty($search) || !empty($status_filter)): ?>
                <a href="index.php" class="btn-top-bar" style="margin-top: 12px; display: inline-flex; background: var(--gray-500);">
                    <i class="fa-solid fa-times me-1"></i> Reset Filter
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
</main>

<?php require_once '../includes/footer-admin.php'; ?>