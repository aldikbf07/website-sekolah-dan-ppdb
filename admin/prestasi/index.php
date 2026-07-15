<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$current_admin_page = 'prestasi';
$page_title = 'Manajemen Prestasi';
$base_path = '../../';

require_once '../includes/header-admin.php';

$pdo = getConnection();

// Search & Filter
$search = trim($_GET['search'] ?? '');
$kategori_filter = trim($_GET['kategori'] ?? '');

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Build query
$where = "WHERE 1=1";
$params = [];

if (!empty($search)) {
    $where .= " AND (judul LIKE ? OR deskripsi LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($kategori_filter)) {
    $where .= " AND kategori = ?";
    $params[] = $kategori_filter;
}

// Total
$stmt = $pdo->prepare("SELECT COUNT(*) FROM prestasi $where");
$stmt->execute($params);
$total_data = $stmt->fetchColumn();
$total_pages = ceil($total_data / $limit);

// Data
$query_params = array_merge($params, [$limit, $offset]);
$stmt = $pdo->prepare("SELECT * FROM prestasi $where ORDER BY tanggal DESC LIMIT ? OFFSET ?");
$stmt->execute($query_params);
$prestasi_list = $stmt->fetchAll();

// Statistik
$stmt = $pdo->query("SELECT COUNT(*) FROM prestasi");
$total_all = $stmt->fetchColumn();

// Kategori
$stmt = $pdo->query("SELECT DISTINCT kategori FROM prestasi WHERE kategori IS NOT NULL ORDER BY kategori");
$kategori_list = $stmt->fetchAll();

require_once '../includes/sidebar.php';
?>

<main class="main-content">
    
    <div class="top-bar">
        <div>
            <h1><i class="fa-solid fa-trophy me-2" style="color: #dc2626;"></i>Manajemen Prestasi</h1>
            <p style="color: var(--gray-500); font-size: 0.9rem;">Total: <strong><?php echo $total_all; ?></strong> prestasi</p>
        </div>
        <div class="top-bar-actions">
            <a href="tambah.php" class="btn-top-bar" style="background: #dc2626;">
                <i class="fa-solid fa-plus me-1"></i> Tambah Prestasi
            </a>
        </div>
    </div>
    
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
                <input type="text" name="search" value="<?php echo h($search); ?>" placeholder="Cari judul atau deskripsi prestasi...">
                <?php if (!empty($kategori_filter)): ?><input type="hidden" name="kategori" value="<?php echo h($kategori_filter); ?>"><?php endif; ?>
                <button type="submit">Cari</button>
            </div>
        </form>
        
        <div class="filter-buttons">
            <select class="filter-select" onchange="window.location.href='?kategori='+this.value+'<?php echo !empty($search) ? '&search='.urlencode($search) : ''; ?>'">
                <option value="">Semua Kategori</option>
                <?php foreach ($kategori_list as $kat): ?>
                <option value="<?php echo urlencode($kat['kategori']); ?>" <?php echo $kategori_filter == $kat['kategori'] ? 'selected' : ''; ?>><?php echo h($kat['kategori']); ?></option>
                <?php endforeach; ?>
            </select>
            <?php if (!empty($search) || !empty($kategori_filter)): ?>
            <a href="index.php" class="filter-clear"><i class="fa-solid fa-times"></i> Reset</a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="result-info">
        Menampilkan <?php echo count($prestasi_list); ?> dari <?php echo $total_data; ?> data
        <?php if (!empty($search)): ?> untuk "<strong><?php echo h($search); ?></strong>"<?php endif; ?>
    </div>
    
    <!-- Table -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div><i class="fa-solid fa-list" style="color: #dc2626;"></i><span>Daftar Prestasi</span></div>
            <span class="badge badge-info"><?php echo $total_data; ?> Data</span>
        </div>
        <div class="admin-card-body" style="padding: 0;">
            <?php if (!empty($prestasi_list)): ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="35%">Judul</th>
                            <th width="25%">Deskripsi</th>
                            <th width="12%">Tanggal</th>
                            <th width="10%">Kategori</th>
                            <th width="13%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($prestasi_list as $i => $p): ?>
                        <tr>
                            <td><?php echo ($page - 1) * $limit + $i + 1; ?></td>
                            <td><strong><?php echo h($p['judul']); ?></strong></td>
                            <td><small style="color: var(--gray-500);"><?php echo h(substr($p['deskripsi'] ?? '', 0, 80)); ?>...</small></td>
                            <td style="color: var(--gray-500);"><?php echo $p['tanggal'] ? date('d/m/Y', strtotime($p['tanggal'])) : '-'; ?></td>
                            <td><span class="badge badge-info"><?php echo h($p['kategori'] ?? 'Umum'); ?></span></td>
                            <td>
                                <div class="action-btns">
                                    <a href="edit.php?id=<?php echo $p['id']; ?>" class="btn-action btn-edit"><i class="fa-solid fa-pen-to-square"></i></a>
                                    <a href="hapus.php?id=<?php echo $p['id']; ?>" class="btn-action btn-delete" onclick="return confirm('Yakin hapus?')"><i class="fa-solid fa-trash"></i></a>
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
                <?php $qs = '';
                if (!empty($search)) $qs .= '&search='.urlencode($search);
                if (!empty($kategori_filter)) $qs .= '&kategori='.urlencode($kategori_filter);
                ?>
                <?php if ($page > 1): ?>
                <a href="?page=<?php echo $page-1 . $qs; ?>" class="page-btn"><i class="fa-solid fa-chevron-left"></i></a>
                <?php endif; ?>
                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?page=<?php echo $i . $qs; ?>" class="page-btn <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                <?php endfor; ?>
                <?php if ($page < $total_pages): ?>
                <a href="?page=<?php echo $page+1 . $qs; ?>" class="page-btn"><i class="fa-solid fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
            
            <?php else: ?>
            <div class="empty-state">
                <i class="fa-solid fa-trophy"></i>
                <p><?php echo !empty($search) ? 'Tidak ada prestasi untuk "' . h($search) . '"' : 'Belum ada prestasi'; ?></p>
                <?php if (!empty($search) || !empty($kategori_filter)): ?>
                <a href="index.php" class="btn-top-bar" style="margin-top: 12px; display: inline-flex;">Reset Filter</a>
                <?php else: ?>
                <a href="tambah.php" class="btn-top-bar" style="margin-top: 12px; display: inline-flex; background: #dc2626;">Tambah Prestasi</a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
</main>

<?php require_once '../includes/footer-admin.php'; ?>