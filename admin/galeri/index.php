<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$current_admin_page = 'galeri';
$page_title = 'Manajemen Galeri';
$base_path = '../../';

require_once '../includes/header-admin.php';

$pdo = getConnection();

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Filter kategori
$kategori_filter = $_GET['kategori'] ?? '';
$where = '';
$params = [];

if (!empty($kategori_filter)) {
    $where = "WHERE kategori = ?";
    $params[] = $kategori_filter;
}

// Total
$stmt = $pdo->prepare("SELECT COUNT(*) FROM galeri $where");
$stmt->execute($params);
$total = $stmt->fetchColumn();
$total_pages = ceil($total / $limit);

// Data
$query_params = array_merge($params, [$limit, $offset]);
$stmt = $pdo->prepare("SELECT * FROM galeri $where ORDER BY created_at DESC LIMIT ? OFFSET ?");
$stmt->execute($query_params);
$galeri_list = $stmt->fetchAll();

// Kategori
$stmt = $pdo->query("SELECT DISTINCT kategori FROM galeri WHERE kategori IS NOT NULL AND kategori != '' ORDER BY kategori");
$kategori_list = $stmt->fetchAll();

require_once '../includes/sidebar.php';
?>

<main class="main-content">
    
    <!-- Top Bar -->
    <div class="top-bar">
        <div>
            <h1><i class="fa-solid fa-images me-2" style="color: #059669;"></i>Manajemen Galeri</h1>
            <p style="color: var(--gray-500); font-size: 0.9rem; margin: 4px 0 0;">Total: <strong><?php echo $total; ?></strong> foto</p>
        </div>
        <div class="top-bar-actions">
            <a href="tambah.php" class="btn-top-bar" style="background: #059669;">
                <i class="fa-solid fa-plus me-1"></i> Upload Foto
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
    
    <!-- Filter Kategori -->
    <?php if (!empty($kategori_list)): ?>
    <div class="filter-bar">
        <a href="index.php" class="filter-btn <?php echo empty($kategori_filter) ? 'active' : ''; ?>">Semua</a>
        <?php foreach ($kategori_list as $kat): ?>
        <a href="?kategori=<?php echo urlencode($kat['kategori']); ?>" 
           class="filter-btn <?php echo $kategori_filter == $kat['kategori'] ? 'active' : ''; ?>">
            <?php echo h($kat['kategori']); ?>
        </a>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    
    <!-- Gallery Grid -->
    <?php if (!empty($galeri_list)): ?>
    <div class="gallery-grid">
        <?php foreach ($galeri_list as $foto): ?>
        <div class="gallery-card">
            <div class="gallery-card-image">
                <img src="../../uploads/galeri/<?php echo h($foto['gambar']); ?>" alt="<?php echo h($foto['deskripsi'] ?? 'Foto'); ?>" 
                     onclick="previewImage('../../uploads/galeri/<?php echo h($foto['gambar']); ?>', '<?php echo h($foto['deskripsi'] ?? ''); ?>')">
            </div>
            <div class="gallery-card-info">
                <p class="gallery-card-desc"><?php echo h(substr($foto['deskripsi'] ?? 'Tanpa deskripsi', 0, 40)); ?></p>
                <?php if ($foto['kategori']): ?>
                <span class="badge badge-info" style="font-size: 0.7rem;"><?php echo h($foto['kategori']); ?></span>
                <?php endif; ?>
                <div class="gallery-card-actions">
                    <a href="edit.php?id=<?php echo $foto['id']; ?>" class="btn-action btn-edit" title="Edit">
                        <i class="fa-solid fa-pen-to-square"></i>
                    </a>
                    <a href="hapus.php?id=<?php echo $foto['id']; ?>" class="btn-action btn-delete" title="Hapus" 
                       onclick="return confirm('Yakin hapus foto ini?')">
                        <i class="fa-solid fa-trash"></i>
                    </a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Pagination -->
    <?php if ($total_pages > 1): ?>
    <div class="pagination-bar">
        <?php 
        $query_string = !empty($kategori_filter) ? '&kategori=' . urlencode($kategori_filter) : '';
        for ($i = 1; $i <= $total_pages; $i++): 
        ?>
        <a href="?page=<?php echo $i . $query_string; ?>" 
           class="page-btn <?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
        <?php endfor; ?>
    </div>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="admin-card">
        <div class="admin-card-body">
            <div class="empty-state">
                <i class="fa-solid fa-images"></i>
                <p>Belum ada foto di galeri</p>
                <a href="tambah.php" class="btn-top-bar" style="margin-top: 12px; display: inline-flex; background: #059669;">
                    <i class="fa-solid fa-plus me-1"></i> Upload Foto
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>
    
</main>

<!-- Preview Modal -->
<div class="modal-preview" id="previewModal" style="display: none;" onclick="closePreview()">
    <div class="modal-preview-content" onclick="event.stopPropagation()">
        <button class="modal-preview-close" onclick="closePreview()">&times;</button>
        <img src="" id="previewImage" alt="Preview">
        <p id="previewDesc" style="padding: 16px; text-align: center; color: var(--gray-700); font-weight: 500;"></p>
    </div>
</div>

<script>
function previewImage(src, desc) {
    document.getElementById('previewImage').src = src;
    document.getElementById('previewDesc').textContent = desc;
    document.getElementById('previewModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}

function closePreview() {
    document.getElementById('previewModal').style.display = 'none';
    document.body.style.overflow = '';
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closePreview();
});
</script>

<?php require_once '../includes/footer-admin.php'; ?>