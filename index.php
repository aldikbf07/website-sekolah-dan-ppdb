<?php
$page_title = 'Beranda';
$current_page = 'home';
require_once 'config/database.php';
require_once 'includes/header.php';

try {
    $pdo = getConnection();
    
    // Berita terbaru
    $stmt = $pdo->prepare("SELECT * FROM berita WHERE status = 'published' ORDER BY tanggal DESC LIMIT 4");
    $stmt->execute();
    $berita_list = $stmt->fetchAll();
    
    // Galeri terbaru
    $stmt = $pdo->prepare("SELECT * FROM galeri ORDER BY created_at DESC LIMIT 6");
    $stmt->execute();
    $galeri_list = $stmt->fetchAll();
    
    // Guru featured
    $stmt = $pdo->prepare("SELECT * FROM guru ORDER BY RAND() LIMIT 4");
    $stmt->execute();
    $guru_list = $stmt->fetchAll();
    
    // Prestasi terbaru
    $stmt = $pdo->query("SELECT * FROM prestasi ORDER BY tanggal DESC LIMIT 4");
    $prestasi_list = $stmt->fetchAll();
    
} catch (PDOException $e) {
    $berita_list = [];
    $galeri_list = [];
    $guru_list = [];
    $prestasi_list = [];
}
?>


<style>
.guru-section-header {
    display: flex;
    align-items: center;
    gap: 16px;
    margin-bottom: 24px;
}
.guru-section-icon {
    width: 50px; height: 50px; border-radius: 14px;
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 1.2rem; flex-shrink: 0;
}
.guru-section-header h3 { font-size: 1.3rem; margin-bottom: 2px; }
.guru-section-header p { color: var(--gray-500); font-size: 0.9rem; margin: 0; }

