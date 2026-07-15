<?php
$page_title = 'Profil Sekolah';
$current_page = 'profil';
require_once 'config/database.php';
require_once 'includes/header.php';

try {
    $pdo = getConnection();
    $stmt = $pdo->query("SELECT * FROM prestasi ORDER BY tanggal DESC");
    $prestasi_list = $stmt->fetchAll();
    $stmt = $pdo->query("SELECT * FROM fasilitas ORDER BY id ASC");
    $fasilitas_list = $stmt->fetchAll();
} catch (PDOException $e) {
    $prestasi_list = [];
    $fasilitas_list = [];
}
?>

<style>
    /* ========================================
   VISI MISI SECTION - MODERN PROFESSIONAL
   ======================================== */

.visi-misi-section {
    padding: 80px 0;
    background: #fafbfc;
    position: relative;
    overflow: hidden;
}

/* Background decoration */
.visi-misi-section::before {
    content: '';
    position: absolute;
    top: -100px;
    right: -100px;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(26, 54, 93, 0.03) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}

.visi-misi-section::after {
    content: '';
    position: absolute;
    bottom: -50px;
    left: -50px;
    width: 300px;
    height: 300px;
    background: radial-gradient(circle, rgba(200, 144, 62, 0.03) 0%, transparent 70%);
    border-radius: 50%;
    pointer-events: none;
}

/* ============ SECTION HEADER ============ */
.section-label-modern {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: #e8eef5;
    color: #1a365d;
    padding: 6px 18px;
    border-radius: 50px;
    font-weight: 600;
    font-size: 0.85rem;
    margin-bottom: 14px;
}

.section-heading-modern {
    font-family: var(--font-heading);
    font-weight: 800;
    font-size: 2.5rem;
    color: #0f172a;
    margin-bottom: 10px;
    letter-spacing: -0.02em;
}

.section-subtitle-modern {
    font-family: var(--font-body);
    font-size: 1.05rem;
    color: #64748b;
    max-width: 560px;
    margin: 0 auto;
    line-height: 1.6;
}

/* ============ VISI CARD ============ */
.visi-card-wrapper {
    position: relative;
    z-index: 1;
}

