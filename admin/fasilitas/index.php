<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';

$current_admin_page = 'fasilitas';
$page_title = 'Manajemen Fasilitas';
$base_path = '../../';

require_once '../includes/header-admin.php';

$pdo = getConnection();
$stmt = $pdo->query("SELECT * FROM fasilitas ORDER BY id ASC");
$fasilitas_list = $stmt->fetchAll();
$total_fasilitas = count($fasilitas_list);

$dengan_gambar = count(array_filter($fasilitas_list, function($f) { return !empty($f['gambar']); }));

require_once '../includes/sidebar.php';
?>

<main class="main-content">
    
    <!-- Top Bar -->
    <div class="top-bar">
        <div>
            <h1><i class="fa-solid fa-building me-2" style="color: #7c3aed;"></i>Manajemen Fasilitas</h1>
            <p style="color: var(--gray-500); font-size: 0.9rem; margin: 4px 0 0;">
                Total: <strong><?php echo $total_fasilitas; ?></strong> fasilitas | 
                <span style="color: #059669;">Dengan Gambar: <?php echo $dengan_gambar; ?></span>
            </p>
        </div>
        <div class="top-bar-actions">
            <a href="tambah.php" class="btn-top-bar" style="background: #7c3aed;">
                <i class="fa-solid fa-plus me-1"></i> Tambah Fasilitas
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
    
    <!-- Table Card -->
    <div class="admin-card">
        <div class="admin-card-header">
            <div>
                <i class="fa-solid fa-list" style="color: #7c3aed;"></i>
                <span>Daftar Fasilitas</span>
            </div>
            <span class="badge badge-info"><?php echo $total_fasilitas; ?> Data</span>
        </div>
        <div class="admin-card-body" style="padding: 0;">
            <?php if (!empty($fasilitas_list)): ?>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th width="12%">Gambar</th>
                            <th width="25%">Nama Fasilitas</th>
                            <th width="38%">Deskripsi</th>
                            <th width="20%">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fasilitas_list as $i => $f): ?>
                        <tr>
                            <td><?php echo $i + 1; ?></td>
                            <td>
                                <?php if ($f['gambar']): ?>
                                <img src="../../uploads/fasilitas/<?php echo h($f['gambar']); ?>" alt="<?php echo h($f['nama']); ?>" class="table-thumb">
                                <?php else: ?>
                                <div class="table-thumb-placeholder">
                                    <i class="fa-solid fa-building"></i>
                                </div>
                                <?php endif; ?>
                            </td>
                            <td><strong><?php echo h($f['nama']); ?></strong></td>
                            <td><small style="color: var(--gray-500);"><?php echo h(substr($f['deskripsi'] ?? '', 0, 100)); ?>...</small></td>
                            <td>
                                <div class="action-btns">
                                    <a href="edit.php?id=<?php echo $f['id']; ?>" class="btn-action btn-edit" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <a href="hapus.php?id=<?php echo $f['id']; ?>" class="btn-action btn-delete" title="Hapus" onclick="return confirm('Yakin hapus fasilitas ini?')">
                                        <i class="fa-solid fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fa-solid fa-building"></i>
                <p>Belum ada data fasilitas</p>
                <a href="tambah.php" class="btn-top-bar" style="margin-top: 12px; display: inline-flex; background: #7c3aed;">
                    <i class="fa-solid fa-plus me-1"></i> Tambah Fasilitas
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
</main>

<?php require_once '../includes/footer-admin.php'; ?>