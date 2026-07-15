<?php
$page_title = 'Berita';
$current_page = 'berita';
require_once 'config/database.php';
require_once 'includes/header.php';

try {
    $pdo = getConnection();
    
    // Pagination
    $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
    $limit = 6;
    $offset = ($page - 1) * $limit;
    
    // Filter
    $search = trim($_GET['search'] ?? '');
    $kategori_filter = trim($_GET['kategori'] ?? '');
    
    $where = "WHERE status = 'published'";
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
    
    // Total berita
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM berita $where");
    $stmt->execute($params);
    $total = $stmt->fetchColumn();
    $total_pages = ceil($total / $limit);
    
    // Data berita
    $query_params = array_merge($params, [$limit, $offset]);
    $stmt = $pdo->prepare("SELECT id, judul, isi, gambar, tanggal, kategori, created_at FROM berita $where ORDER BY tanggal DESC LIMIT ? OFFSET ?");
    $stmt->execute($query_params);
    $berita_list = $stmt->fetchAll();
    
    // Berita terbaru untuk sidebar
    $stmt = $pdo->query("SELECT id, judul, gambar, tanggal, kategori FROM berita WHERE status = 'published' ORDER BY tanggal DESC LIMIT 5");
    $recent_posts = $stmt->fetchAll();
    
    // Kategori untuk filter
    $stmt = $pdo->query("SELECT DISTINCT kategori, COUNT(*) as jumlah FROM berita WHERE status = 'published' AND kategori IS NOT NULL AND kategori != '' GROUP BY kategori ORDER BY kategori");
    $kategori_list = $stmt->fetchAll();
    
    // Berita populer (random)
    $stmt = $pdo->query("SELECT id, judul, tanggal FROM berita WHERE status = 'published' ORDER BY RAND() LIMIT 4");
    $popular = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $berita_list = [];
    $recent_posts = [];
    $kategori_list = [];
    $popular = [];
    $total = 0;
    $total_pages = 0;
}

// Kategori colors
$kategori_colors = [
    'Akademik' => '#2563eb',
    'Keagamaan' => '#059669',
    'Olahraga' => '#dc2626',
    'Seni' => '#7c3aed',
    'Umum' => '#c8903e',
    'Pengumuman' => '#0284c7',
    'Prestasi' => '#d97706',
];
?>

<!-- Page Header -->
<section class="page-header-modern page-header-berita">
    <div class="page-header-dots"></div>
    <div class="page-header-grid"></div>
    <div class="container">
        <div class="header-breadcrumb" data-aos="fade-down">
            <a href="index.php"> Beranda</a>
            <span class="separator">›</span>
            <span>Berita</span>
        </div>
        <div class="header-icon" data-aos="zoom-in" data-aos-delay="100">
            <i class="fa-solid fa-newspaper"></i>
        </div>
        <h1 class="header-title" data-aos="fade-up" data-aos-delay="200">Berita & Kegiatan</h1>
        <p class="header-desc" data-aos="fade-up" data-aos-delay="300">Informasi terkini seputar MI Muhammadiyah Bojongsana</p>
    </div>
</section>