.visi-card {
    background: linear-gradient(135deg, #1a365d 0%, #1e4d8c 50%, #2563eb 100%);
    border-radius: 24px;
    padding: 50px 40px;
    text-align: center;
    color: white;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(26, 54, 93, 0.2);
    transition: all 0.3s ease;
}

.visi-card:hover {
    box-shadow: 0 15px 50px rgba(26, 54, 93, 0.3);
    transform: translateY(-3px);
}

.visi-card-content {
    position: relative;
    z-index: 2;
}

.visi-label {
    display: inline-block;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    padding: 6px 22px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 0.8rem;
    letter-spacing: 3px;
    text-transform: uppercase;
    margin-bottom: 20px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.visi-text-large {
    font-family: var(--font-heading);
    font-weight: 700;
    font-size: 1.3rem;
    line-height: 1.9;
    margin-bottom: 16px;
    color: rgba(255, 255, 255, 0.95);
}

.visi-description {
    font-size: 0.9rem;
    color: rgba(255, 255, 255, 0.65);
    margin: 0;
}

/* Decorative circles */
.visi-card-decoration {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}

.decor-circle {
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.03);
}

.decor-1 {
    width: 200px;
    height: 200px;
    top: -50px;
    right: -50px;
}

.decor-2 {
    width: 150px;
    height: 150px;
    bottom: -30px;
    left: -30px;
}

.decor-3 {
    width: 100px;
    height: 100px;
    top: 50%;
    left: 10%;
}

/* ============ MISI SECTION ============ */
.misi-section {
    position: relative;
    z-index: 1;
}

.misi-label {
    display: inline-block;
    background: #fdf6ed;
    color: #c8903e;
    padding: 5px 18px;
    border-radius: 50px;
    font-weight: 700;
    font-size: 0.78rem;
    letter-spacing: 3px;
    text-transform: uppercase;
    margin-bottom: 10px;
}

.misi-heading {
    font-family: var(--font-heading);
    font-weight: 700;
    font-size: 1.8rem;
    color: #0f172a;
    margin-bottom: 6px;
}

.misi-subtitle {
    font-size: 0.95rem;
    color: #64748b;
    margin-bottom: 30px;
}

/* ============ MISI CARD ============ */
.misi-card {
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 28px 22px;
    text-align: center;
    height: 100%;
    transition: all 0.3s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    position: relative;
    top: 0;
}

.misi-card:hover {
    top: -6px;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
    border-color: #cbd5e1;
    transform: translateY(-2px);
}

.misi-card-icon {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    margin: 0 auto 16px;
    transition: all 0.3s ease;
}

.misi-card:hover .misi-card-icon {
    transform: scale(1.08);
}

.misi-card-text {
    font-family: var(--font-body);
    font-weight: 500;
    font-size: 0.95rem;
    color: #475569;
    line-height: 1.7;
    margin: 0;
}

/* ============ RESPONSIVE ============ */
@media (max-width: 991px) {
    .visi-misi-section {
        padding: 60px 0;
    }
    
    .section-heading-modern {
        font-size: 2rem;
    }
    
    .visi-card {
        padding: 36px 28px;
        border-radius: 20px;
    }
    
    .visi-text-large {
        font-size: 1.15rem;
    }
    
    .misi-heading {
        font-size: 1.5rem;
    }
    
    .misi-card {
        padding: 22px 16px;
        border-radius: 16px;
    }
    
    .misi-card-icon {
        width: 52px;
        height: 52px;
        font-size: 1.15rem;
        border-radius: 14px;
        margin-bottom: 12px;
    }
    
    .misi-card-text {
        font-size: 0.9rem;
    }
}

@media (max-width: 767px) {
    .visi-misi-section {
        padding: 50px 0;
    }
    
    .section-heading-modern {
        font-size: 1.7rem;
    }
    
    .section-subtitle-modern {
        font-size: 0.95rem;
    }
    
    .visi-card {
        padding: 30px 20px;
        border-radius: 18px;
    }
    
    .visi-label {
        font-size: 0.7rem;
        padding: 5px 16px;
        letter-spacing: 2px;
    }
    
    .visi-text-large {
        font-size: 1.05rem;
        line-height: 1.7;
    }
    
    .visi-description {
        font-size: 0.82rem;
    }
    
    .misi-heading {
        font-size: 1.3rem;
    }
    
    .misi-card {
        padding: 20px;
    }
    
    .misi-card-icon {
        width: 48px;
        height: 48px;
        font-size: 1.1rem;
        border-radius: 12px;
        margin-bottom: 10px;
    }
    
    .misi-card-text {
        font-size: 0.88rem;
        line-height: 1.6;
    }
}

@media (max-width: 575px) {
    .visi-misi-section {
        padding: 40px 0;
    }
    
    .section-heading-modern {
        font-size: 1.5rem;
    }
    
    .visi-card {
        padding: 24px 16px;
        border-radius: 16px;
    }
    
    .visi-text-large {
        font-size: 0.95rem;
    }
    
    .misi-card {
        padding: 18px 14px;
        border-radius: 14px;
    }
    
    .misi-card-icon {
        width: 44px;
        height: 44px;
        font-size: 1rem;
        border-radius: 10px;
    }
}
</style>
<!-- Page Header -->
<section class="page-header-modern page-header-profil">
    <div class="page-header-dots"></div>
    <div class="page-header-grid"></div>
    <div class="container">
        <div class="header-breadcrumb" data-aos="fade-down">
            <a href="index.php">Beranda</a>
            <span class="separator">›</span>
            <span>Profil</span>
        </div>
        <div class="header-icon" data-aos="zoom-in">
            <i class="fa-solid fa-school"></i>
        </div>
        <h1 class="header-title" data-aos="fade-up" data-aos-delay="100">Profil Sekolah</h1>
        <p class="header-desc" data-aos="fade-up" data-aos-delay="200">Mengenal lebih dekat MI Muhammadiyah Bojongsana</p>
    </div>
</section>

<!-- Visi & Misi -->
<section class="visi-misi-section">
    <div class="container">
        
        <!-- Section Header -->
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-label-modern">
                <i class="fa-solid fa-bullseye me-1"></i> Visi & Misi
            </span>
            <h2 class="section-heading-modern">Visi & Misi</h2>
            <p class="section-subtitle-modern">Landasan utama dalam mencetak generasi Qurani yang unggul dan berakhlak mulia</p>
        </div>
        
        <!-- ============ VISI CARD ============ -->
        <div class="visi-card-wrapper mb-5" data-aos="fade-up">
            <div class="visi-card">
                <div class="visi-card-content">
                    <span class="visi-label">VISI</span>
                    <h3 class="visi-text-large">
                        <?php echo nl2br(h(getPengaturan('visi'))); ?>
                    </h3>
                </div>
                <div class="visi-card-decoration">
                    <div class="decor-circle decor-1"></div>
                    <div class="decor-circle decor-2"></div>
                    <div class="decor-circle decor-3"></div>
                </div>
            </div>
        </div>
        
        <!-- ============ MISI SECTION ============ -->
        <div class="misi-section" data-aos="fade-up">
            <div class="text-center mb-4">
                <span class="misi-label">MISI</span>
                <h3 class="misi-heading">Misi Kami</h3>
                <p class="misi-subtitle">Langkah strategis untuk mewujudkan visi madrasah</p>
            </div>
            
            <div class="row g-4">
                <?php 
                $misi = getPengaturan('misi');
                $items = explode("\n", $misi);
                
                // Icons & Colors untuk setiap misi
                $misi_icons = [
                    'fa-solid fa-book-quran',
                    'fa-solid fa-lightbulb',
                    'fa-solid fa-star',
                    'fa-solid fa-heart',
                    'fa-solid fa-users',
                    'fa-solid fa-handshake',
                    'fa-solid fa-seedling',
                ];
                
                $misi_colors = [
                    ['bg' => '#eff6ff', 'icon' => '#2563eb'],
                    ['bg' => '#fef9f0', 'icon' => '#c8903e'],
                    ['bg' => '#ecfdf5', 'icon' => '#059669'],
                    ['bg' => '#fef2f2', 'icon' => '#dc2626'],
                    ['bg' => '#f5f3ff', 'icon' => '#7c3aed'],
                    ['bg' => '#f0f9ff', 'icon' => '#0284c7'],
                    ['bg' => '#fff7ed', 'icon' => '#ea580c'],
                ];
                
                foreach ($items as $i => $item): 
                    $item = trim($item);
                    if (empty($item)) continue;
                    $clean = preg_replace('/^\d+\.\s*/', '', $item);
                    $icon = $misi_icons[$i % count($misi_icons)];
                    $color = $misi_colors[$i % count($misi_colors)];
                ?>
                <div class="col-lg-4 col-md-6 col-12" data-aos="fade-up" data-aos-delay="<?php echo $i * 100; ?>">
                    <div class="misi-card">
                        <div class="misi-card-icon" style="background: <?php echo $color['bg']; ?>;">
                            <i class="<?php echo $icon; ?>" style="color: <?php echo $color['icon']; ?>;"></i>
                        </div>
                        <p class="misi-card-text"><?php echo h($clean); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
    </div>
</section>

<!-- Sejarah -->
<section class="section-modern section-gray" id="sejarah">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-label"><i class="fa-solid fa-clock-rotate-left"></i> Sejarah</span>
            <h2 class="section-title-modern">Sejarah Sekolah</h2>
            <p class="section-desc-modern mx-auto">Perjalanan MI Muhammadiyah Bojongsana dari masa ke masa</p>
        </div>
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="timeline-modern">
                    <?php 
                    $sejarah_data = [
                        ['year'=>'1980','icon'=>'fa-solid fa-building-columns','title'=>'Berdirinya Madrasah','text'=>getPengaturan('sejarah'),'color'=>'#2563eb'],
                        ['year'=>'1990-2000','icon'=>'fa-solid fa-chart-line','title'=>'Masa Perkembangan','text'=>'Periode pengembangan gedung, penambahan ruang kelas, dan peningkatan kualitas tenaga pendidik. Jumlah siswa terus bertambah setiap tahunnya.','color'=>'#c8903e'],
                        ['year'=>'2000-Sekarang','icon'=>'fa-solid fa-trophy','title'=>'Era Prestasi','text'=>'MI Muhammadiyah Bojongsana terus berprestasi di berbagai bidang, baik akademik maupun non-akademik. Menjadi salah satu madrasah unggulan di wilayah.','color'=>'#059669'],
                    ];
                    foreach ($sejarah_data as $i => $s): 
                    ?>
                    <div class="timeline-item" data-aos="fade-up" data-aos-delay="<?php echo $i*100; ?>">
                        <div class="timeline-dot" style="background:<?php echo $s['color']; ?>;"></div>
                        <div class="card-modern">
                            <div class="card-body">
                                <span class="badge-modern" style="background:<?php echo $s['color']; ?>15;color:<?php echo $s['color']; ?>;margin-bottom:12px;"><?php echo $s['year']; ?></span>
                                <h4><i class="<?php echo $s['icon']; ?> me-2" style="color:<?php echo $s['color']; ?>;"></i><?php echo $s['title']; ?></h4>
                                <p style="color:var(--gray-500);font-size:0.95rem;"><?php echo nl2br(h($s['text'])); ?></p>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Prestasi - Horizontal Scroll dengan Card Vertikal -->
<section class="section-modern section-white" id="prestasi">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-label"><i class="fa-solid fa-trophy"></i> Prestasi</span>
            <h2 class="section-title-modern">Prestasi Membanggakan</h2>
            <p class="section-desc-modern mx-auto">Prestasi yang telah diraih siswa MI Muhammadiyah Bojongsana</p>
        </div>
        
        <?php if (!empty($prestasi_list)): ?>
        <!-- Scroll Wrapper -->
        <div class="prestasi-scroll-wrapper">
            <!-- Tombol Kiri -->
            <button class="prestasi-scroll-btn prestasi-scroll-left" id="prestasiScrollLeft" aria-label="Scroll kiri">
                <i class="fa-solid fa-chevron-left"></i>
            </button>
            
            <!-- Container Scroll -->
            <div class="prestasi-scroll-container" id="prestasiScroll">
                <?php 
                $icons = ['fa-solid fa-trophy', 'fa-solid fa-medal', 'fa-solid fa-award', 'fa-solid fa-star', 'fa-solid fa-certificate', 'fa-solid fa-crown'];
                $colors = ['#2563eb', '#c8903e', '#059669', '#7c3aed', '#dc2626', '#0284c7'];
                $gradients = [
                    'linear-gradient(180deg, #eff6ff 0%, #dbeafe 100%)',
                    'linear-gradient(180deg, #fef9f0 0%, #fdf0d5 100%)',
                    'linear-gradient(180deg, #ecfdf5 0%, #d1fae5 100%)',
                    'linear-gradient(180deg, #f5f3ff 0%, #ede9fe 100%)',
                    'linear-gradient(180deg, #fef2f2 0%, #fecaca 100%)',
                    'linear-gradient(180deg, #f0f9ff 0%, #e0f2fe 100%)',
                ];
                
                foreach ($prestasi_list as $i => $p): 
                    $icon = $icons[$i % count($icons)];
                    $color = $colors[$i % count($colors)];
                    $gradient = $gradients[$i % count($gradients)];
                ?>
                <div class="prestasi-card-vertical" data-aos="fade-up" data-aos-delay="<?php echo $i * 80; ?>">
                    <!-- Bagian Atas - Icon & Gradient -->
                    <div class="prestasi-card-top" style="background: <?php echo $gradient; ?>;">
                        <div class="prestasi-card-badge" style="background: <?php echo $color; ?>15; color: <?php echo $color; ?>;">
                            <?php if ($p['kategori']): ?>
                                <?php echo h($p['kategori']); ?>
                            <?php else: ?>
                                Prestasi
                            <?php endif; ?>
                        </div>
                        <div class="prestasi-card-icon-large" style="background: white; color: <?php echo $color; ?>; box-shadow: 0 4px 15px <?php echo $color; ?>30;">
                            <i class="<?php echo $icon; ?>"></i>
                        </div>
                    </div>
                    
                    <!-- Bagian Bawah - Konten -->
                    <div class="prestasi-card-bottom">
                        <h4 class="prestasi-card-title-vertical"><?php echo h($p['judul']); ?></h4>
                        
                        <?php if ($p['tanggal']): ?>
                        <div class="prestasi-card-date-vertical">
                            <i class="fa-regular fa-calendar me-1"></i> <?php echo formatTanggal($p['tanggal']); ?>
                        </div>
                        <?php endif; ?>
                        
                        <p class="prestasi-card-desc-vertical">
                            <?php echo h(substr($p['deskripsi'] ?? '', 0, 80)); ?>...
                        </p>
                        
                        <div class="prestasi-card-footer">
                            <span class="prestasi-card-line" style="background: <?php echo $color; ?>;"></span>
                            <span class="prestasi-card-number"><?php echo str_pad($i + 1, 2, '0', STR_PAD_LEFT); ?></span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            
            <!-- Tombol Kanan -->
            <button class="prestasi-scroll-btn prestasi-scroll-right" id="prestasiScrollRight" aria-label="Scroll kanan">
                <i class="fa-solid fa-chevron-right"></i>
            </button>
        </div>
        
        <!-- Scroll Indicator Dots -->
        <div class="prestasi-scroll-dots" id="prestasiDots"></div>
        
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fa-solid fa-trophy fa-3x mb-3" style="color: var(--gray-300);"></i>
            <h4 style="color: var(--gray-400);">Data prestasi akan segera diperbarui</h4>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Fasilitas -->
<section class="section-modern section-light" id="fasilitas">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-label"><i class="fa-solid fa-building"></i> Fasilitas</span>
            <h2 class="section-title-modern">Fasilitas Sekolah</h2>
            <p class="section-desc-modern mx-auto">Fasilitas pendukung pembelajaran optimal</p>
        </div>
        <?php if (!empty($fasilitas_list)): ?>
        <div class="row g-4">
            <?php $f_icons = ['fa-solid fa-chalkboard-teacher','fa-solid fa-couch','fa-solid fa-user-tie','fa-solid fa-desktop','fa-solid fa-utensils' ,'fa-solid fa-person-chalkboard','fa-solid fa-person-chalkboard','fa-solid fa-person-chalkboard','fa-solid fa-person-chalkboard','fa-solid fa-person-chalkboard','fa-solid fa-person-chalkboard'];
            foreach ($fasilitas_list as $i => $f): ?>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="<?php echo $i*80; ?>">
                <div class="card-modern h-100">
                    <?php if($f['gambar']): ?>
                    <img src="uploads/fasilitas/<?php echo h($f['gambar']); ?>" alt="<?php echo h($f['nama']); ?>" style="height:180px;object-fit:cover;width:100%;cursor:pointer;" onclick="openModal('uploads/fasilitas/<?php echo h($f['gambar']); ?>','<?php echo h($f['nama']); ?>','<?php echo h($f['deskripsi']??''); ?>')">
                    <?php endif; ?>
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="<?php echo $f_icons[$i]; ?>" style="color:var(--primary);"></i>
                            <h5 style="font-size:0.95rem;"><?php echo h($f['nama']); ?></h5>
                        </div>
                        <p style="color:var(--gray-500);font-size:0.85rem;"><?php echo h(substr($f['deskripsi']??'',0,80)); ?>...</p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- Lokasi -->
<section class="section-modern section-light" id="lokasi">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-label"><i class="fa-solid fa-location-dot"></i> Lokasi</span>
            <h2 class="section-title-modern">Lokasi Kami</h2>
        </div>
        <div class="row g-4">
            <div class="col-lg-8" data-aos="fade-up">
                <div class="card-modern" style="overflow:hidden;">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3957.6887714618288!2d109.51259327414698!3d-7.276210571501175!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e6fffdb5abe4ab1%3A0x3ce0f1755d8922eb!2sMadrasah%20Ibtidaiyah%20Muhammadiyah%20Bojongsana!5e0!3m2!1sid!2sid!4v1778069816825!5m2!1sid!2sid" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" width="100%" height="400" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
                </div>
            </div>
            <div class="col-lg-4" data-aos="fade-up" data-aos-delay="100">
                <div class="card-modern h-100"><div class="card-body">
                    <h4><i class="fa-solid fa-circle-info me-2" style="color:var(--primary);"></i>Kontak</h4><hr>
                    <p><strong><i class="fa-solid fa-map-marker-alt me-2"></i> Alamat:</strong><br><?php echo h(getPengaturan('alamat')); ?></p>
                    <p><strong><i class="fa-solid fa-phone-alt me-2"></i> Telepon:</strong><br><?php echo h(getPengaturan('telepon')); ?></p>
                    <p><strong><i class="fa-solid fa-envelope me-2"></i> Email:</strong><br><?php echo h(getPengaturan('email')); ?></p>
                </div></div>
            </div>
        </div>
    </div>
</section>

<!-- CSS Timeline -->
<style>
.timeline-modern{position:relative;padding-left:40px;border-left:3px solid var(--gray-200);}
.timeline-item{position:relative;margin-bottom:30px;}
.timeline-dot{position:absolute;left:-50px;top:20px;width:18px;height:18px;border-radius:50%;border:3px solid white;box-shadow:0 0 0 3px currentColor;}
@media(max-width:767px){.timeline-modern{padding-left:30px;}.timeline-dot{left:-38px;width:14px;height:14px;}}
</style>

<!-- Modal -->
<div id="fasilitasModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.8);z-index:9999;align-items:center;justify-content:center;padding:20px;" onclick="closeModal()">
    <div style="background:white;max-width:700px;width:100%;border-radius:16px;overflow:hidden;" onclick="event.stopPropagation()">
        <div style="padding:16px 24px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid var(--gray-200);"><h5 class="mb-0" id="modalTitle"></h5><button onclick="closeModal()" style="border:none;background:none;font-size:1.5rem;cursor:pointer;">&times;</button></div>
        <img src="" id="modalImage" style="width:100%;max-height:450px;object-fit:contain;background:var(--gray-100);">
        <div style="padding:16px 24px;"><p class="mb-0" id="modalDesc" style="color:var(--gray-600);"></p></div>
    </div>
</div>
<script>
function openModal(src,title,desc){document.getElementById('modalImage').src=src;document.getElementById('modalTitle').textContent=title;document.getElementById('modalDesc').textContent=desc;document.getElementById('fasilitasModal').style.display='flex';document.body.style.overflow='hidden';}
function closeModal(){document.getElementById('fasilitasModal').style.display='none';document.body.style.overflow='';}
document.addEventListener('keydown',function(e){if(e.key==='Escape')closeModal();});
</script>

<?php require_once 'includes/footer.php'; ?>