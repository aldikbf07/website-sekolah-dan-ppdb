<?php
$page_title = 'Guru & Staff';
$current_page = 'guru';
require_once 'config/database.php';
require_once 'includes/header.php';

try {
    $pdo = getConnection();
    
    // Ambil data guru dengan jabatan ganda
    $stmt = $pdo->query("SELECT g.*, GROUP_CONCAT(j.nama_jabatan SEPARATOR '||') as jabatan_list 
                         FROM guru g 
                         LEFT JOIN guru_jabatan gj ON g.id = gj.guru_id 
                         LEFT JOIN jabatan j ON gj.jabatan_id = j.id 
                         GROUP BY g.id 
                         ORDER BY 
                            CASE 
                                WHEN GROUP_CONCAT(j.nama_jabatan) LIKE '%Kepala Sekolah%' AND GROUP_CONCAT(j.nama_jabatan) NOT LIKE '%Waka%' THEN 1
                                WHEN GROUP_CONCAT(j.nama_jabatan) LIKE '%Waka%' THEN 2
                                WHEN GROUP_CONCAT(j.nama_jabatan) LIKE '%Guru Kelas%' THEN 3
                                WHEN GROUP_CONCAT(j.nama_jabatan) LIKE '%Guru%' THEN 4
                                ELSE 5
                            END,
                         g.nama ASC");
    $guru_list = $stmt->fetchAll();
    
    // Klasifikasi guru untuk struktur organisasi
    $kepala_sekolah = [];
    $pembina = [];
    $wakil_kepala = [];
    $guru_kelas = [];
    $guru_mapel = [];
    $staff = [];
    
    foreach ($guru_list as $g) {
        $jabatans = !empty($g['jabatan_list']) ? explode('||', $g['jabatan_list']) : [];
        
        $is_kepala = false;
        $is_waka = false;
        $is_kelas = false;
        $is_mapel = false;
        $is_staff = false;
        $is_pembina = false;
        
        foreach ($jabatans as $jab) {
            $jab = trim($jab);
            if (stripos($jab, 'Kepala Sekolah') !== false && stripos($jab, 'Waka') === false) $is_kepala = true;
            if (stripos($jab, 'Waka') !== false || stripos($jab, 'Wakil') !== false) $is_waka = true;
            if (stripos($jab, 'Kelas') !== false) $is_kelas = true;
            if (stripos($jab, 'PJOK') !== false || stripos($jab, 'PAI') !== false || 
                stripos($jab, 'Inggris') !== false || stripos($jab, 'Arab') !== false ||
                stripos($jab, 'Mapel') !== false) $is_mapel = true;
            if (stripos($jab, 'Staff') !== false || stripos($jab, 'TU') !== false || 
                stripos($jab, 'Pustakawan') !== false || stripos($jab, 'Penjaga') !== false) $is_staff = true;
            if (stripos($jab, 'Pembina') !== false || stripos($jab, 'Pelindung') !== false) $is_pembina = true;
        }
        
        if ($is_kepala) $kepala_sekolah[] = $g;
        if ($is_pembina) $pembina[] = $g;
        if ($is_waka) $wakil_kepala[] = $g;
        if ($is_kelas) $guru_kelas[] = $g;
        if ($is_mapel) $guru_mapel[] = $g;
        if ($is_staff) $staff[] = $g;
        
        // Jika tidak masuk kategori manapun, masukkan ke guru_mapel
        if (!$is_kepala && !$is_waka && !$is_kelas && !$is_mapel && !$is_staff && !$is_pembina) {
            if (!empty($g['mapel'])) {
                $guru_mapel[] = $g;
            } else {
                $staff[] = $g;
            }
        }
    }
    
    // Hitung statistik
    $total_guru = count($guru_list);
    $total_kelas = count($guru_kelas);
    $total_mapel = count($guru_mapel);
    $total_staff = count($staff);
    
} catch (PDOException $e) {
    $guru_list = [];
    $kepala_sekolah = [];
    $pembina = [];
    $wakil_kepala = [];
    $guru_kelas = [];
    $guru_mapel = [];
    $staff = [];
    $total_guru = 0;
    $total_kelas = 0;
    $total_mapel = 0;
    $total_staff = 0;
}
?>

<!-- Page Header -->
<section class="page-header-modern page-header-guru">
    <div class="page-header-dots"></div>
    <div class="page-header-grid"></div>
    <div class="container">
        <div class="header-breadcrumb" data-aos="fade-down">
            <a href="index.php">Beranda</a>
            <span class="separator">›</span>
            <span>Guru & Staff</span>
        </div>
        <div class="header-icon" data-aos="zoom-in" data-aos-delay="100">
            <i class="fa-solid fa-chalkboard-user"></i>
        </div>
        <h1 class="header-title" data-aos="fade-up" data-aos-delay="200">Guru & Staff</h1>
        <p class="header-desc" data-aos="fade-up" data-aos-delay="300">Tenaga pendidik profesional MI Muhammadiyah
            Bojongsana</p>
    </div>
</section>

<!-- Struktur Organisasi -->
<section class="section-modern section-white">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-label"><i class="fa-solid fa-sitemap"></i> Struktur</span>
            <h2 class="section-title-modern">Struktur Organisasi</h2>
            <p class="section-desc-modern mx-auto">Struktur kepengurusan MI Muhammadiyah Bojongsana</p>
        </div>

        <div class="org-chart" data-aos="zoom-in">
            <!-- Kepala Sekolah -->
            <div class="org-level">
                <div class="org-card org-card-head">
                    <div class="org-avatar org-avatar-lg">
                        <?php if (!empty($kepala_sekolah) && $kepala_sekolah[0]['foto']): ?>
                        <img src="uploads/guru/<?php echo h($kepala_sekolah[0]['foto']); ?>"
                            alt="<?php echo h($kepala_sekolah[0]['nama']); ?>">
                        <?php else: ?>
                        <i class="fa-solid fa-user-tie"></i>
                        <?php endif; ?>
                    </div>
                    <div class="org-info">
                        <h5><?php echo !empty($kepala_sekolah) ? h($kepala_sekolah[0]['nama']) : 'Kepala Sekolah'; ?>
                        </h5>
                        <span>Kepala Sekolah</span>
                    </div>
                </div>
            </div>

            <!-- Garis Penghubung -->
            <div class="org-connector">
                <div class="org-line-vertical"></div>
                <div class="org-line-horizontal"></div>
            </div>

            <!-- Wakil Kepala -->
            <div class="org-level">
                <div class="org-row">
                    <?php 
                    $waka_default = [
                        ['title' => 'Waka Kurikulum', 'icon' => 'fa-solid fa-book-open', 'color' => '#2563eb'],
                        ['title' => 'Waka Kesiswaan', 'icon' => 'fa-solid fa-users', 'color' => '#059669'],
                        ['title' => 'Waka Sarpras', 'icon' => 'fa-solid fa-building', 'color' => '#c8903e'],
                        ['title' => 'Waka Humas', 'icon' => 'fa-solid fa-handshake', 'color' => '#7c3aed'],
                    ];
                    
                    foreach ($waka_default as $wi => $waka):
                        $found = false;
                        foreach ($wakil_kepala as $wk) {
                            if (stripos($wk['jabatan'] ?? '', str_replace('Waka ', '', $waka['title'])) !== false) {
                                $found = $wk;
                                break;
                            }
                        }
                    ?>
                    <div class="org-card org-card-sub" style="border-top: 4px solid <?php echo $waka['color']; ?>;">
                        <div class="org-avatar"
                            style="background: <?php echo $waka['color']; ?>15; color: <?php echo $waka['color']; ?>;">
                            <?php if ($found && $found['foto']): ?>
                            <img src="uploads/guru/<?php echo h($found['foto']); ?>"
                                alt="<?php echo h($found['nama']); ?>">
                            <?php else: ?>
                            <i class="<?php echo $waka['icon']; ?>"></i>
                            <?php endif; ?>
                        </div>
                        <div class="org-info">
                            <h6><?php echo $found ? h($found['nama']) : $waka['title']; ?></h6>
                            <span><?php echo $waka['title']; ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Garis Penghubung -->
            <div class="org-connector">
                <div class="org-line-vertical"></div>
                <div class="org-line-horizontal"></div>
            </div>

            <!-- Dewan Guru -->
            <div class="org-level">
                <div class="org-card org-card-wide" style="border-top: 4px solid var(--primary);">
                    <div class="org-avatar" style="background: var(--primary-light); color: var(--primary);">
                        <i class="fa-solid fa-users"></i>
                    </div>
                    <div class="org-info">
                        <h6>Dewan Guru & Staff</h6>
                        <span>Tenaga Pendidik & Kependidikan</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistik -->
<?php if ($total_guru > 0): ?>
<section class="section-modern section-gray">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6" data-aos="fade-up">
                <div class="stat-card-mini">
                    <div class="stat-icon-mini" style="background: #2563eb15; color: #2563eb;"><i
                            class="fa-solid fa-users"></i></div>
                    <div class="stat-info-mini">
                        <h4><?php echo $total_guru; ?></h4>
                        <span>Total Guru</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-card-mini">
                    <div class="stat-icon-mini" style="background: #05966915; color: #059669;"><i
                            class="fa-solid fa-chalkboard"></i></div>
                    <div class="stat-info-mini">
                        <h4><?php echo count($guru_kelas); ?></h4>
                        <span>Guru Kelas</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-card-mini">
                    <div class="stat-icon-mini" style="background: #c8903e15; color: #c8903e;"><i
                            class="fa-solid fa-book"></i></div>
                    <div class="stat-info-mini">
                        <h4><?php echo count($guru_mapel); ?></h4>
                        <span>Guru Mapel</span>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-card-mini">
                    <div class="stat-icon-mini" style="background: #7c3aed15; color: #7c3aed;"><i
                            class="fa-solid fa-gear"></i></div>
                    <div class="stat-info-mini">
                        <h4><?php echo count($staff); ?></h4>
                        <span>Staff</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Daftar Guru -->
<section class="section-modern section-white">
    <div class="container">
        <div class="text-center mb-5" data-aos="fade-up">
            <span class="section-label"><i class="fa-solid fa-list"></i> Daftar</span>
            <h2 class="section-title-modern">Daftar Guru & Staff</h2>
            <p class="section-desc-modern mx-auto">Tenaga pendidik dan kependidikan MI Muhammadiyah Bojongsana</p>
        </div>

        <?php if (!empty($guru_list)): ?>

        <!-- Kepala Sekolah -->
        <?php if (!empty($kepala_sekolah)): ?>
        <div class="guru-section-title mb-4" data-aos="fade-up">
            <div class="section-line" style="background: #2563eb;"></div>
            <h3><i class="fa-solid fa-crown me-2" style="color: #2563eb;"></i>Kepala Sekolah</h3>
        </div>
        <div class="row g-4 mb-5">
            <?php foreach ($kepala_sekolah as $g): ?>
            <div class="col-lg-6" data-aos="fade-up">
                <?php echo renderGuruCard($g); ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Wakil Kepala -->
        <?php if (!empty($wakil_kepala)): ?>
        <div class="guru-section-title mb-4" data-aos="fade-up">
            <div class="section-line" style="background: #c8903e;"></div>
            <h3><i class="fa-solid fa-star me-2" style="color: #c8903e;"></i>Wakil Kepala</h3>
        </div>
        <div class="row g-4 mb-5">
            <?php foreach ($wakil_kepala as $g): ?>
            <div class="col-lg-6" data-aos="fade-up">
                <?php echo renderGuruCard($g); ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Guru Kelas -->
        <?php if (!empty($guru_kelas)): ?>
        <div class="guru-section-title mb-4" data-aos="fade-up">
            <div class="section-line" style="background: #059669;"></div>
            <h3><i class="fa-solid fa-chalkboard me-2" style="color: #059669;"></i>Guru Kelas</h3>
        </div>
        <div class="row g-4 mb-5">
            <?php foreach ($guru_kelas as $g): ?>
            <div class="col-lg-6" data-aos="fade-up">
                <?php echo renderGuruCard($g); ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Guru Mapel -->
        <?php if (!empty($guru_mapel)): ?>
        <div class="guru-section-title mb-4" data-aos="fade-up">
            <div class="section-line" style="background: #7c3aed;"></div>
            <h3><i class="fa-solid fa-book-open me-2" style="color: #7c3aed;"></i>Guru Mata Pelajaran</h3>
        </div>
        <div class="row g-4 mb-5">
            <?php foreach ($guru_mapel as $g): ?>
            <div class="col-lg-6" data-aos="fade-up">
                <?php echo renderGuruCard($g); ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Staff -->
        <?php if (!empty($staff)): ?>
        <div class="guru-section-title mb-4" data-aos="fade-up">
            <div class="section-line" style="background: #dc2626;"></div>
            <h3><i class="fa-solid fa-gear me-2" style="color: #dc2626;"></i>Staff & Tenaga Kependidikan</h3>
        </div>
        <div class="row g-4">
            <?php foreach ($staff as $g): ?>
            <div class="col-lg-6" data-aos="fade-up">
                <?php echo renderGuruCard($g); ?>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
        <div class="text-center py-5">
            <i class="fa-solid fa-users fa-3x mb-3" style="color: var(--gray-300);"></i>
            <h4 style="color: var(--gray-400);">Data guru akan segera diperbarui</h4>
        </div>
        <?php endif; ?>

    </div>
</section>

<?php
/**
 * Fungsi untuk render card guru horizontal
 */
function renderGuruCard($g) {
    $jabatans = !empty($g['jabatan_list']) ? explode('||', $g['jabatan_list']) : [];
    
    // Warna untuk jabatan
    $badge_colors = [
        'Kepala Sekolah' => '#2563eb',
        'Waka' => '#c8903e',
        'Kelas' => '#059669',
        'Guru' => '#7c3aed',
        'Staff' => '#dc2626',
        'TU' => '#dc2626',
        'Pustakawan' => '#0284c7',
        'Penjaga' => '#ea580c',
        'Pembina' => '#1a365d',
        'Pelindung' => '#1a365d',
    ];
    
    ob_start();
    ?>
<div class="guru-card-horizontal">
    <!-- Foto -->
    <div class="guru-card-photo">
        <?php if ($g['foto']): ?>
        <img src="uploads/guru/<?php echo h($g['foto']); ?>" alt="<?php echo h($g['nama']); ?>">
        <?php else: ?>
        <div class="guru-card-photo-placeholder">
            <i class="fa-solid fa-user"></i>
        </div>
        <?php endif; ?>
    </div>

    <!-- Info -->
    <div class="guru-card-info">
        <h4 class="guru-card-name"><?php echo h($g['nama']); ?></h4>
        <span class="guru-card-nip">
            <?php if (!empty($g['nip'])): ?>
            NIP. <?php echo h($g['nip']); ?>
            <?php else: ?>
            <span style="color: #94a3b8; font-style: italic;">
                <i class="fa-solid fa-circle-info me-1"></i> Belum memiliki NIP
            </span>
            <?php endif; ?>
        </span >
        <!-- jabatan -->
        <?php if (!empty($jabatans)): ?>
        <div class="guru-card-badges">
            <?php foreach ($jabatans as $jab): 
                    $jab = trim($jab);
                    $color = '#64748b';
                    foreach ($badge_colors as $key => $val) {
                        if (stripos($jab, $key) !== false) { $color = $val; break; }
                    }
                ?>
            <span class="badge-jabatan"
                style="background: <?php echo $color; ?>15; color: <?php echo $color; ?>; border: 1px solid <?php echo $color; ?>30;">
                <?php echo h($jab); ?>
            </span>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Mapel -->
        <?php if (!empty($g['mapel'])): ?>
        <span class="guru-card-mapel"><i class="fa-solid fa-book"></i> <?php echo h($g['mapel']); ?></span>
        <?php endif; ?>
    </div>
</div>
<?php
    return ob_get_clean();
}
?>

<!-- CSS Organisasi & Guru Card -->
<style>
    /* ============ ORGANISASI CHART ============ */
    .org-chart {
        max-width: 900px;
        margin: 0 auto;
        text-align: center;
    }

    .org-level {
        display: flex;
        justify-content: center;
        margin-bottom: 0;
    }

    .org-row {
        display: flex;
        gap: 16px;
        justify-content: center;
        flex-wrap: wrap;
    }

    /* Connector */
    .org-connector {
        display: flex;
        flex-direction: column;
        align-items: center;
        height: 40px;
        position: relative;
    }

    .org-line-vertical {
        width: 3px;
        height: 20px;
        background: var(--gray-300);
        border-radius: 2px;
    }

    .org-line-horizontal {
        width: 60%;
        height: 3px;
        background: var(--gray-300);
        border-radius: 2px;
        margin-top: -1px;
    }

    /* Org Card */
    .org-card {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: 16px;
        padding: 20px;
        box-shadow: var(--shadow-sm);
        display: inline-flex;
        align-items: center;
        gap: 14px;
        transition: all 0.3s ease;
    }

    .org-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-3px);
    }

    .org-card-head {
        min-width: 300px;
    }

    .org-card-sub {
        flex: 1;
        min-width: 200px;
        max-width: 220px;
    }

    .org-card-wide {
        min-width: 280px;
    }

    .org-avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
        overflow: hidden;
    }

    .org-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .org-avatar-lg {
        width: 70px;
        height: 70px;
        font-size: 1.6rem;
    }

    .org-info {
        text-align: left;
    }

    .org-info h5 {
        font-size: 1rem;
        margin-bottom: 2px;
    }

    .org-info h6 {
        font-size: 0.9rem;
        margin-bottom: 2px;
    }

    .org-info span {
        font-size: 0.78rem;
        color: var(--gray-500);
    }

    /* ============ STAT MINI ============ */
    .stat-card-mini {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: 16px;
        padding: 20px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: var(--shadow-xs);
        transition: all 0.3s ease;
    }

    .stat-card-mini:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .stat-icon-mini {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .stat-info-mini h4 {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0;
    }

    .stat-info-mini span {
        font-size: 0.8rem;
        color: var(--gray-500);
        font-weight: 500;
    }

    /* ============ GURU SECTION ============ */
    .guru-section-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 24px;
    }

    .guru-section-icon {
        width: 50px;
        height: 50px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .guru-section-header h3 {
        font-size: 1.3rem;
        margin-bottom: 2px;
    }

    .guru-section-header p {
        color: var(--gray-500);
        font-size: 0.9rem;
        margin: 0;
    }

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

    .guru-card-modern:hover {
        box-shadow: var(--shadow-lg);
        transform: translateY(-5px);
    }

    .guru-card-leader {
        border-top: 5px solid #1a365d !important;
    }

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
        width: 90px;
        height: 90px;
        border-radius: 50%;
        object-fit: cover;
        margin: 0 auto 14px;
        border: 3px solid white;
        box-shadow: 0 0 0 3px var(--gray-200);
    }

    .guru-card-photo-placeholder {
        width: 90px;
        height: 90px;
        border-radius: 50%;
        margin: 0 auto 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.8rem;
    }

    .guru-card-info h4 {
        font-size: 1.05rem;
        margin-bottom: 4px;
    }

    .guru-card-nip {
        display: block;
        font-size: 0.8rem;
        color: var(--gray-400);
        margin-bottom: 6px;
    }

    .guru-card-role {
        display: block;
        font-size: 0.82rem;
        color: var(--gray-500);
        font-weight: 500;
    }

    .guru-card-mapel {
        display: inline-block;
        margin-top: 6px;
        padding: 3px 12px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 500;
        background: var(--primary-light);
        color: var(--primary);
    }

    /* ============ RESPONSIVE ============ */
    @media (max-width: 991px) {
        .org-card-head {
            min-width: auto;
        }

        .org-card-sub {
            max-width: none;
            flex: 1 1 45%;
            min-width: 160px;
        }

        .org-row {
            gap: 10px;
        }
    }

    @media (max-width: 767px) {
        .org-card {
            padding: 14px;
            gap: 10px;
        }

        .org-card-sub {
            flex: 1 1 100%;
        }

        .org-avatar {
            width: 44px;
            height: 44px;
            font-size: 1rem;
        }

        .org-avatar-lg {
            width: 56px;
            height: 56px;
        }

        .guru-card-modern {
            padding: 20px 14px 16px;
        }

        .guru-card-photo,
        .guru-card-photo-placeholder {
            width: 72px;
            height: 72px;
        }
    }

    @media (max-width: 575px) {
        .org-card {
            border-radius: 12px;
        }

        .org-line-horizontal {
            width: 80%;
        }

        .guru-section-header {
            gap: 10px;
        }

        .guru-section-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
            border-radius: 10px;
        }

        .guru-section-header h3 {
            font-size: 1.1rem;
        }
    }

    .guru-section-title {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-line {
        width: 4px;
        height: 28px;
        border-radius: 2px;
    }

    .guru-section-title h3 {
        font-family: var(--font-heading);
        font-weight: 700;
        font-size: 1.2rem;
        margin: 0;
    }

    /* ============ GURU CARD HORIZONTAL ============ */
    .guru-card-horizontal {
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 20px;
        display: flex;
        gap: 20px;
        box-shadow: var(--shadow-xs);
        transition: all 0.3s ease;
        height: 100%;
    }

    .guru-card-horizontal:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    /* Foto - LEBIH BESAR */
    .guru-card-photo {
        width: 120px;
        height: 150px;
        border-radius: 12px;
        overflow: hidden;
        flex-shrink: 0;
        background: #f1f5f9;
    }

    .guru-card-photo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .guru-card-photo-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        color: #94a3b8;
        background: #f1f5f9;
    }

    /* Info */
    .guru-card-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
    }

    .guru-card-name {
        font-family: var(--font-heading);
        font-weight: 700;
        font-size: 1.15rem;
        color: var(--gray-900);
        margin: 0 0 4px;
    }

    .guru-card-nip {
        font-size: 0.82rem;
        color: var(--gray-500);
        display: block;
        margin-bottom: 8px;
    }

    /* Badges Jabatan */
    .guru-card-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        margin-bottom: 8px;
    }

    .badge-jabatan {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 600;
    }

    /* Mapel */
    .guru-card-mapel {
        display: inline-block;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 0.82rem;
        font-weight: 500;
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
        width: fit-content;
    }

    /* ============ RESPONSIVE ============ */
    @media (max-width: 991px) {
        .org-row-4 {
            grid-template-columns: repeat(2, 1fr);
        }

        .org-node-head {
            min-width: auto;
        }

        .guru-card-photo {
            width: 100px;
            height: 130px;
        }
    }

    @media (max-width: 767px) {
        .org-row-4 {
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }

        .org-node {
            padding: 14px;
            gap: 10px;
        }

        .org-node-icon {
            width: 42px;
            height: 42px;
            font-size: 1rem;
            border-radius: 10px;
        }

        .org-node-avatar {
            width: 52px;
            height: 52px;
        }

        .guru-card-horizontal {
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .guru-card-photo {
            width: 120px;
            height: 150px;
        }

        .guru-card-badges {
            justify-content: center;
        }

        .guru-card-mapel {
            margin: 0 auto;
        }
    }

    @media (max-width: 575px) {
        .org-row-4 {
            grid-template-columns: 1fr;
        }

        .org-node-wide {
            min-width: auto;
        }

        .guru-card-photo {
            width: 100px;
            height: 130px;
        }

        .guru-card-name {
            font-size: 1rem;
        }
    }
</style>

<?php require_once 'includes/footer.php'; ?>