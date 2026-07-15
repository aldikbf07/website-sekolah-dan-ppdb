<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$current_admin_page = 'berita';
$page_title = 'Manajemen Berita';
$base_path = '../../';

require_once '../includes/header-admin.php';

$pdo = getConnection();

// Search & Filter
$search = trim($_GET['search'] ?? '');
$kategori_filter = trim($_GET['kategori'] ?? '');
$status_filter = trim($_GET['status'] ?? '');

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Build query
$where = "WHERE 1=1";
$params = [];

if (!empty($search)) {
    $where .= " AND (judul LIKE ? OR isi LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($kategori_filter)) {
    $where .= " AND kategori = ?";
    $params[] = $kategori_filter;
}

if (!empty($status_filter)) {
    $where .= " AND status = ?";
    $params[] = $status_filter;
}

// Total data
$stmt = $pdo->prepare("SELECT COUNT(*) FROM berita $where");
$stmt->execute($params);
$total_data = $stmt->fetchColumn();
$total_pages = ceil($total_data / $limit);

// Data
$query_params = array_merge($params, [$limit, $offset]);
$stmt = $pdo->prepare("SELECT * FROM berita $where ORDER BY tanggal DESC, created_at DESC LIMIT ? OFFSET ?");
$stmt->execute($query_params);
$berita_list = $stmt->fetchAll();

// Statistik
$stmt = $pdo->query("SELECT COUNT(*) FROM berita");
$total_all = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT COUNT(*) FROM berita WHERE status = 'published'");
$published = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT COUNT(*) FROM berita WHERE status = 'draft'");
$draft = $stmt->fetchColumn();

// Kategori list
$stmt = $pdo->query("SELECT DISTINCT kategori FROM berita WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori");
$kategori_list = $stmt->fetchAll();

require_once '../includes/sidebar.php';
?>