<!-- Search & Filter -->
<section class="section-modern section-gray py-3">
    <div class="container">
        <div class="row align-items-center g-3">
            <div class="col-lg-10">
                <form action="berita.php" method="GET" class="d-flex gap-2">
                    <?php if (!empty($kategori_filter)): ?>
                    <input type="hidden" name="kategori" value="<?php echo h($kategori_filter); ?>">
                    <?php endif; ?>
                    <div class="input-group" style="box-shadow: var(--shadow-sm); border-radius: 12px; overflow: hidden;">
                        <span class="input-group-text" style="background: white; border: 1px solid var(--gray-300); border-right: none;">
                            <i class="fa-solid fa-magnifying-glass" style="color: var(--gray-400);"></i>
                        </span>
                        <input type="text" name="search" class="form-control" placeholder="Cari berita..." 
                               value="<?php echo h($search); ?>" 
                               style="border: 1px solid var(--gray-300); border-left: none; padding: 10px 16px;">
                        <button class="btn-modern btn-primary-modern" type="submit" style="border-radius: 0 12px 12px 0;">Cari</button>
                    </div>
                </form>
            </div>
            </div>
            <div class="row align-items-center g-3 mt-2 mt-lg-0">
            <div class="col-lg-10">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="berita.php<?php echo !empty($search) ? '?search=' . urlencode($search) : ''; ?>" 
                       class="badge-modern" style="background:<?php echo empty($kategori_filter) ? 'var(--primary)' : 'var(--gray-200)'; ?>; color:<?php echo empty($kategori_filter) ? 'white' : 'var(--gray-600)'; ?>; text-decoration: none; padding: 8px 16px;">
                        Semua
                    </a>
                    <?php foreach ($kategori_list as $kat): 
                        $kat_color = $kategori_colors[$kat['kategori']] ?? '#64748b';
                        $is_active = ($kategori_filter == $kat['kategori']);
                    ?>
                    <a href="?kategori=<?php echo urlencode($kat['kategori']); ?><?php echo !empty($search) ? '&search=' . urlencode($search) : ''; ?>" 
                       class="badge-modern" style="background:<?php echo $is_active ? $kat_color : 'var(--gray-200)'; ?>; color:<?php echo $is_active ? 'white' : 'var(--gray-600)'; ?>; text-decoration: none; padding: 8px 16px;">
                        <?php echo h($kat['kategori']); ?>
                        <small style="opacity:0.7;">(<?php echo $kat['jumlah']; ?>)</small>
                    </a>
                    <?php endforeach; ?>
                    <?php if (!empty($search) || !empty($kategori_filter)): ?>
                    <a href="berita.php" class="badge-modern" style="background: var(--gray-300); color: var(--gray-600); text-decoration: none; padding: 8px 16px;">
                        <i class="fa-solid fa-times me-1"></i> Reset
                    </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <?php if (!empty($search) || !empty($kategori_filter)): ?>
        <div class="mt-3 p-3" style="background: var(--primary-light); border-radius: 12px; font-weight: 500; font-size: 0.9rem; color: var(--primary);">
            <i class="fa-solid fa-circle-info me-2"></i>
            Ditemukan <strong><?php echo $total; ?></strong> berita
            <?php if (!empty($search)): ?> untuk "<strong><?php echo h($search); ?></strong>"<?php endif; ?>
            <?php if (!empty($kategori_filter)): ?> dalam kategori <strong><?php echo h($kategori_filter); ?></strong><?php endif; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Content -->
