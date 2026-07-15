<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$current_admin_page = 'guru';
$page_title = 'Manajemen Guru & Staff';
$base_path = '../../';

require_once '../includes/header-admin.php';

$pdo = getConnection();

// Search
$search = trim($_GET['search'] ?? '');

// Pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

// Build query
$where = "WHERE 1=1";
$params = [];

if (!empty($search)) {
    $where .= " AND (nama LIKE ? OR nip LIKE ? OR mapel LIKE ? OR jabatan LIKE ?)";
    $search_param = "%$search%";
    $params = [$search_param, $search_param, $search_param, $search_param];
}

// Total data
$stmt = $pdo->prepare("SELECT COUNT(*) FROM guru $where");
$stmt->execute($params);
$total_data = $stmt->fetchColumn();
$total_pages = ceil($total_data / $limit);

// Data guru
$query_params = array_merge($params, [$limit, $offset]);
$stmt = $pdo->prepare("SELECT * FROM guru $where ORDER BY nama ASC LIMIT ? OFFSET ?");
$stmt->execute($query_params);
$guru_list = $stmt->fetchAll();

// Total semua
$stmt = $pdo->query("SELECT COUNT(*) FROM guru");
$total_all = $stmt->fetchColumn();

// Statistik
$stmt = $pdo->query("SELECT COUNT(*) FROM guru WHERE jabatan LIKE '%Kepala%' AND jabatan NOT LIKE '%Wakil%' AND jabatan NOT LIKE '%Waka%'");
$kepala = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT COUNT(*) FROM guru WHERE jabatan LIKE '%Waka%' OR jabatan LIKE '%Wakil%'");
$waka = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT COUNT(*) FROM guru WHERE jabatan LIKE '%Kelas%'");
$kelas = $stmt->fetchColumn();
$stmt = $pdo->query("SELECT COUNT(*) FROM guru WHERE jabatan LIKE '%Mapel%' OR mapel IS NOT NULL");
$mapel = $stmt->fetchColumn();

