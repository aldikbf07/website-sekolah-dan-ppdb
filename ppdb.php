<?php
$page_title = 'PPDB 2026/2027';
$current_page = 'ppdb';
require_once 'config/database.php';
require_once 'includes/header.php';
?>

<!-- Page Header -->
<section class="page-header-modern page-header-ppdb">
    <div class="page-header-dots"></div>
    <div class="page-header-grid"></div>
    <div class="container">
        <div class="header-breadcrumb" data-aos="fade-down">
            <a href="index.php">Beranda</a>
            <span class="separator">›</span>
            <span>PPDB</span>
        </div>
        <div class="header-icon" data-aos="zoom-in">
            <i class="fa-solid fa-graduation-cap"></i>
        </div>
        <h1 class="header-title" data-aos="fade-up" data-aos-delay="100">PPDB 2026/2027</h1>
        <p class="header-desc" data-aos="fade-up" data-aos-delay="200">Penerimaan Peserta Didik Baru - MI Muhammadiyah Bojongsana</p>
    </div>
</section>
<!-- Info Cards -->
<section class="section-modern section-white">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-6" data-aos="fade-up">
                <div class="card-modern h-100" style="border-left:4px solid var(--primary);">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div style="width:48px;height:48px;background:var(--primary-light);border-radius:12px;display:flex;align-items:center;justify-content:center;color:var(--primary);"><i class="fa-solid fa-clipboard-list"></i></div>
                            <h4>Syarat Pendaftaran</h4>
                        </div>
                        <ul style="color:var(--gray-600);line-height:2;">
                            <li>Memiliki STTB/Ijazah TK/RA</li>
                            <li>Usia minimal <strong>6 tahun</strong> per 1 Juni 2026</li>
                            <li>Mendaftar langsung ke panitia PPDB</li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card-modern h-100" style="border-left:4px solid var(--accent);">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div style="width:48px;height:48px;background:var(--accent-light);border-radius:12px;display:flex;align-items:center;justify-content:center;color:var(--accent);"><i class="fa-solid fa-calendar-days"></i></div>
                            <h4>Waktu Pendaftaran</h4>
                        </div>
                        <div style="color:var(--gray-600);line-height:2;">
                            <p><strong><i class="fa-solid fa-calendar-days"></i> Pendaftaran:</strong> 15 April – 15 Juli 2026</p>
                            <p><strong><i class="fa-solid fa-sync"></i> Daftar Ulang:</strong> 11 Juli 2026</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-up">
                <div class="card-modern h-100" style="border-left:4px solid #059669;">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div style="width:48px;height:48px;background:#ecfdf5;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#059669;"><i class="fa-solid fa-location-dot"></i></div>
                            <h4>Tempat</h4>
                        </div>
                        <p style="color:var(--gray-600);"><strong>MI Muhammadiyah Bojongsana</strong><br>Panusupan RT 02/07, Rembang, Purbalingga</p>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
                <div class="card-modern h-100" style="border-left:4px solid #7c3aed;">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            <div style="width:48px;height:48px;background:#f5f3ff;border-radius:12px;display:flex;align-items:center;justify-content:center;color:#7c3aed;"><i class="fa-solid fa-file-lines"></i></div>
                            <h4>Cara Pendaftaran</h4>
                        </div>
                        <p style="color:var(--gray-600);">Mengisi formulir dan melampirkan: fotokopi ijazah, pas foto 3x4, akta kelahiran, kartu keluarga, KTP orang tua.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="text-center mt-5">
            <a href="form-ppdb.php" class="btn-modern btn-primary-modern btn-lg"><i class="fa-solid fa-pen-to-square me-2"></i>Isi Formulir Pendaftaran</a>
        </div> 
        <p class="text-center text-muted m-2">Atau</p>
                    <div class="text-center mt-2" data-aos="zoom-in">
           <a href="cek-status.php" class="btn-modern btn-outline-modern btn-lg">
                            <i class="fa-solid fa-magnifying-glass me-2"></i> Cek Status pendaftaran 
                        </a>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
