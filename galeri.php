<?php
$page_title = 'Galeri';
$current_page = 'galeri';
require_once 'config/database.php';
require_once 'includes/header.php';

try {
    $pdo = getConnection();
    $page = isset($_GET['page']) ? max(1,(int)$_GET['page']) : 1;
    $limit = 12;
    $offset = ($page-1)*$limit;
    $kategori = $_GET['kategori']??'';
    
    $where = ''; $params = [];
    if($kategori){$where="WHERE kategori=?";$params[]=$kategori;}
    
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM galeri $where");
    $stmt->execute($params);
    $total = $stmt->fetchColumn();
    $total_pages = ceil($total/$limit);
    
    $qp = array_merge($params,[$limit,$offset]);
    $stmt = $pdo->prepare("SELECT * FROM galeri $where ORDER BY created_at DESC LIMIT ? OFFSET ?");
    $stmt->execute($qp);
    $galeri_list = $stmt->fetchAll();
    
    $stmt = $pdo->query("SELECT DISTINCT kategori FROM galeri WHERE kategori IS NOT NULL ORDER BY kategori");
    $kategori_list = $stmt->fetchAll();
} catch (PDOException $e) {
    $galeri_list = []; $kategori_list = []; $total_pages = 0;
}
?>

<!-- Page Header -->
<section class="page-header-modern page-header-galeri">
    <div class="page-header-dots"></div>
    <div class="page-header-grid"></div>
    <div class="container">
        <div class="header-breadcrumb" data-aos="fade-down">
            <a href="index.php">Beranda</a>
            <span class="separator">›</span>
            <span>Galeri</span>
        </div>
        <div class="header-icon" data-aos="zoom-in">
            <i class="fa-solid fa-images"></i>
        </div>
        <h1 class="header-title" data-aos="fade-up" data-aos-delay="100">Galeri Kegiatan</h1>
        <p class="header-desc" data-aos="fade-up" data-aos-delay="200">Momen kegiatan siswa MI Muhammadiyah Bojongsana</p>
    </div>
</section>

<section class="section-modern section-white">
    <div class="container">
        <div class="d-flex gap-2 flex-wrap mb-4 justify-content-center">
            <a href="galeri.php" class="badge-modern" style="background:<?php echo !$kategori?'var(--primary)':'var(--gray-200)';?>;color:<?php echo !$kategori?'white':'var(--gray-600)';?>;text-decoration:none;">Semua</a>
            <?php foreach($kategori_list as $k): ?>
            <a href="?kategori=<?php echo urlencode($k['kategori']); ?>" class="badge-modern" style="background:<?php echo $kategori==$k['kategori']?'var(--primary)':'var(--gray-200)';?>;color:<?php echo $kategori==$k['kategori']?'white':'var(--gray-600)';?>;text-decoration:none;"><?php echo h($k['kategori']); ?></a>
            <?php endforeach; ?>
        </div>
        
        <?php if(!empty($galeri_list)): ?>
        <div class="row g-3">
            <?php foreach($galeri_list as $f): ?>
            <div class="col-6 col-md-4 col-lg-3" data-aos="zoom-in">
                <div style="border-radius:12px;overflow:hidden;box-shadow:var(--shadow-sm);cursor:pointer;transition:all 0.3s ease;position:relative;" onclick="openGallery('uploads/galeri/<?php echo h($f['gambar']); ?>','<?php echo h($f['deskripsi']??''); ?>')" onmouseover="this.style.transform='scale(1.03)'" onmouseout="this.style.transform='scale(1)'">
                    <img src="uploads/galeri/<?php echo h($f['gambar']); ?>" alt="Galeri" style="width:100%;height:220px;object-fit:cover;" loading="lazy">
                    <?php if($f['deskripsi']): ?>
                    <div style="position:absolute;bottom:0;left:0;right:0;background:linear-gradient(transparent,rgba(0,0,0,0.7));color:white;padding:10px;font-size:0.8rem;"><?php echo h(substr($f['deskripsi'],0,50)); ?></div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php if($total_pages>1): ?>
        <div class="d-flex justify-content-center gap-2 mt-4">
            <?php for($i=1;$i<=$total_pages;$i++): ?>
            <a href="?page=<?php echo $i; ?><?php echo $kategori?'&kategori='.urlencode($kategori):''; ?>" class="btn btn-sm" style="background:<?php echo $i==$page?'var(--primary)':'var(--gray-100)';?>;color:<?php echo $i==$page?'white':'var(--gray-600)';?>;border-radius:8px;"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
        <?php else: ?>
        <div class="text-center py-5"><h4 style="color:var(--gray-400);">Belum ada foto</h4></div>
        <?php endif; ?>
    </div>
</section>

<div id="galleryModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.9);z-index:9999;align-items:center;justify-content:center;padding:20px;" onclick="closeGallery()">
    <div style="max-width:800px;width:100%;" onclick="event.stopPropagation()">
        <button onclick="closeGallery()" style="position:absolute;top:20px;right:20px;background:white;border:none;border-radius:50%;width:40px;height:40px;cursor:pointer;z-index:10;">✕</button>
        <img src="" id="galleryImage" style="width:100%;max-height:80vh;object-fit:contain;border-radius:12px;">
    </div>
</div>
<script>
function openGallery(src,desc){document.getElementById('galleryImage').src=src;document.getElementById('galleryModal').style.display='flex';document.body.style.overflow='hidden';}
function closeGallery(){document.getElementById('galleryModal').style.display='none';document.body.style.overflow='';}
document.addEventListener('keydown',function(e){if(e.key==='Escape')closeGallery();});
</script>

<?php require_once 'includes/footer.php'; ?>