$stmt = $pdo->prepare("SELECT g.*, GROUP_CONCAT(j.nama_jabatan SEPARATOR ', ') as jabatan_list 
                       FROM guru g 
                       LEFT JOIN guru_jabatan gj ON g.id = gj.guru_id 
                       LEFT JOIN jabatan j ON gj.jabatan_id = j.id 
                       $where 
                       GROUP BY g.id 
                       ORDER BY g.nama ASC 
                       LIMIT ? OFFSET ?");
$stmt->execute($query_params);
$guru_list = $stmt->fetchAll();

require_once '../includes/sidebar.php';
?>

<!-- Di bagian tabel, tampilkan jabatan -->
<td>
    <?php if (!empty($g['jabatan_list'])): ?>
        <?php 
        $jabatans = explode(', ', $g['jabatan_list']);
        foreach ($jabatans as $jab): 
            $jab_color = '#2563eb';
            if (stripos($jab, 'Waka') !== false || stripos($jab, 'Wakil') !== false) $jab_color = '#c8903e';
            if (stripos($jab, 'Kelas') !== false) $jab_color = '#059669';
            if (stripos($jab, 'PJOK') !== false || stripos($jab, 'PAI') !== false || stripos($jab, 'Inggris') !== false || stripos($jab, 'Arab') !== false) $jab_color = '#7c3aed';
            if (stripos($jab, 'Staff') !== false || stripos($jab, 'TU') !== false || stripos($jab, 'Pustakawan') !== false || stripos($jab, 'Penjaga') !== false) $jab_color = '#dc2626';
        ?>
        <span class="badge" style="background: <?php echo $jab_color; ?>15; color: <?php echo $jab_color; ?>; font-size: 0.75rem; margin: 2px; display: inline-block;">
            <?php echo h(trim($jab)); ?>
        </span>
        <?php endforeach; ?>
    <?php else: ?>
    <?php endif; ?>
</td>

<main class="main-content">
    
    <!-- Top Bar -->
    <div class="top-bar">
        <div>
            <h1><i class="fa-solid fa-chalkboard-user me-2" style="color: #2563eb;"></i>Manajemen Guru & Staff</h1>
            <p style="color: var(--gray-500); font-size: 0.9rem; margin: 4px 0 0;">
                Total: <strong><?php echo $total_all; ?></strong> guru | 
                <span style="color: #2563eb;">Kepala: <?php echo $kepala; ?></span> | 
                <span style="color: #c8903e;">Waka: <?php echo $waka; ?></span> | 
                <span style="color: #059669;">Kelas: <?php echo $kelas; ?></span> | 
                <span style="color: #7c3aed;">Mapel: <?php echo $mapel; ?></span>
            </p>
        </div>
        <div class="top-bar-actions">
            <a href="tambah.php" class="btn-top-bar" style="background: #2563eb;">
                <i class="fa-solid fa-plus me-1"></i> Tambah Guru
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
    
    <!-- Search -->
    <div class="search-filter-bar">
        <form method="GET" class="search-form">
            <div class="search-input-group">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" name="search" value="<?php echo h($search); ?>" placeholder="Cari nama, NIP, mapel, atau jabatan...">
                <button type="submit">Cari</button>
            </div>
        </form>
        <?php if (!empty($search)): ?>
        <a href="index.php" class="filter-clear"><i class="fa-solid fa-times"></i> Reset</a>
        <?php endif; ?>
    </div>
    
    <!-- Result Info -->
    <?php if (!empty($search)): ?>
    <div class="result-info">
        Menampilkan <strong><?php echo count($guru_list); ?></strong> dari <strong><?php echo $total_data; ?></strong> data untuk "<strong><?php echo h($search); ?></strong>"
    </div>
    <?php endif; ?>
    
    <!-- Table Card -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <i class="fa-solid fa-list" style="color: #2563eb;"></i>
                <span>Daftar Guru & Staff</span>
            </div>
            <span class="badge badge-info"><?php echo $total_data; ?> Data</span>
        </div>
        <div class="admin-card-body" style="padding: 0;">
            <?php if (!empty($guru_list)): ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="8%">Foto</th>
                            <th width="25%">Nama</th>
                            <th width="15%">NIP</th>
                            <th width="17%">Mata Pelajaran</th>
                            <th width="15%">Jabatan</th>
                            <th width="15%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($guru_list as $i => $g): ?>
                        <tr>
                            <td><?php echo ($page - 1) * $limit + $i + 1; ?></td>
                            <td>
                                <?php if ($g['foto']): ?>
                                <img src="../../uploads/guru/<?php echo h($g['foto']); ?>" alt="<?php echo h($g['nama']); ?>" class="table-avatar">
                                <?php else: ?>
                                <div class="table-avatar-placeholder" style="background: #eff6ff; color: #2563eb;">
                                    <i class="fa-solid fa-user"></i>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo h($g['nama']); ?></strong></td>
                           <td style="color: var(--gray-500); font-size: 0.88rem;">
    <?php if (!empty($g['nip'])): ?>
        <?php echo h($g['nip']); ?>
    <?php else: ?>
        <span style="color: #94a3b8; font-style: italic;">Belum ada NIP</span>
    <?php endif; ?>
</td>
                            <td><?php echo h($g['mapel'] ?: '-'); ?></td>
                            <td>
                                <?php if ($g['jabatan']): ?>
                                <?php 
                                $jab_color = '#2563eb';
                                if (stripos($g['jabatan'], 'Waka') !== false || stripos($g['jabatan'], 'Wakil') !== false) $jab_color = '#c8903e';
                                if (stripos($g['jabatan'], 'Kelas') !== false) $jab_color = '#059669';
                                if (stripos($g['jabatan'], 'Mapel') !== false) $jab_color = '#7c3aed';
                                if (stripos($g['jabatan'], 'Staff') !== false || stripos($g['jabatan'], 'TU') !== false) $jab_color = '#dc2626';
                                ?>
                                <span class="badge" style="background: <?php echo $jab_color; ?>15; color: <?php echo $jab_color; ?>; font-size: 0.78rem;">
                                    <?php echo h($g['jabatan']); ?>
                                </span>
                                <?php else: ?>
                                <span style="color: var(--gray-400);"> <i class="fa-solid fa-user-times"></i> </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="edit.php?id=<?php echo $g['id']; ?>" class="btn-action btn-edit" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="hapus.php?id=<?php echo $g['id']; ?>" class="btn-action btn-delete" title="Hapus" 
                                       onclick="return confirm('Yakin hapus guru ini?')">
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
                $qs = !empty($search) ? '&search=' . urlencode($search) : '';
                
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
                <i class="fa-solid fa-users"></i>
                <p>
                    <?php if (!empty($search)): ?>
                        Tidak ada guru untuk "<strong><?php echo h($search); ?></strong>"
                    <?php else: ?>
                        Belum ada data guru
                    <?php endif; ?>
                </p>
                <?php if (!empty($search)): ?>
                <a href="index.php" class="btn-top-bar" style="margin-top: 12px; display: inline-flex; background: var(--gray-500);">
                    <i class="fa-solid fa-times me-1"></i> Reset
                </a>
                <?php else: ?>
                <a href="tambah.php" class="btn-top-bar" style="margin-top: 12px; display: inline-flex; background: #2563eb;">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Guru
                </a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
</main>

<?php require_once '../includes/footer-admin.php'; ?>