<section class="section-modern section-white">
    <div class="container">
        <div class="row g-4">
            
            <!-- Main Content -->
            <div class="col-lg-8">
                <?php if (!empty($berita_list)): ?>
                <div class="row g-4">
                    <?php foreach ($berita_list as $index => $berita): 
                        $kat_color = $kategori_colors[$berita['kategori']] ?? '#64748b';
                    ?>
                    <div class="col-md-6" data-aos="fade-up" data-aos-delay="<?php echo ($index % 2) * 100; ?>">
                        <div class="card-modern h-100">
                            <!-- Gambar -->
                            <div style="position: relative; height: 180px; overflow: hidden;">
                                <?php if ($berita['gambar']): ?>
                                <img src="uploads/berita/<?php echo h($berita['gambar']); ?>" 
                                     alt="<?php echo h($berita['judul']); ?>" 
                                     style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease;"
                                     onmouseover="this.style.transform='scale(1.05)'"
                                     onmouseout="this.style.transform='scale(1)'"
                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div style="display: none; width: 100%; height: 100%; background: var(--gray-100); align-items: center; justify-content: center; font-size: 2.5rem; color: var(--gray-300);">
                                    <i class="fa-solid fa-newspaper"></i>
                                </div>
                                <?php else: ?>
                                <div style="width: 100%; height: 100%; background: var(--gray-100); display: flex; align-items: center; justify-content: center; font-size: 2.5rem; color: var(--gray-300);">
                                    <i class="fa-solid fa-newspaper"></i>
                                </div>
                                <?php endif; ?>
                                <!-- Badge Kategori -->
                                <span class="badge-modern" style="position: absolute; top: 12px; left: 12px; background: <?php echo $kat_color; ?>; color: white; font-size: 0.72rem; padding: 4px 10px;">
                                    <?php echo h($berita['kategori'] ?? 'Umum'); ?>
                                </span>
                            </div>
                            
                            <!-- Konten -->
                            <div class="card-body d-flex flex-column" style="padding: 16px;">
                                <div style="display: flex; align-items: center; gap: 6px; margin-bottom: 8px;">
                                    <i class="fa-regular fa-calendar" style="color: var(--gray-400); font-size: 0.8rem;"></i>
                                    <small style="color: var(--gray-400); font-size: 0.8rem;"><?php echo formatTanggal($berita['tanggal']); ?></small>
                                </div>
                                
                                <h5 style="font-size: 0.95rem; font-weight: 600; margin-bottom: 6px; line-height: 1.4; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                    <a href="detail-berita.php?id=<?php echo $berita['id']; ?>" style="color: var(--gray-900); text-decoration: none;">
                                        <?php echo h($berita['judul']); ?>
                                    </a>
                                </h5>
                                
                                <p style="color: var(--gray-500); font-size: 0.85rem; line-height: 1.5; margin-bottom: 10px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; flex: 1;">
                                    <?php echo substr(strip_tags($berita['isi']), 0, 100); ?>...
                                </p>
                                
                                <a href="detail-berita.php?id=<?php echo $berita['id']; ?>" 
                                   style="display: inline-flex; align-items: center; gap: 4px; color: <?php echo $kat_color; ?>; font-weight: 600; font-size: 0.85rem; text-decoration: none; margin-top: auto;">
                                    Baca Selengkapnya <i class="fa-solid fa-arrow-right" style="font-size: 0.7rem;"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="d-flex justify-content-center gap-2 mt-5">
                    <?php 
                    $page_url = '?';
                    if (!empty($search)) $page_url .= 'search=' . urlencode($search) . '&';
                    if (!empty($kategori_filter)) $page_url .= 'kategori=' . urlencode($kategori_filter) . '&';
                    
                    if ($page > 1): ?>
                    <a href="<?php echo $page_url . 'page=' . ($page - 1); ?>" class="btn btn-sm" style="background: var(--gray-100); color: var(--gray-600); border-radius: 8px; padding: 8px 14px; text-decoration: none;">
                        <i class="fa-solid fa-chevron-left"></i>
                    </a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <a href="<?php echo $page_url . 'page=' . $i; ?>" 
                       class="btn btn-sm" 
                       style="background: <?php echo $i == $page ? 'var(--primary)' : 'var(--gray-100)'; ?>; 
                              color: <?php echo $i == $page ? 'white' : 'var(--gray-600)'; ?>; 
                              border-radius: 8px; padding: 8px 14px; text-decoration: none; min-width: 40px; text-align: center;">
                        <?php echo $i; ?>
                    </a>
                    <?php endfor; ?>
                    
                    <?php if ($page < $total_pages): ?>
                    <a href="<?php echo $page_url . 'page=' . ($page + 1); ?>" class="btn btn-sm" style="background: var(--gray-100); color: var(--gray-600); border-radius: 8px; padding: 8px 14px; text-decoration: none;">
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
                
                <?php else: ?>
                <div class="text-center py-5">
                    <i class="fa-solid fa-newspaper fa-3x mb-3" style="color: var(--gray-300);"></i>
                    <h4 style="color: var(--gray-400);">
                        <?php echo !empty($search) ? 'Tidak ada berita untuk "' . h($search) . '"' : 'Belum ada berita'; ?>
                    </h4>
                    <?php if (!empty($search)): ?>
                    <a href="berita.php" class="btn-modern btn-outline-modern mt-3">Lihat Semua Berita</a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
  <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sidebar-berita">
                    
                    <!-- PPDB Banner -->
                    <div class="sidebar-ppdb-banner">
                        <div class="ppdb-icon"><img src="assets/images/icon/backpack.png" alt="" style="width: 40px; height: 40px;"></div>
                        <h5>PPDB 2026/2027</h5>
                        <p>Segera daftarkan putra-putri Anda!</p>
                        <a href="ppdb.php" class="btn-modern btn-white-modern btn-sm" style="padding: 6px 14px; font-size: 0.8rem;">
                            Daftar Sekarang →
                        </a>
                    </div>
                    
                    <!-- Berita Terbaru -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-body">
                            <h5 class="sidebar-card-title">
                                <i class="fa-solid fa-clock"></i> Berita Terbaru
                            </h5>
                            
                            <?php if (!empty($recent_posts)): ?>
                            <div class="d-flex flex-column" style="gap: 2px;">
                                <?php foreach ($recent_posts as $recent): ?>
                                <a href="detail-berita.php?id=<?php echo $recent['id']; ?>" class="recent-post-item">
                                    <?php if ($recent['gambar']): ?>
                                    <img src="uploads/berita/<?php echo h($recent['gambar']); ?>" 
                                         alt="<?php echo h($recent['judul']); ?>" 
                                         class="recent-post-thumb"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="recent-post-thumb-placeholder" style="display: none;">
                                        <i class="fa-solid fa-newspaper"></i>
                                    </div>
                                    <?php else: ?>
                                    <div class="recent-post-thumb-placeholder">
                                        <i class="fa-solid fa-newspaper"></i>
                                    </div>
                                    <?php endif; ?>
                                    <div class="recent-post-info">
                                        <p class="post-title"><?php echo h($recent['judul']); ?></p>
                                        <span class="post-date">
                                            <i class="fa-regular fa-calendar me-1"></i> <?php echo formatTanggal($recent['tanggal']); ?>
                                        </span>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <p style="color: var(--gray-400); font-size: 0.85rem; text-align: center; padding: 10px 0;">Belum ada berita</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Kategori -->
                    <?php if (!empty($kategori_list)): ?>
                    <div class="sidebar-card">
                        <div class="sidebar-card-body">
                            <h5 class="sidebar-card-title">
                                <i class="fa-solid fa-folder-tree"></i> Kategori
                            </h5>
                            <div class="d-flex flex-column" style="gap: 1px;">
                                <?php foreach ($kategori_list as $kat): 
                                    $k_color = $kategori_colors[$kat['kategori']] ?? '#64748b';
                                ?>
                                <a href="?kategori=<?php echo urlencode($kat['kategori']); ?>" class="kategori-list-item">
                                    <div class="kat-info">
                                        <span class="kat-dot" style="background: <?php echo $k_color; ?>;"></span>
                                        <span class="kat-name"><?php echo h($kat['kategori']); ?></span>
                                    </div>
                                    <span class="kat-count"><?php echo $kat['jumlah']; ?></span>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Populer -->
                    <?php if (!empty($popular)): ?>
                    <div class="sidebar-card">
                        <div class="sidebar-card-body">
                            <h5 class="sidebar-card-title">
                                <i class="fa-solid fa-fire" style="color: #dc2626 !important;"></i> Populer
                            </h5>
                            <div class="d-flex flex-column" style="gap: 1px;">
                                <?php foreach ($popular as $i => $pop): ?>
                                <a href="detail-berita.php?id=<?php echo $pop['id']; ?>" class="popular-item">
                                    <span class="pop-rank" style="color: <?php echo $i < 3 ? '#dc2626' : 'var(--gray-400)'; ?>;">#<?php echo $i + 1; ?></span>
                                    <span class="pop-title"><?php echo h($pop['judul']); ?></span>
                                </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                </div>
            </div>
            
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>