/* ============ GURU CARD ============ */
.guru-card-modern {
    background: white;
    border: 1px solid var(--gray-200);
    border-radius: 20px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    transition: all 0.3s ease;
    text-align: center;
    padding: 28px 20px 22px;
    height: 100%;
}
.guru-card-modern:hover { box-shadow: var(--shadow-lg); transform: translateY(-5px); }
.guru-card-leader { border-top: 5px solid #1a365d !important; }
.guru-card-badge {
    display: inline-block;
    background: linear-gradient(135deg, #1a365d, #2563eb);
    color: white;
    padding: 4px 14px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    margin-bottom: 14px;
}
.guru-card-photo {
    width: 90px; height: 90px; border-radius: 50%;
    object-fit: cover; margin: 0 auto 14px;
    border: 3px solid white; box-shadow: 0 0 0 3px var(--gray-200);
}
.guru-card-photo-placeholder {
    width: 90px; height: 90px; border-radius: 50%;
    margin: 0 auto 14px; display: flex;
    align-items: center; justify-content: center;
    color: white; font-size: 1.8rem;
}
.guru-card-info h4 { font-size: 1.05rem; margin-bottom: 4px; }
.guru-card-nip { display: block; font-size: 0.8rem; color: var(--gray-400); margin-bottom: 6px; }
.guru-card-role { display: block; font-size: 0.82rem; color: var(--gray-500); font-weight: 500; }
.guru-card-mapel {
    display: inline-block; margin-top: 6px;
    padding: 3px 12px; border-radius: 20px;
    font-size: 0.78rem; font-weight: 500;
    background: var(--primary-light); color: var(--primary);
}

/* ============ RESPONSIVE ============ */
@media (max-width: 991px) {
    .org-card-head { min-width: auto; }
    .org-card-sub { max-width: none; flex: 1 1 45%; min-width: 160px; }
    .org-row { gap: 10px; }
}

@media (max-width: 767px) {
    .org-card { padding: 14px; gap: 10px; }
    .org-card-sub { flex: 1 1 100%; }
    .org-avatar { width: 44px; height: 44px; font-size: 1rem; }
    .org-avatar-lg { width: 56px; height: 56px; }
    .guru-card-modern { padding: 20px 14px 16px; }
    .guru-card-photo, .guru-card-photo-placeholder { width: 72px; height: 72px; }
}

@media (max-width: 575px) {
    .org-card { border-radius: 12px; }
    .org-line-horizontal { width: 80%; }
    .guru-section-header { gap: 10px; }
    .guru-section-icon { width: 40px; height: 40px; font-size: 1rem; border-radius: 10px; }
    .guru-section-header h3 { font-size: 1.1rem; }
}
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
<!-- Hero Carousel Modern -->
<section class="hero-carousel-modern">
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
    
        
        <div class="carousel-inner">
            
            <!-- Slide 1 - Pendidikan -->
            <div class="carousel-item active">
                <div class="hero-slide" style="background: linear-gradient(135deg, #1a365d 0%, #1e4d8c 40%, #2563eb 100%);">
                    <div class="slide-shape slide-shape-1"></div>
                    <div class="slide-shape slide-shape-2"></div>
                    <div class="slide-dots"></div>
                    
                    <div class="container h-100">
                        <div class="row align-items-center h-100">
                            
                            <!-- GAMBAR - Mobile: Atas | Desktop: Kanan -->
                            <div class="col-lg-6 order-1 order-lg-2 d-flex justify-content-center" data-aos="fade-up" data-aos-duration="800">
                                <div class="slide-image-wrapper">
                                    <div class="slide-image-frame">
                                        <img src="assets/images/belajar.jpg" alt="Pendidikan" class="slide-image" 
                                             onerror="this.style.display='none'; this.parentElement.querySelector('.image-placeholder').style.display='flex';">
                                        <div class="image-placeholder" style="display:none;">
                                            <i class="fa-solid fa-book-open"></i>
                                        </div>
                                    </div>
                                </div>
                                 <div class="floating-card floating-card-1"><span><i class="fa-solid fa-graduation-cap"></i></span><small>Belajar Aktif</small></div>
                                    <div class="floating-card floating-card-2"><span><i class="fa-solid fa-trophy"></i></span><small>Berprestasi</small></div>
                            </div>
                            
                            <!-- KONTEN - Mobile: Bawah | Desktop: Kiri -->
                            <div class="col-lg-6 order-2 order-lg-1 mt-5" data-aos="fade-up" data-aos-duration="800" data-aos-delay="200">
                                <div class="slide-content">
                                    <span class="slide-badge">
                                        <span class="badge-dot"></span> Pendidikan Berkualitas
                                    </span>
                                    <h1 class="slide-title">
                                        Belajar dengan<br>
                                        <span class="text-gradient-gold">Semangat & Ceria</span>
                                    </h1>
                                    <p class="slide-desc">
                                        MI Muhammadiyah Bojongsana menyediakan pendidikan berkualitas dengan metode pembelajaran yang menyenangkan dan interaktif untuk mencetak generasi unggul.
                                    </p>
                                    <div class="slide-buttons">
                                        <a href="profil.php" class="btn-slide btn-slide-primary">
                                            <i class="fa-solid fa-circle-info"></i> Profil Sekolah
                                        </a>
                                        <a href="ppdb.php" class="btn-slide btn-slide-outline">
                                            <i class="fa-solid fa-graduation-cap"></i> PPDB 2026/2027
                                        </a>
                                    </div>
                                    <div class="slide-stats">
                                        <div class="slide-stat">
                                            <span class="stat-value counter" data-target="200">0</span>
                                            <span class="stat-label">Siswa Aktif</span>
                                        </div>
                                        <div class="slide-stat">
                                            <span class="stat-value counter" data-target="10">0</span>
                                            <span class="stat-label">Guru Professional</span>
                                        </div>
                                        <div class="slide-stat">
                                            <span class="stat-value counter" data-target="60">0</span>
                                            <span class="stat-label">Tahun Berdiri</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Slide 2 - Keagamaan -->
            <div class="carousel-item">
                <div class="hero-slide" style="background: linear-gradient(135deg, #0f3d24 0%, #1a5f3a 40%, #0d9488 100%);">
                    <div class="slide-shape slide-shape-1"></div>
                    <div class="slide-shape slide-shape-2"></div>
                    <div class="slide-dots"></div>
                    
                    <div class="container h-100">
                        <div class="row align-items-center h-100">
                            
                            <!-- GAMBAR - Mobile: Atas | Desktop: Kanan -->
                            <div class="col-lg-6 order-1 order-lg-2  justify-content-center">
                                <div class="slide-image-wrapper">
                                    <div class="slide-image-frame">
                                        <img src="assets/images/mengaji.jpg" alt="Kreativitas" class="slide-image"
                                             onerror="this.style.display='none'; this.parentElement.querySelector('.image-placeholder').style.display='flex';">
                                        <div class="image-placeholder" style="display:none;">
                                            <i class="fa-solid fa-book"></i>
                                        </div>
                                    </div>
                                    <div class="floating-card floating-card-1">
                                        <span><i class="fa-solid fa-mosque"></i></span>
                                        <small>Ibadah rutin</small>
                                    </div>
                                    <div class="floating-card floating-card-2">
                                        <span><i class="fa-solid fa-quran"></i></span>
                                        <small>Mengaji</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- KONTEN - Mobile: Bawah | Desktop: Kiri -->
                            <div class="col-lg-6 order-2 order-lg-1">
                                <div class="slide-content">
                                    <span class="slide-badge"><span class="badge-dot" style="background:#4ade80;"></span> Keagamaan</span>
                                    <h1 class="slide-title">Membentuk<br><span class="text-gradient-gold">Generasi Qurani</span></h1>
                                    <p class="slide-desc">Pembiasaan ibadah dan mengaji sebagai bagian dari pembentukan karakter Islami yang berakhlak mulia dan mencintai Al-Quran.</p>
                                    <div class="slide-buttons">
                                        <a href="galeri.php" class="btn-slide btn-slide-white"><i class="fa-solid fa-images"></i> Lihat Galeri</a>
                                        <a href="profil.php#visi-misi" class="btn-slide btn-slide-outline-white"><i class="fa-solid fa-eye"></i> Visi Misi</a>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Slide 3 - Olahraga -->
            <div class="carousel-item">
                <div class="hero-slide" style="background: linear-gradient(135deg, #1a365d 0%, #0284c7 40%, #0ea5e9 100%);">
                    <div class="slide-shape slide-shape-1"></div>
                    <div class="slide-shape slide-shape-2"></div>
                    <div class="slide-dots"></div>
                    
                    <div class="container h-100">
                        <div class="row align-items-center h-100">
                            
                            <!-- GAMBAR - Mobile: Atas | Desktop: Kanan -->
                                <div class="col-lg-6 order-1 order-lg-2 justify-content-center">
                                <div class="slide-image-wrapper">
                                    <div class="slide-image-frame">
                                        <img src="assets/images/olahraga.jpeg" alt="Olahraga" class="slide-image"
                                             onerror="this.style.display='none'; this.parentElement.querySelector('.image-placeholder').style.display='flex';">
                                        <div class="image-placeholder" style="display:none;">
                                            <i class="fa-solid fa-futbol"></i>
                                        </div>
                                    </div>
                                    <div class="floating-card floating-card-1">
                                        <span><i class="fa-solid fa-futbol"></i></span>
                                        <small>Futsal</small>
                                    </div>
                                    <div class="floating-card floating-card-2">
                                        <span><i class="fa-solid fa-running"></i></span>
                                        <small>Atletik</small>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- KONTEN - Mobile: Bawah | Desktop: Kiri -->
                            <div class="col-lg-6 order-2 order-lg-1">
                                <div class="slide-content">
                                    <span class="slide-badge"><span class="badge-dot" style="background:#fbbf24;"></span> Olahraga</span>
                                    <h1 class="slide-title">Aktif Bergerak<br><span class="text-gradient-gold">Sehat & Bugar</span></h1>
                                    <p class="slide-desc">Kegiatan olahraga rutin untuk menjaga kebugaran dan mengembangkan bakat siswa dalam berbagai cabang olahraga dan permainan.</p>
                                    <div class="slide-buttons">
                                        <a href="galeri.php" class="btn-slide btn-slide-white"><i class="fa-solid fa-images"></i> Galeri</a>
                                        <a href="profil.php#fasilitas" class="btn-slide btn-slide-outline-white"><i class="fa-solid fa-building"></i> Fasilitas</a>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Navigation Arrows -->
        <button class="carousel-control-prev hero-nav" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
            <span class="hero-nav-icon"><i class="fa-solid fa-arrow-left"></i></span>
        </button>
        <button class="carousel-control-next hero-nav" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
            <span class="hero-nav-icon"><i class="fa-solid fa-arrow-right"></i></span>
        </button>
        
    </div>
</section>


<!-- ========================================
     VISI MISI SECTION - MODERN DESIGN
     ======================================== -->
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
                        " <?php echo nl2br(h(getPengaturan('visi'))); ?> "
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

<!-- ========================================
     PROFIL OVERVIEW
     ======================================== -->
<section class="section-modern section-white">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-label"><i class="fa-solid fa-school"></i> Profil</span>
            <h2 class="section-title-modern">Mengenal MI Muhammadiyah Bojongsana</h2>
            <p class="section-desc-modern mx-auto">Sekolah Islam unggulan yang berkomitmen mencetak generasi berkualitas</p>
        </div>
        
        <div class="row g-4">
            <?php 
            $features = [
                ['icon'=>'assets/images/icon/sejarah.png','title'=>'Sejarah','text'=>'Berdiri sejak 1980, melahirkan generasi unggul.','link'=>'profil.php#sejarah','color'=>'var(--primary)'],
                ['icon'=>'assets/images/icon/prestasi.png','title'=>'Prestasi','text'=>'Berbagai prestasi akademik dan non-akademik.','link'=>'profil.php#prestasi','color'=>'var(--accent)'],
                ['icon'=>'assets/images/icon/fasilitas.png','title'=>'Fasilitas','text'=>'Fasilitas lengkap pendukung pembelajaran.','link'=>'profil.php#fasilitas','color'=>'#0284c7'],
                ['icon'=>'assets/images/icon/lokasi.png','title'=>'Lokasi','text'=>'Lokasi strategis mudah dijangkau.','link'=>'profil.php#lokasi','color'=>'#059669'],
            ];
            foreach ($features as $i => $f): 
            ?>
            <div class="col-md-6 col-lg-3" data-aos="fade-up" data-aos-delay="<?php echo $i*100; ?>">
                <a href="<?php echo $f['link']; ?>" class="text-decoration-none">
                    <div class="card-modern text-center h-100 hover-lift">
                        <div class="card-body">
                            <div style="width:60px;height:60px;background:<?php echo $f['color']; ?>15;border-radius:16px;display:inline-flex;align-items:center;justify-content:center;font-size:1.5rem;color:<?php echo $f['color']; ?>;margin-bottom:16px;">
                                <img src="<?php echo $f['icon']; ?>" alt="<?php echo $f['title']; ?>" class="img-fluid">
                            </div>
                            <h5><?php echo $f['title']; ?></h5>
                            <p style="color:var(--gray-500);font-size:0.9rem;"><?php echo $f['text']; ?></p>
                            <span style="color:<?php echo $f['color']; ?>;font-weight:600;font-size:0.85rem;">Selengkapnya →</span>
                        </div>
                    </div>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ========================================
     BERITA TERBARU
     ======================================== -->
<section class="section-modern section-light">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-label"><i class="fa-solid fa-newspaper"></i> Update</span>
            <h2 class="section-title-modern">Berita & Kegiatan Terbaru</h2>
            <p class="section-desc-modern mx-auto">Informasi terkini seputar kegiatan di MI Muhammadiyah Bojongsana</p>
        </div>
        
        <?php if (!empty($berita_list)): ?>
        <div class="row g-4">
            <?php foreach ($berita_list as $berita): ?>
            <div class="col-md-6 col-lg-3" data-aos="fade-up">
                <div class="card-modern h-100">
                    <?php if ($berita['gambar']): ?>
                    <img src="uploads/berita/<?php echo h($berita['gambar']); ?>" alt="<?php echo h($berita['judul']); ?>" class="card-img-top" style="height:180px;">
                    <?php else: ?>
                    <div class="card-img-top" style="height:180px;background:var(--gray-200);display:flex;align-items:center;justify-content:center;font-size:3rem;color:var(--gray-400);">
                        <i class="fa-solid fa-newspaper"></i>
                    </div>
                    <?php endif; ?>
                    <div class="card-body d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small style="color:var(--gray-400);"><i class="fa-regular fa-calendar me-1"></i> <?php echo formatTanggal($berita['tanggal']); ?></small>
                            <span class="badge-modern badge-primary"><?php echo h($berita['kategori'] ?? 'Umum'); ?></span>
                        </div>
                        <h5 class="card-title"><?php echo h($berita['judul']); ?></h5>
                        <p class="card-text flex-grow-1"><?php echo substr(strip_tags($berita['isi']), 0, 100); ?>...</p>
                        <a href="detail-berita.php?id=<?php echo $berita['id']; ?>" class="btn-modern btn-outline-modern btn-sm mt-2" style="padding:6px 14px;font-size:0.85rem;">
                            Baca <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="berita.php" class="btn-modern btn-primary-modern">
                Lihat Semua Berita <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fa-solid fa-newspaper fa-3x mb-3" style="color:var(--gray-300);"></i>
            <h4 style="color:var(--gray-400);">Belum ada berita</h4>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ========================================
     GALERI PREVIEW
     ======================================== -->
<section class="section-modern">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-label"><i class="fa-solid fa-images"></i> Galeri</span>
            <h2 class="section-title-modern">Galeri Kegiatan</h2>
            <p class="section-desc-modern mx-auto">Dokumentasi momen kegiatan siswa MI Muhammadiyah Bojongsana</p>
        </div>
        
        <?php if (!empty($galeri_list)): ?>
        <div class="row g-3">
            <?php foreach ($galeri_list as $foto): ?>
            <div class="col-6 col-md-4 col-lg-2" data-aos="zoom-in">
                <div class="hover-scale" style="border-radius:12px;overflow:hidden;box-shadow:var(--shadow-sm);cursor:pointer;" 
                     onclick="openGalleryModal('uploads/galeri/<?php echo h($foto['gambar']); ?>','<?php echo h($foto['deskripsi']??''); ?>')">
                    <img src="uploads/galeri/<?php echo h($foto['gambar']); ?>" alt="Galeri" style="width:100%;height:180px;object-fit:cover;" loading="lazy">
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="galeri.php" class="btn-modern btn-primary-modern">
                Lihat Semua Galeri <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>
        <?php else: ?>
        <div class="text-center py-5">
            <i class="fa-solid fa-images fa-3x mb-3" style="color:var(--gray-300);"></i>
            <h4 style="color:var(--gray-400);">Belum ada foto galeri</h4>
        </div>
        <?php endif; ?>
    </div>
</section>

<!-- ========================================
     GURU FEATURED
     ======================================== -->
<?php if (!empty($guru_list)): ?>
<section class="section-modern section-gray">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-label"><i class="fa-solid fa-users"></i> Tim Pengajar</span>
            <h2 class="section-title-modern">Guru & Staff Kami</h2>
            <p class="section-desc-modern mx-auto">Tenaga pendidik profesional yang berdedikasi</p>
        </div>
        
        <div class="row g-4">
            <?php foreach ($guru_list as $g): ?>
            <div class="col-md-6 col-lg-3" data-aos="fade-up">
                    <div class="guru-card-modern" style="border-top: 4px solid #7c3aed;">
                        <?php if ($g['foto']): ?>
                        <img src="uploads/guru/<?php echo h($g['foto']); ?>" alt="<?php echo h($g['nama']); ?>" class="guru-card-photo">
                        <?php else: ?>
                        <div class="guru-card-photo-placeholder" style="background: linear-gradient(135deg, #7c3aed, #8b5cf6);">
                            <i class="fa-solid fa-book-open"></i>
                        </div>
                        <?php endif; ?>
                        <div class="guru-card-info">
                            <h4><?php echo h($g['nama']); ?></h4>
                              <span class="guru-card-nip">
            <?php if (!empty($g['nip'])): ?>
            NIP. <?php echo h($g['nip']); ?>
            <?php else: ?>
            <span style="color: #94a3b8; font-style: italic;">
                <i class="fa-solid fa-circle-info me-1"></i> Belum memiliki NIP
            </span>
            <?php endif; ?>
        </span >
                            <?php if ($g['mapel']): ?>
                            <span class="guru-card-mapel"><i class="fa-solid fa-book"></i> <?php echo h($g['mapel']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="guru.php" class="btn-modern btn-outline-modern">
                Lihat Semua Guru <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ========================================
     CTA PPDB
     ======================================== -->
<section style="background: linear-gradient(135deg, var(--primary-dark), var(--primary)); padding: 80px 0; text-align: center; color: white;">
    <div class="container" data-aos="fade-up">
        <img src="assets/images/icon/backpack.png" alt="Backpack" style="display:block;width:80px;height:80px;margin:0 auto 20px;" >
        <h2 style="color:white;font-size:2rem;margin-bottom:12px; font-family: var(--font-body);">  PPDB 2026/2027 Telah Dibuka!</h2>
        <p style="color:rgba(255,255,255,0.9);font-size:1.1rem;margin-bottom:24px;max-width:500px;margin-left:auto;margin-right:auto;">
            Segera daftarkan putra-putri Anda di MI Muhammadiyah Bojongsana. Kuota terbatas!
        </p>
        <a href="ppdb.php" class="btn-modern btn-accent-modern btn-lg">
            <i class="fa-solid fa-graduation-cap"></i> Daftar Sekarang
        </a>
    </div>
</section>

<!-- Gallery Modal -->
<div id="galleryModal" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.9);z-index:9999;align-items:center;justify-content:center;padding:20px;" onclick="closeGalleryModal()">
    <div style="max-width:800px;width:100%;" onclick="event.stopPropagation()">
        <button onclick="closeGalleryModal()" style="position:absolute;top:20px;right:20px;background:white;border:none;border-radius:50%;width:40px;height:40px;font-size:1.2rem;cursor:pointer;z-index:10;">✕</button>
        <img src="" id="galleryImage" style="width:100%;max-height:80vh;object-fit:contain;border-radius:12px;">
    </div>
</div>

<script>
// Gallery Modal
function openGalleryModal(src, desc) {
    document.getElementById('galleryImage').src = src;
    document.getElementById('galleryModal').style.display = 'flex';
    document.body.style.overflow = 'hidden';
}
function closeGalleryModal() {
    document.getElementById('galleryModal').style.display = 'none';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e) { if(e.key==='Escape') closeGalleryModal(); });
</script>

<?php require_once 'includes/footer.php'; ?>