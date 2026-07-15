<?php
require_once 'config/database.php';
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) { header('Location: berita.php'); exit(); }

try {
    $pdo = getConnection();
    
    // Ambil detail berita
    $stmt = $pdo->prepare("SELECT * FROM berita WHERE id=? AND status='published'");
    $stmt->execute([$id]);
    $berita = $stmt->fetch();
    if (!$berita) { header('Location: berita.php'); exit(); }
    
    // Berita terkait
    $stmt = $pdo->prepare("SELECT * FROM berita WHERE status='published' AND id!=? ORDER BY RAND() LIMIT 3");
    $stmt->execute([$id]);
    $terkait = $stmt->fetchAll();

    // Berita terbaru untuk sidebar
    $stmt = $pdo->query("SELECT id, judul, gambar, tanggal, kategori FROM berita WHERE status = 'published' ORDER BY tanggal DESC LIMIT 5");
    $recent_posts = $stmt->fetchAll();
    
    // Multi gambar
    $stmt = $pdo->prepare("SELECT * FROM berita_gambar WHERE berita_id = ? ORDER BY urutan ASC");
    $stmt->execute([$id]);
    $gambar_list = $stmt->fetchAll();
    
    // Video
    $stmt = $pdo->prepare("SELECT * FROM berita_video WHERE berita_id = ? ORDER BY urutan ASC");
    $stmt->execute([$id]);
    $video_list = $stmt->fetchAll();
    
    // Kategori populer untuk sidebar
    $stmt = $pdo->query("SELECT DISTINCT kategori, COUNT(*) as jumlah FROM berita WHERE status='published' AND kategori IS NOT NULL GROUP BY kategori ORDER BY jumlah DESC LIMIT 5");
    $kategori_populer = $stmt->fetchAll();
    
    $page_title = $berita['judul'];
    $current_page = 'berita';
} catch (PDOException $e) {
    header('Location: berita.php');
    exit();
}

// Warna kategori
$kategori_colors = [
    'Akademik' => '#2563eb',
    'Keagamaan' => '#059669',
    'Olahraga' => '#dc2626',
    'Seni' => '#7c3aed',
    'Umum' => '#c8903e',
    'Pengumuman' => '#0284c7',
    'Prestasi' => '#d97706',
];
$kat_color = $kategori_colors[$berita['kategori']] ?? '#64748b';

require_once 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header-modern page-header-berita">
    <div class="page-header-dots"></div>
    <div class="page-header-grid"></div>
    <div class="container">
        <div class="header-breadcrumb" data-aos="fade-down">
            <a href="index.php"><i class="fa-solid fa-house me-1"></i> Beranda</a>
            <span class="separator">›</span>
            <a href="berita.php">Berita</a>
            <span class="separator">›</span>
            <span>Detail</span>
        </div>
        <div class="header-icon" data-aos="zoom-in" data-aos-delay="100">
            <i class="fa-solid fa-book-open"></i>
        </div>
        <h1 class="header-title" data-aos="fade-up" data-aos-delay="200"><?php echo h($berita['judul']); ?></h1>
        <p class="header-desc" data-aos="fade-up" data-aos-delay="300">
            <i class="fa-regular fa-calendar me-1"></i> <?php echo formatTanggal($berita['tanggal']); ?> | 
            <?php echo h($berita['kategori'] ?? 'Umum'); ?>
        </p>
    </div>
</section>