<main class="main-content">
    
    <div class="top-bar">
        <div>
            <h1><i class="fa-solid fa-newspaper me-2" style="color: #c8903e;"></i>Manajemen Berita</h1>
            <p style="color: var(--gray-500); font-size: 0.9rem; margin: 4px 0 0;">
                Total: <strong><?php echo $total_all; ?></strong> | Published: <?php echo $published; ?> | Draft: <?php echo $draft; ?>
            </p>
        </div>
        <div class="top-bar-actions">
            <a href="tambah.php" class="btn-top-bar" style="background: #c8903e;">
                <i class="fa-solid fa-plus me-1"></i> Tambah Berita
            </a>
        </div>
    </div>
    
    <!-- Alert Messages -->
    <?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><i class="fa-solid fa-circle-check"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <!-- Search & Filter -->
    <div class="search-filter-bar">
        <form method="GET" class="search-form">
            <div class="search-input-group">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" value="<?php echo h($search); ?>" placeholder="Cari judul atau isi berita...">
                <?php if (!empty($kategori_filter)): ?>
                <input type="hidden" name="kategori" value="<?php echo h($kategori_filter); ?>">
                <?php endif; ?>
                <?php if (!empty($status_filter)): ?>
                <input type="hidden" name="status" value="<?php echo h($status_filter); ?>">
                <?php endif; ?>
                <button type="submit">Cari</button>
            </div>
        </form>
        
        <div class="filter-buttons">
            <!-- Filter Kategori -->
            <select class="filter-select" onchange="window.location.href='?kategori='+this.value+'<?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?><?php echo !empty($status_filter) ? '&status='.urlencode($status_filter) : ''; ?>'">
                <option value="">Semua Kategori</option>
                <?php foreach ($kategori_list as $kat): ?>
                <option value="<?php echo urlencode($kat['kategori']); ?>" <?php echo $kategori_filter == $kat['kategori'] ? 'selected' : ''; ?>>
                    <?php echo h($kat['kategori']); ?>
                </option>
                <?php endforeach; ?>
            </select>
            
            <!-- Filter Status -->
            <select class="filter-select" onchange="window.location.href='?status='+this.value+'<?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?><?php echo !empty($kategori_filter) ? '&kategori='.urlencode($kategori_filter) : ''; ?>'">
                <option value="">Semua Status</option>
                <option value="published" <?php echo $status_filter == 'published' ? 'selected' : ''; ?>>Published</option>
                <option value="draft" <?php echo $status_filter == 'draft' ? 'selected' : ''; ?>>Draft</option>
            </select>
            
            <?php if (!empty($search) || !empty($kategori_filter) || !empty($status_filter)): ?>
            <a href="index.php" class="filter-clear"><i class="fa-solid fa-times"></i> Reset</a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Result Info -->
    <div class="result-info">
        Menampilkan <?php echo count($berita_list); ?> dari <?php echo $total_data; ?> data
        <?php if (!empty($search)): ?> untuk "<strong><?php echo h($search); ?></strong>"<?php endif; ?>
    </div>
    
    <!-- Table -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <i class="fa-solid fa-list" style="color: #c8903e;"></i>
                <span>Daftar Berita</span>
            </div>
            <span class="badge badge-info"><?php echo $total_data; ?> Data</span>
        </div>
        <div class="admin-card-body" style="padding: 0;">
            <?php if (!empty($berita_list)): ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="8%">Gambar</th>
                            <th width="35%">Judul</th>
                            <th width="12%">Tanggal</th>
                            <th width="12%">Kategori</th>
                            <th width="10%">Status</th>
                            <th width="18%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($berita_list as $i => $b): ?>
                        <tr>
                            <td><?php echo ($page - 1) * $limit + $i + 1; ?></td>
                            <td>
                                <?php if ($b['gambar']): ?>
                                <img src="../../uploads/berita/<?php echo h($b['gambar']); ?>" alt="Thumb" class="table-thumb">
                                <?php else: ?>
                                <div class="table-thumb-placeholder"><i class="fa-solid fa-newspaper"></i></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo h($b['judul']); ?></strong>
                                <br><small style="color: var(--gray-400);"><?php echo substr(strip_tags($b['isi']), 0, 60); ?>...</small>
                            </td>
                            <td style="color: var(--gray-500);"><?php echo date('d/m/Y', strtotime($b['tanggal'])); ?></td>
                            <td><span class="badge badge-info"><?php echo h($b['kategori'] ?? 'Umum'); ?></span></td>
                            <td>
                                <?php if ($b['status'] == 'published'): ?>
                                <span class="badge badge-success">Published</span>
                                <?php else: ?>
                                <span class="badge badge-warning">Draft</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="../../detail-berita.php?id=<?php echo $b['id']; ?>" target="_blank" class="btn-action btn-view" title="Lihat"><i class="fa-solid fa-eye"></i></a>
                                    <a href="edit.php?id=<?php echo $b['id']; ?>" class="btn-action btn-edit" title="Edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="hapus.php?id=<?php echo $b['id']; ?>" class="btn-action btn-delete" title="Hapus" onclick="return confirm('Yakin hapus?')"><i class="fa-solid fa-trash"></i></a>
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
                $query_string = '';
                if (!empty($search)) $query_string .= '&search=' . urlencode($search);
                if (!empty($kategori_filter)) $query_string .= '&kategori=' . urlencode($kategori_filter);
                if (!empty($status_filter)) $query_string .= '&status=' . urlencode($status_filter);
                ?>
                
                <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page - 1 . $query_string; ?>" class="page-btn"><i class="fa-solid fa-chevron-left"></i></a>
                <?php endif; ?>
                
                <?php
                $start = max(1, $page - 2);
                $end = min($total_pages, $page + 2);
                if ($start > 1): ?>
                <a href="?page=1<?php echo $query_string; ?>" class="page-btn">1</a>
                <?php if ($start > 2): ?><span class="page-dots">...</span><?php endif; ?>
                <?php endif; ?>
                
                <?php for ($i = $start; $i <= $end; $i++): ?>
                <a href="?page=<?php echo $i . $query_string; ?>" class="page-btn <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                
                <?php if ($end < $total_pages): ?>
                <?php if ($end < $total_pages - 1): ?><span class="page-dots">...</span><?php endif; ?>
                <a href="?page=<?php echo $total_pages . $query_string; ?>" class="page-btn"><?php echo $total_pages; ?></a>
                <?php endif; ?>
                
                <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page + 1 . $query_string; ?>" class="page-btn"><i class="fa-solid fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <?php else: ?>
            <div class="empty-state">
                <i class="fa-solid fa-newspaper"></i>
                <p><?php echo !empty($search) ? 'Tidak ada berita untuk "' . h($search) . '"' : 'Belum ada berita'; ?></p>
                <?php if (!empty($search) || !empty($kategori_filter) || !empty($status_filter)): ?>
                <a href="index.php" class="btn-top-bar" style="margin-top: 12px; display: inline-flex;">Reset Filter</a>
                <?php else: ?>
                <a href="tambah.php" class="btn-top-bar" style="margin-top: 12px; display: inline-flex; background: #c8903e;">Tambah Berita</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
</main>

<?php require_once '../includes/footer-admin.php'; ?>