<!-- Content -->
<section class="section-modern section-white" style="padding-top: 10px;">
    <div class="container">
        <div class="row g-4">
            
            <!-- Main Content -->
            <div class="col-lg-8">
                <article>
                    
                    <!-- Breadcrumb -->
                    <nav class="mb-3" style="font-size:0.85rem;">
                        <a href="index.php">Beranda</a> / <a href="berita.php">Berita</a> / <span style="color:var(--primary);"><?php echo h($berita['judul']); ?></span>
                    </nav>
                    
                    <!-- Featured Image -->
                    <?php if ($berita['gambar']): ?>
                    <div class="detail-featured-image" data-aos="fade-up">
                        <img src="uploads/berita/<?php echo h($berita['gambar']); ?>" 
                             alt="<?php echo h($berita['judul']); ?>"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="image-fallback" style="display: none;">
                            <i class="fa-solid fa-newspaper fa-3x"></i>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Meta Info -->
                    <div class="d-flex flex-wrap align-items-center gap-3 mb-3" data-aos="fade-up">
                        <span class="badge-modern" style="background: <?php echo $kat_color; ?>; color: white; font-size: 0.8rem; padding: 6px 14px;">
                            <?php echo h($berita['kategori'] ?? 'Umum'); ?>
                        </span>
                        <span style="font-size: 0.85rem; color: var(--gray-400); font-weight: 500;">
                            <i class="fa-regular fa-calendar me-1"></i> <?php echo formatTanggal($berita['tanggal']); ?>
                        </span>
                        <span style="font-size: 0.85rem; color: var(--gray-400); font-weight: 500;">
                            <i class="fa-regular fa-clock me-1"></i> <?php echo date('H:i', strtotime($berita['created_at'])); ?> WIB
                        </span>
                    </div>
                    
                    <!-- Title -->
                    <h1 style="font-size: 1.8rem; font-weight: 700; color: var(--gray-900); margin-bottom: 20px; line-height: 1.3;" data-aos="fade-up">
                        <?php echo h($berita['judul']); ?>
                    </h1>
                    
                    <!-- Share Buttons -->
                    <div class="d-flex align-items-center gap-3 mb-4 pb-4" style="border-bottom: 1px solid var(--gray-200);" data-aos="fade-up">
                        <span style="font-size: 0.85rem; font-weight: 600; color: var(--gray-500);">Bagikan:</span>
                        <a href="https://facebook.com/sharer/sharer.php?u=<?php echo urlencode('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>" 
                           target="_blank" rel="noopener" class="share-btn share-btn-facebook" title="Facebook">
                            <i class="fa-brands fa-facebook-f"></i>
                        </a>
                        <a href="https://wa.me/?text=<?php echo urlencode($berita['judul'].' - https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>" 
                           target="_blank" rel="noopener" class="share-btn share-btn-whatsapp" title="WhatsApp">
                            <i class="fa-brands fa-whatsapp"></i>
                        </a>
                        <a href="https://t.me/share/url?url=<?php echo urlencode('https://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($berita['judul']); ?>" 
                           target="_blank" rel="noopener" class="share-btn share-btn-telegram" title="Telegram">
                            <i class="fa-brands fa-telegram"></i>
                        </a>
                    </div>
                    
                    <!-- Content -->
                    <div class="detail-content" data-aos="fade-up">
                        <?php echo nl2br($berita['isi']); ?>
                    </div>
                    
                    <!-- ============ MULTI GAMBAR ============ -->
                    <?php if (!empty($gambar_list)): ?>
                    <div class="berita-gallery mt-4 pt-4" style="border-top:1px solid var(--gray-200);">
                        <h4 style="font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                            <span style="width: 4px; height: 24px; background: var(--primary); border-radius: 2px; display: inline-block;"></span>
                            <i class="fa-solid fa-images" style="color: var(--primary);"></i> Galeri Foto
                            <span style="font-size: 0.8rem; color: var(--gray-400); font-weight: 500;">(<?php echo count($gambar_list); ?> foto)</span>
                        </h4>
                        <div class="row g-3">
                            <?php foreach ($gambar_list as $gbr): ?>
                            <div class="col-md-4 col-6">
                                <div class="gallery-item-card" 
                                     onclick="openGalleryModal('uploads/berita/<?php echo h($gbr['gambar']); ?>', '<?php echo h($gbr['caption'] ?? ''); ?>')">
                                    <div class="gallery-img-wrapper">
                                        <img src="uploads/berita/<?php echo h($gbr['gambar']); ?>" 
                                             alt="<?php echo h($gbr['caption'] ?? 'Galeri'); ?>" 
                                             loading="lazy">
                                    </div>
                                    <?php if ($gbr['caption']): ?>
                                    <div style="padding:8px 12px;background:white;font-size:0.82rem;font-weight:500;color:var(--gray-600);line-height:1.3;">
                                        <?php echo h($gbr['caption']); ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- ============ VIDEO ============ -->
                    <?php if (!empty($video_list)): ?>
                    <div class="berita-videos mt-4 pt-4" style="border-top:1px solid var(--gray-200);">
                        <h4 style="font-weight: 700; margin-bottom: 16px; display: flex; align-items: center; gap: 10px;">
                            <span style="width: 4px; height: 24px; background: #dc2626; border-radius: 2px; display: inline-block;"></span>
                            <i class="fa-solid fa-video" style="color: #dc2626;"></i> Video
                            <span style="font-size: 0.8rem; color: var(--gray-400); font-weight: 500;">(<?php echo count($video_list); ?> video)</span>
                        </h4>
                        <div class="row g-4">
                            <?php foreach ($video_list as $index => $vid): 
                                $thumbnail = '';
                                $video_id = '';
                                
                                if ($vid['platform'] == 'youtube') {
                                    preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $vid['url'], $matches);
                                    $video_id = $matches[1] ?? '';
                                    if ($video_id) {
                                        $thumbnail = "https://img.youtube.com/vi/{$video_id}/maxresdefault.jpg";
                                    }
                                }
                            ?>
                            <div class="col-md-6">
                                <div class="video-card" id="videoCard<?php echo $index; ?>">
                                    <?php if ($vid['embed_code']): ?>
                                        <!-- Embed -->
                                        <div class="video-embed" id="videoEmbed<?php echo $index; ?>" style="display: none;">
                                            <?php echo $vid['embed_code']; ?>
                                        </div>
                                        <!-- Thumbnail -->
                                        <div class="video-thumbnail" id="videoThumb<?php echo $index; ?>">
                                            <?php if ($thumbnail): ?>
                                                <img src="<?php echo $thumbnail; ?>" alt="<?php echo h($vid['caption'] ?? 'Video'); ?>" loading="lazy"
                                                     onerror="this.src='https://img.youtube.com/vi/<?php echo $video_id; ?>/hqdefault.jpg'; this.onerror=null;">
                                            <?php else: ?>
                                                <div style="position:absolute;top:0;left:0;width:100%;height:100%;background:linear-gradient(135deg,#1a1a2e,#16213e,#0f3460);display:flex;align-items:center;justify-content:center;flex-direction:column;gap:10px;">
                                                    <i class="fa-solid fa-video fa-3x" style="color:rgba(255,255,255,0.3);"></i>
                                                    <span style="color:rgba(255,255,255,0.6);font-weight:600;font-size:0.8rem;"><?php echo strtoupper($vid['platform']); ?> Video</span>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="video-play-overlay" onclick="playVideo(<?php echo $index; ?>, '<?php echo h($vid['url']); ?>')">
                                                <div class="video-play-btn">
                                                    <i class="fa-solid fa-play"></i>
                                                </div>
                                            </div>
                                            
                                            <span class="video-platform-badge platform-<?php echo $vid['platform']; ?>">
                                                <i class="fa-brands fa-<?php echo $vid['platform']; ?> me-1"></i>
                                                <?php echo strtoupper($vid['platform']); ?>
                                            </span>
                                        </div>
                                    <?php else: ?>
                                        <div class="video-thumbnail">
                                            <div style="position:absolute;top:0;left:0;width:100%;height:100%;background:linear-gradient(135deg,#1a1a2e,#16213e);display:flex;align-items:center;justify-content:center;flex-direction:column;gap:12px;">
                                                <i class="fa-solid fa-video fa-3x" style="color:rgba(255,255,255,0.4);"></i>
                                                <a href="<?php echo h($vid['url']); ?>" target="_blank" class="btn-modern btn-white-modern btn-sm">
                                                    <i class="fa-solid fa-external-link-alt me-1"></i> Buka Video
                                                </a>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <?php if ($vid['caption']): ?>
                                    <div class="video-info">
                                        <p class="video-title"><?php echo h($vid['caption']); ?></p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Tags -->
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-4 pt-4" style="border-top:1px solid var(--gray-200);">
                        <span style="font-size: 0.85rem; font-weight: 600; color: var(--gray-500);">Tags:</span>
                        <span class="badge-modern" style="background: var(--gray-100); color: var(--gray-600); font-size: 0.78rem;">MI Muhammadiyah</span>
                        <span class="badge-modern" style="background: var(--gray-100); color: var(--gray-600); font-size: 0.78rem;">Bojongsana</span>
                        <span class="badge-modern" style="background: <?php echo $kat_color; ?>15; color: <?php echo $kat_color; ?>; font-size: 0.78rem;"><?php echo h($berita['kategori'] ?? 'Umum'); ?></span>
                    </div>
                    
                    <!-- Back Button -->
                    <div class="mt-4" data-aos="fade-up">
                        <a href="berita.php" style="display: inline-flex; align-items: center; gap: 8px; color: var(--gray-600); font-weight: 600; font-size: 0.9rem; text-decoration: none; padding: 10px 20px; border: 1px solid var(--gray-300); border-radius: 50px; transition: all 0.2s ease;"
                           onmouseover="this.style.background='var(--gray-50)'; this.style.borderColor='var(--gray-400)';"
                           onmouseout="this.style.background='transparent'; this.style.borderColor='var(--gray-300)';">
                            <i class="fa-solid fa-arrow-left"></i> Kembali ke Berita
                        </a>
                    </div>
                    
                </article>
                
                <!-- Berita Terkait -->
                <?php if (!empty($terkait)): ?>
                <div class="mt-5" data-aos="fade-up">
                    <h4 style="font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                        <span style="width: 4px; height: 24px; background: <?php echo $kat_color; ?>; border-radius: 2px; display: inline-block;"></span>
                        Berita Terkait
                    </h4>
                    <div class="row g-3">
                        <?php foreach ($terkait as $t): 
                            $t_color = $kategori_colors[$t['kategori']] ?? '#64748b';
                        ?>
                        <div class="col-md-4">
                            <a href="detail-berita.php?id=<?php echo $t['id']; ?>" class="related-card">
                                <div class="card-modern" style="overflow: hidden;">
                                    <?php if ($t['gambar']): ?>
                                    <img src="uploads/berita/<?php echo h($t['gambar']); ?>" alt="<?php echo h($t['judul']); ?>" class="related-card-image"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="related-card-image-fallback" style="display: none;"><i class="fa-solid fa-newspaper"></i></div>
                                    <?php else: ?>
                                    <div class="related-card-image-fallback"><i class="fa-solid fa-newspaper"></i></div>
                                    <?php endif; ?>
                                    <div style="padding: 14px;">
                                        <small style="color: var(--gray-400); font-size: 0.75rem;">
                                            <i class="fa-regular fa-calendar me-1"></i> <?php echo formatTanggal($t['tanggal']); ?>
                                        </small>
                                        <h6 style="font-size: 0.88rem; font-weight: 600; color: var(--gray-800); margin-top: 4px; line-height: 1.3; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                            <?php echo h($t['judul']); ?>
                                        </h6>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <div class="sidebar-berita">
                    
                    <!-- PPDB Banner -->
                    <div class="sidebar-ppdb-banner">
                        <div class="ppdb-icon"><img src="assets/images/icon/backpack.png" alt="PPDB" style="width: 40px; height: 40px;"></div>
                        <h5>PPDB 2026/2027</h5>
                        <p>Segera daftarkan putra-putri Anda!</p>
                        <a href="ppdb.php" class="btn-modern btn-white-modern btn-sm" style="padding: 6px 14px; font-size: 0.8rem;">
                            Daftar Sekarang →
                        </a>
                    </div>
                    
                    <!-- Berita Terbaru -->
                    <div class="sidebar-card">
                        <div class="sidebar-card-body">
                            <h5 class="sidebar-card-title"><i class="fa-solid fa-clock"></i> Berita Terbaru</h5>
                            <?php if (!empty($recent_posts)): ?>
                            <div class="d-flex flex-column" style="gap: 2px;">
                                <?php foreach ($recent_posts as $recent): ?>
                                <a href="detail-berita.php?id=<?php echo $recent['id']; ?>" class="recent-post-item">
                                    <?php if ($recent['gambar']): ?>
                                    <img src="uploads/berita/<?php echo h($recent['gambar']); ?>" alt="<?php echo h($recent['judul']); ?>" class="recent-post-thumb"
                                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="recent-post-thumb-placeholder" style="display: none;"><i class="fa-solid fa-newspaper"></i></div>
                                    <?php else: ?>
                                    <div class="recent-post-thumb-placeholder"><i class="fa-solid fa-newspaper"></i></div>
                                    <?php endif; ?>
                                    <div class="recent-post-info">
                                        <p class="post-title"><?php echo h($recent['judul']); ?></p>
                                        <span class="post-date"><i class="fa-regular fa-calendar me-1"></i> <?php echo formatTanggal($recent['tanggal']); ?></span>
                                    </div>
                                </a>
                                <?php endforeach; ?>
                            </div>
                            <?php else: ?>
                            <p style="color: var(--gray-400); font-size: 0.85rem; text-align: center; padding: 10px 0;">Belum ada berita</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Kategori Populer -->
                    <?php if (!empty($kategori_populer)): ?>
                    <div class="sidebar-card">
                        <div class="sidebar-card-body">
                            <h5 class="sidebar-card-title"><i class="fa-solid fa-folder-tree"></i> Kategori Populer</h5>
                            <div class="d-flex flex-column" style="gap: 1px;">
                                <?php foreach ($kategori_populer as $kp): 
                                    $kp_color = $kategori_colors[$kp['kategori']] ?? '#64748b';
                                ?>
                                <a href="berita.php?kategori=<?php echo urlencode($kp['kategori']); ?>" class="kategori-list-item">
                                    <div class="kat-info">
                                        <span class="kat-dot" style="background: <?php echo $kp_color; ?>;"></span>
                                        <span class="kat-name"><?php echo h($kp['kategori']); ?></span>
                                    </div>
                                    <span class="kat-count"><?php echo $kp['jumlah']; ?></span>
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

<!-- Gallery Modal -->
<div id="galleryModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.9);z-index:9999;align-items:center;justify-content:center;padding:20px;" onclick="closeGalleryModal()">
    <div style="max-width:800px;width:100%;" onclick="event.stopPropagation()">
        <button onclick="closeGalleryModal()" style="position:absolute;top:20px;right:20px;background:white;border:none;border-radius:50%;width:40px;height:40px;font-size:1.2rem;cursor:pointer;z-index:10;">✕</button>
        <img src="" id="galleryImage" style="width:100%;max-height:80vh;object-fit:contain;border-radius:12px;">
        <p id="galleryCaption" style="color:white;text-align:center;margin-top:16px;font-weight:500;"></p>
    </div>
</div>

<style>
/* Featured Image */
.detail-featured-image { position:relative; width:100%; overflow:hidden; border-radius:16px; margin-bottom:24px; background:var(--gray-100); }
.detail-featured-image img { width:100%; height:auto; display:block; border-radius:16px; object-fit:contain; }
.detail-featured-image .image-fallback { width:100%; min-height:250px; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,var(--gray-100),var(--gray-200)); color:var(--gray-400); border-radius:16px; }

/* Gallery */
.gallery-item-card { background:white; border:1px solid var(--gray-200); border-radius:12px; overflow:hidden; cursor:pointer; box-shadow:var(--shadow-sm); transition:all 0.3s ease; }
.gallery-item-card:hover { box-shadow:var(--shadow-md); transform:translateY(-2px); }
.gallery-img-wrapper { height:200px; overflow:hidden; }
.gallery-img-wrapper img { width:100%; height:100%; object-fit:cover; transition:transform 0.4s ease; }
.gallery-item-card:hover .gallery-img-wrapper img { transform:scale(1.05); }

/* Video */
.video-card { background:white; border:1px solid var(--gray-200); border-radius:12px; overflow:hidden; box-shadow:var(--shadow-sm); transition:all 0.3s ease; cursor:pointer; }
.video-card:hover { box-shadow:var(--shadow-md); transform:translateY(-3px); }
.video-thumbnail { position:relative; width:100%; padding-bottom:56.25%; height:0; overflow:hidden; background:#000; }
.video-thumbnail img { position:absolute; top:0; left:0; width:100%; height:100%; object-fit:cover; transition:transform 0.4s ease; }
.video-card:hover .video-thumbnail img { transform:scale(1.05); }
.video-play-overlay { position:absolute; top:0; left:0; width:100%; height:100%; display:flex; align-items:center; justify-content:center; background:rgba(0,0,0,0.3); transition:background 0.3s ease; }
.video-card:hover .video-play-overlay { background:rgba(0,0,0,0.4); }
.video-play-btn { width:60px; height:60px; border-radius:50%; background:rgba(255,0,0,0.9); display:flex; align-items:center; justify-content:center; transition:all 0.3s ease; box-shadow:0 4px 20px rgba(255,0,0,0.3); }
.video-card:hover .video-play-btn { transform:scale(1.1); background:#ff0000; }
.video-play-btn i { color:white; font-size:1.3rem; margin-left:3px; }
.video-platform-badge { position:absolute; top:10px; left:10px; padding:4px 10px; border-radius:6px; font-size:0.7rem; font-weight:700; text-transform:uppercase; letter-spacing:0.5px; z-index:2; }
.platform-youtube { background:#ff0000; color:white; }
.platform-tiktok { background:#000000; color:white; }
.platform-instagram { background:linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888); color:white; }
.video-embed { position:relative; padding-bottom:56.25%; height:0; overflow:hidden; }
.video-embed iframe { position:absolute; top:0; left:0; width:100%; height:100%; border:none; }
.video-info { padding:10px 14px; }
.video-title { font-size:0.85rem; font-weight:600; color:var(--gray-800); margin:0; }

/* Share */
.share-btn { width:38px; height:38px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:0.9rem; text-decoration:none; transition:all 0.2s ease; color:white; }
.share-btn:hover { transform:scale(1.15); color:white; }
.share-btn-facebook { background:#1877F2; }
.share-btn-whatsapp { background:#25D366; }
.share-btn-telegram { background:#0088cc; }

/* Related */
.related-card { display:block; text-decoration:none; height:100%; }
.related-card-image { width:100%; height:140px; object-fit:cover; display:block; border-radius:16px 16px 0 0; }
.related-card-image-fallback { width:100%; height:140px; display:flex; align-items:center; justify-content:center; background:var(--gray-100); color:var(--gray-400); font-size:2rem; border-radius:16px 16px 0 0; }

/* Detail Content */
.detail-content { font-size:1.05rem; color:var(--gray-700); line-height:1.9; margin-bottom:30px; }
.detail-content p { margin-bottom:16px; }
.detail-content img { max-width:100%; height:auto; border-radius:12px; margin:16px 0; }

@media (max-width:767px) {
    .detail-featured-image img { border-radius:12px; }
    .detail-featured-image .image-fallback { min-height:200px; }
    .gallery-img-wrapper { height:160px; }
    .video-play-btn { width:46px; height:46px; }
    .video-play-btn i { font-size:1rem; }
    .detail-content { font-size:1rem; }
}

@media (max-width:575px) {
    .detail-featured-image img { border-radius:10px; }
    .detail-featured-image .image-fallback { min-height:180px; }
    .gallery-img-wrapper { height:140px; }
    .video-play-btn { width:40px; height:40px; }
    .video-play-btn i { font-size:0.9rem; }
    .detail-content { font-size:0.95rem; line-height:1.8; }
}
</style>

<script>
function openGalleryModal(src,caption){document.getElementById('galleryImage').src=src;document.getElementById('galleryCaption').textContent=caption;document.getElementById('galleryModal').style.display='flex';document.body.style.overflow='hidden';}
function closeGalleryModal(){document.getElementById('galleryModal').style.display='none';document.body.style.overflow='';}
document.addEventListener('keydown',function(e){if(e.key==='Escape')closeGalleryModal();});

function playVideo(index,url){var e=document.getElementById('videoEmbed'+index);var t=document.getElementById('videoThumb'+index);if(e&&t){t.style.display='none';e.style.display='block';e.scrollIntoView({behavior:'smooth',block:'center'});}else{window.open(url,'_blank');}}
</script>

<?php require_once 'includes/footer.php'; ?>