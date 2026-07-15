<?php
// Pastikan session dimulai
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debug: cek session (hapus setelah production)
// echo '<pre>SESSION: '; print_r($_SESSION); echo '</pre>';

// Cek apakah ada data pendaftaran
if (!isset($_SESSION['pendaftaran_sukses']) || empty($_SESSION['pendaftaran_sukses'])) {
    // Jika tidak ada session, redirect dengan pesan
    $_SESSION['warning'] = 'Tidak ada data pendaftaran. Silakan daftar terlebih dahulu.';
    header('Location: form-ppdb.php');
    exit();
}

$page_title = 'Pendaftaran Berhasil';
$current_page = 'ppdb';

require_once 'config/database.php';
require_once 'includes/header.php';

$data = $_SESSION['pendaftaran_sukses'];
$no_pendaftaran = $data['no_pendaftaran'] ?? '';
$nama_lengkap = $data['nama_lengkap'] ?? '';
$tempat_lahir = $data['tempat_lahir'] ?? '';
$tanggal_lahir = $data['tanggal_lahir'] ?? '';
$jenis_kelamin = $data['jenis_kelamin'] ?? '';
$agama = $data['agama'] ?? '';
$asal_tk = $data['asal_tk'] ?? '-';
$alamat = $data['alamat'] ?? '';
$kecamatan = $data['kecamatan'] ?? '';
$kabupaten = $data['kabupaten'] ?? '';
$provinsi = $data['provinsi'] ?? '';
$nama_ayah = $data['nama_ayah'] ?? '';
$nama_ibu = $data['nama_ibu'] ?? '';
$no_hp = $data['no_hp'] ?? '';
$tanggal_daftar = $data['tanggal_daftar'] ?? date('Y-m-d H:i:s');
?>

<!-- Page Header -->
<section class="page-header-modern page-header-guru">
    <div class="page-header-dots"></div>
    <div class="page-header-grid"></div>
    <div class="container">
        <div class="header-icon" data-aos="zoom-in" data-aos-delay="100">
            <i class="fa-solid fa-circle-check"></i>
        </div>
        <h1 class="header-title" data-aos="fade-up" data-aos-delay="200">Pendaftaran Berhasil!</h1>
        <p class="header-desc" data-aos="fade-up" data-aos-delay="300">mohon cetak bukti pendaftaran untuk melakukan cek
            status pendaftaran</p>
    </div>
</section>

<!-- Content -->
<!-- Content -->
<section class="section-modern section-white" style="padding-top: 10px;">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">

                <!-- Card Nomor Pendaftaran -->
                <div class="card-modern mb-4" data-aos="fade-up" style="border: 2px solid var(--primary);">
                    <div class="card-body" style="padding: 28px 24px;">

                        <!-- Header -->
                        <div class="text-center mb-4">
                            <div
                                style="width: 52px; height: 52px; background: var(--primary-light); border-radius: 14px; display: inline-flex; align-items: center; justify-content: center; font-size: 1.3rem; color: var(--primary); margin-bottom: 10px;">
                                <i class="fa-solid fa-ticket"></i>
                            </div>
                            <h4 style="font-weight: 700; margin-bottom: 2px; font-size: 1.15rem;">Nomor Pendaftaran Anda
                            </h4>
                            <p style="color: var(--gray-500); font-size: 0.82rem; margin: 0;">
                                Simpan nomor ini untuk cek status dan cetak bukti
                            </p>
                        </div>

                        <!-- Nomor + Tombol Salin -->
                        <div class="no-pendaftaran-container">
                            <div class="no-pendaftaran-box" id="noPendaftaranBox">
                                <i class="fa-solid fa-hashtag"
                                    style="color: var(--primary); font-size: 1.1rem; opacity: 0.5;"></i>
                                <span class="no-pendaftaran-text"
                                    id="noPendaftaran"><?php echo h($no_pendaftaran); ?></span>
                            </div>

                            <button class="btn-salin tooltip-salin" id="btnSalin" onclick="copyNoPendaftaran()">
                                <i class="fa-regular fa-copy"></i>
                                <span id="btnSalinText">Salin Nomor</span>
                                <span class="tooltip-text">Klik untuk menyalin</span>
                            </button>
                            <a href="cetak-bukti.php?no=<?php echo h($no_pendaftaran); ?>" target="_blank"
                                class="btn-modern btn-primary-modern">
                                <i class="fa-solid fa-print me-2"></i> Cetak Bukti Pendaftaran
                            </a>
                        </div>
                        <!-- Info Tambahan -->
                        <div class="text-center mt-3">
                            <small style="color: var(--gray-400);">
                                <i class="fa-solid fa-circle-info me-1"></i>
                                Klik tombol <strong>"Salin Nomor"</strong> atau <strong>double-click</strong> nomor
                                untuk menyalin
                            </small>
                        </div>
                    </div>
                </div>

                <!-- Notifikasi Salin -->
                <div class="copy-notification" id="copyNotification">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>Nomor pendaftaran berhasil disalin!</span>
                </div>

                <!-- Card Detail Pendaftaran -->
                <div class="card-pendaftaran mb-4" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-body" style="padding: 20px 24px;">
                        <div class="success-card-header">
                            <div class="success-card-icon"
                                style="background: var(--accent-light); color: var(--accent);">
                                <i class="fa-solid fa-clipboard-list"></i>
                            </div>
                            <h4 class="success-card-title">Detail Pendaftaran</h4>
                        </div>

                        <div class="detail-grid">
                            <div class="detail-box">
                                <span class="detail-label">Nama Lengkap</span>
                                <span class="detail-value"><?php echo h($nama_lengkap); ?></span>
                            </div>
                            <div class="detail-box">
                                <span class="detail-label">Jenis Kelamin</span>
                                <span class="detail-value"><?php echo h($jenis_kelamin); ?></span>
                            </div>
                            <div class="detail-box">
                                <span class="detail-label">Tempat, Tgl Lahir</span>
                                <span
                                    class="detail-value"><?php echo h($tempat_lahir . ', ' . date('d/m/Y', strtotime($tanggal_lahir))); ?></span>
                            </div>
                            <div class="detail-box">
                                <span class="detail-label">Agama</span>
                                <span class="detail-value"><?php echo h($agama); ?></span>
                            </div>
                            <div class="detail-box">
                                <span class="detail-label">Asal TK/RA</span>
                                <span class="detail-value"><?php echo h($asal_tk); ?></span>
                            </div>
                            <div class="detail-box">
                                <span class="detail-label">No. HP</span>
                                <span class="detail-value"><?php echo h($no_hp); ?></span>
                            </div>
                            <div class="detail-box detail-box-full">
                                <span class="detail-label">Alamat</span>
                                <span
                                    class="detail-value"><?php echo h($alamat . ', ' . $kecamatan . ', ' . $kabupaten); ?></span>
                            </div>
                            <div class="detail-box">
                                <span class="detail-label">Nama Ayah</span>
                                <span class="detail-value"><?php echo h($nama_ayah); ?></span>
                            </div>
                            <div class="detail-box">
                                <span class="detail-label">Nama Ibu</span>
                                <span class="detail-value"><?php echo h($nama_ibu); ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Card Informasi Selanjutnya -->
                <div class="card-pendaftaran mb-4" data-aos="fade-up" data-aos-delay="200"
                    style="border-left: 4px solid var(--primary);">
                    <div class="card-body" style="padding: 20px 24px;">
                        <div class="success-card-header">
                            <div class="success-card-icon"
                                style="background: var(--primary-light); color: var(--primary);">
                                <i class="fa-solid fa-circle-info"></i>
                            </div>
                            <h4 class="success-card-title">Informasi Selanjutnya</h4>
                        </div>

                        <div class="info-steps" style="padding-left: 52px;">
                            <div class="info-step" data-aos="fade-up" data-aos-delay="250">
                                <div class="step-number" style="background: #2563eb;">1</div>
                                <div class="step-content">
                                    <strong>Verifikasi Berkas</strong>
                                    <p>Pendaftaran Anda akan diverifikasi oleh panitia PPDB</p>
                                </div>
                            </div>
                            <div class="info-step" data-aos="fade-up" data-aos-delay="350">
                                <div class="step-number" style="background: #c8903e;">2</div>
                                <div class="step-content">
                                    <strong>Cek Status Secara Berkala</strong>
                                    <p>Gunakan nomor pendaftaran untuk cek status</p>
                                </div>
                            </div>
                            <div class="info-step" data-aos="fade-up" data-aos-delay="450">
                                <div class="step-number" style="background: #059669;">3</div>
                                <div class="step-content">
                                    <strong>Daftar Ulang</strong>
                                    <p>Jika DITERIMA, daftar ulang pada <strong>11 Juli
                                            <?php echo date('Y'); ?></strong></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Di bagian action buttons -->
                <div class="text-center mb-5">
                    <div class="d-flex justify-content-center gap-3 flex-wrap">
                        <a href="cek-status.php" class="btn-modern btn-outline-modern">
                            <i class="fa-solid fa-magnifying-glass me-2"></i> Cek Status
                        </a>
                        <a href="ppdb.php" class="btn-modern"
                            style="background: var(--gray-100); color: var(--gray-600); border: 1px solid var(--gray-300);">
                            <i class="fa-solid fa-arrow-left me-2"></i> Kembali
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<style>
    /* ========================================
   NO PENDAFTARAN DENGAN TOMBOL SALIN
   ======================================== */

    /* Container Nomor Pendaftaran */
    .no-pendaftaran-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        flex-wrap: wrap;
        margin: 12px 0;
    }

    /* Box Nomor */
    .no-pendaftaran-box {
        background: var(--primary-light);
        border: 2px dashed var(--primary);
        border-radius: 12px;
        padding: 16px 24px;
        display: inline-flex;
        align-items: center;
        gap: 12px;
    }

    .no-pendaftaran-text {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: 1.5rem;
        color: var(--primary);
        letter-spacing: 2px;
        white-space: nowrap;
    }

    /* Tombol Salin */
    .btn-salin {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 18px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 10px;
        font-family: 'Outfit', sans-serif;
        font-weight: 600;
        font-size: 0.88rem;
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
        box-shadow: 0 2px 8px rgba(26, 54, 93, 0.2);
    }

    .btn-salin:hover {
        background: #0f2440;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(26, 54, 93, 0.3);
    }

    .btn-salin:active {
        transform: scale(0.95);
    }

    .btn-salin i {
        font-size: 0.9rem;
    }

    /* Tombol Salin - Success State */
    .btn-salin.success {
        background: #059669;
        pointer-events: none;
    }

    .btn-salin.success i {
        animation: popIn 0.3s ease;
    }

    /* Tooltip */
    .tooltip-salin {
        position: relative;
    }

    .tooltip-salin .tooltip-text {
        visibility: hidden;
        width: auto;
        background: #1e293b;
        color: #fff;
        text-align: center;
        border-radius: 8px;
        padding: 8px 14px;
        position: absolute;
        z-index: 10;
        bottom: calc(100% + 10px);
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.82rem;
        font-weight: 500;
        white-space: nowrap;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .tooltip-salin .tooltip-text::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #1e293b transparent transparent transparent;
    }

    .tooltip-salin:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }

    /* Notifikasi Sukses */
    .copy-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #059669;
        color: white;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 8px 25px rgba(5, 150, 105, 0.3);
        z-index: 9999;
        opacity: 0;
        transform: translateX(100px);
        transition: all 0.3s ease;
        pointer-events: none;
    }

    .copy-notification.show {
        opacity: 1;
        transform: translateX(0);
    }

    .copy-notification i {
        font-size: 1.1rem;
    }

    /* ============ RESPONSIVE ============ */
    @media (max-width: 767px) {
        .no-pendaftaran-container {
            flex-direction: column;
            gap: 10px;
        }

        .no-pendaftaran-box {
            width: 100%;
            justify-content: center;
            padding: 14px 18px;
        }

        .no-pendaftaran-text {
            font-size: 1.3rem;
            letter-spacing: 1px;
        }

        .btn-salin {
            width: 100%;
            justify-content: center;
            padding: 12px 18px;
        }

        .copy-notification {
            top: auto;
            bottom: 20px;
            right: 10px;
            left: 10px;
            text-align: center;
            justify-content: center;
        }
    }

    @media (max-width: 575px) {
        .no-pendaftaran-text {
            font-size: 1.1rem;
        }

        .btn-salin {
            font-size: 0.85rem;
            padding: 10px 16px;
        }
    }

    /* Animasi Pop */
    @keyframes popIn {
        0% {
            transform: scale(0.5);
            opacity: 0;
        }

        70% {
            transform: scale(1.2);
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .card-pendaftaran {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-xl);
        overflow: hidden;
        transition: all var(--transition-slow);
        box-shadow: var(--shadow-xs);
    }

    /* ============ COPY BUTTON ============ */
    #copyBtn:hover {
        opacity: 0.8;
    }

    /* ============ INFO STEPS ============ */
    .info-steps {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .info-step {
        display: flex;
        gap: 14px;
        align-items: flex-start;
    }

    .step-number {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    .step-content strong {
        display: block;
        font-size: 0.9rem;
        color: var(--gray-800);
        margin-bottom: 2px;
    }

    .step-content p {
        font-size: 0.85rem;
        color: var(--gray-500);
        margin: 0;
        line-height: 1.5;
    }

    /* ============ PRINT STYLES ============ */
    @media print {

        nav,
        footer,
        .btn-modern,
        .page-header-modern {
            display: none !important;
        }

        body {
            background: white;
            font-size: 12pt;
        }

        .card-modern {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
        }
    }

    /* ============ RESPONSIVE ============ */
    @media (max-width: 767px) {
        .detail-grid {
            grid-template-columns: 1fr;
        }

        .info-steps {
            gap: 12px;
        }

        .step-number {
            width: 28px;
            height: 28px;
            font-size: 0.75rem;
        }

        .step-content strong {
            font-size: 0.85rem;
        }

        .step-content p {
            font-size: 0.8rem;
        }
    }

    @media (max-width: 575px) {
        .card-modern .card-body {
            padding: 20px;
        }
    }

    <style>

    /* ============ DETAIL GRID ============ */
    .detail-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px;
    }

    .detail-box {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: 8px;
        padding: 8px 12px;
    }

    .detail-box-full {
        grid-column: 1 / -1;
    }

    .detail-label {
        display: block;
        font-size: 0.68rem;
        font-weight: 600;
        color: var(--gray-400);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 1px;
    }

    .detail-value {
        display: block;
        font-weight: 600;
        font-size: 0.85rem;
        color: var(--gray-800);
        line-height: 1.3;
    }

    /* ============ INFO STEPS ============ */
    .info-steps {
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .info-step {
        display: flex;
        gap: 10px;
        align-items: flex-start;
    }

    .step-number {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 0.78rem;
        flex-shrink: 0;
    }

    .step-content {
        flex: 1;
    }

    .step-content strong {
        display: block;
        font-size: 0.85rem;
        color: var(--gray-800);
        margin-bottom: 1px;
    }

    .step-content p {
        font-size: 0.8rem;
        color: var(--gray-500);
        margin: 0;
        line-height: 1.4;
    }

    /* ============ NO PENDAFTARAN CARD ============ */
    .no-pendaftaran-card {
        border: 2px solid var(--primary);
    }

    .no-pendaftaran-card .card-body {
        padding: 24px;
    }

    .no-pendaftaran-display {
        background: var(--primary-light);
        border: 2px dashed var(--primary);
        border-radius: 10px;
        padding: 12px 20px;
        display: inline-block;
        margin-bottom: 10px;
    }

    .no-pendaftaran-text {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: 1.4rem;
        color: var(--primary);
        letter-spacing: 2px;
    }

    /* ============ CARD HEADER ============ */
    .success-card-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
        padding-bottom: 14px;
        border-bottom: 1px solid var(--gray-200);
    }

    .success-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        flex-shrink: 0;
    }

    .success-card-title {
        font-weight: 700;
        font-size: 1.1rem;
        margin: 0;
    }

    /* ============ PRINT ============ */
    @media print {

        nav,
        footer,
        .btn-modern,
        .page-header-modern {
            display: none !important;
        }

        body {
            background: white;
        }

        .card-modern {
            box-shadow: none !important;
            border: 1px solid #ddd !important;
            page-break-inside: avoid;
        }
    }

    /* ============ RESPONSIVE ============ */
    @media (max-width: 767px) {
        .detail-grid {
            grid-template-columns: 1fr;
            gap: 6px;
        }

        .detail-box {
            padding: 6px 10px;
        }

        .no-pendaftaran-text {
            font-size: 1.2rem;
        }

        .info-steps {
            gap: 8px;
        }
    }

    @media (max-width: 575px) {
        .no-pendaftaran-card .card-body {
            padding: 16px;
        }

        .no-pendaftaran-display {
            padding: 10px 16px;
        }

        .no-pendaftaran-text {
            font-size: 1.1rem;
            letter-spacing: 1px;
        }
    }

    <style>
    /* ========================================
   NO PENDAFTARAN DENGAN TOMBOL SALIN
   ======================================== */

    /* Container Nomor Pendaftaran */
    .no-pendaftaran-container {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        flex-wrap: wrap;
        margin: 12px 0;
    }

    /* Box Nomor */
    .no-pendaftaran-box {
        background: var(--primary-light);
        border: 2px dashed var(--primary);
        border-radius: 12px;
        padding: 16px 24px;
        display: inline-flex;
        align-items: center;
        gap: 12px;
    }

    .no-pendaftaran-text {
        font-family: var(--font-heading);
        font-weight: 800;
        font-size: 1.5rem;
        color: var(--primary);
        letter-spacing: 2px;
        white-space: nowrap;
    }

    /* Tombol Salin */
    .btn-salin {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 10px 18px;
        background: var(--primary);
        color: white;
        border: none;
        border-radius: 10px;
        font-family: 'Outfit', sans-serif;
        font-weight: 600;
        font-size: 0.88rem;
        cursor: pointer;
        transition: all 0.3s ease;
        white-space: nowrap;
        box-shadow: 0 2px 8px rgba(26, 54, 93, 0.2);
    }

    .btn-salin:hover {
        background: #0f2440;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(26, 54, 93, 0.3);
    }

    .btn-salin:active {
        transform: scale(0.95);
    }

    .btn-salin i {
        font-size: 0.9rem;
    }

    /* Tombol Salin - Success State */
    .btn-salin.success {
        background: #059669;
        pointer-events: none;
    }

    .btn-salin.success i {
        animation: popIn 0.3s ease;
    }

    /* Tooltip */
    .tooltip-salin {
        position: relative;
    }

    .tooltip-salin .tooltip-text {
        visibility: hidden;
        width: auto;
        background: #1e293b;
        color: #fff;
        text-align: center;
        border-radius: 8px;
        padding: 8px 14px;
        position: absolute;
        z-index: 10;
        bottom: calc(100% + 10px);
        left: 50%;
        transform: translateX(-50%);
        font-size: 0.82rem;
        font-weight: 500;
        white-space: nowrap;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .tooltip-salin .tooltip-text::after {
        content: "";
        position: absolute;
        top: 100%;
        left: 50%;
        margin-left: -5px;
        border-width: 5px;
        border-style: solid;
        border-color: #1e293b transparent transparent transparent;
    }

    .tooltip-salin:hover .tooltip-text {
        visibility: visible;
        opacity: 1;
    }

    /* Notifikasi Sukses */
    .copy-notification {
        position: fixed;
        top: 20px;
        right: 20px;
        background: #059669;
        color: white;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        display: flex;
        align-items: center;
        gap: 8px;
        box-shadow: 0 8px 25px rgba(5, 150, 105, 0.3);
        z-index: 9999;
        opacity: 0;
        transform: translateX(100px);
        transition: all 0.3s ease;
        pointer-events: none;
    }

    .copy-notification.show {
        opacity: 1;
        transform: translateX(0);
    }

    .copy-notification i {
        font-size: 1.1rem;
    }

    /* ============ RESPONSIVE ============ */
    @media (max-width: 767px) {
        .no-pendaftaran-container {
            flex-direction: column;
            gap: 10px;
        }

        .no-pendaftaran-box {
            width: 100%;
            justify-content: center;
            padding: 14px 18px;
        }

        .no-pendaftaran-text {
            font-size: 1.3rem;
            letter-spacing: 1px;
        }

        .btn-salin {
            width: 100%;
            justify-content: center;
            padding: 12px 18px;
        }

        .copy-notification {
            top: auto;
            bottom: 20px;
            right: 10px;
            left: 10px;
            text-align: center;
            justify-content: center;
        }
    }

    @media (max-width: 575px) {
        .no-pendaftaran-text {
            font-size: 1.1rem;
        }

        .btn-salin {
            font-size: 0.85rem;
            padding: 10px 16px;
        }
    }

    /* Animasi Pop */
    @keyframes popIn {
        0% {
            transform: scale(0.5);
            opacity: 0;
        }

        70% {
            transform: scale(1.2);
        }

        100% {
            transform: scale(1);
            opacity: 1;
        }
    }
</style>

<script>
    /**
     * Salin Nomor Pendaftaran
     */
    function copyNoPendaftaran() {
        const noPendaftaran = document.getElementById('noPendaftaran').textContent.trim();
        const btnSalin = document.getElementById('btnSalin');
        const btnSalinText = document.getElementById('btnSalinText');
        const notification = document.getElementById('copyNotification');

        // Gunakan Clipboard API
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(noPendaftaran).then(function () {
                showCopySuccess(btnSalin, btnSalinText, notification);
            }).catch(function (err) {
                // Fallback untuk browser lama
                fallbackCopy(noPendaftaran, btnSalin, btnSalinText, notification);
            });
        } else {
            // Fallback untuk browser yang tidak mendukung Clipboard API
            fallbackCopy(noPendaftaran, btnSalin, btnSalinText, notification);
        }
    }

    /**
     * Fallback copy untuk browser lama
     */
    function fallbackCopy(text, btn, btnText, notification) {
        // Buat textarea sementara
        const textarea = document.createElement('textarea');
        textarea.value = text;
        textarea.style.position = 'fixed';
        textarea.style.left = '-9999px';
        textarea.style.top = '-9999px';
        document.body.appendChild(textarea);

        // Select dan copy
        textarea.focus();
        textarea.select();

        try {
            document.execCommand('copy');
            showCopySuccess(btn, btnText, notification);
        } catch (err) {
            alert('Gagal menyalin. Silakan salin manual: ' + text);
        }

        // Hapus textarea
        document.body.removeChild(textarea);
    }

    /**
     * Tampilkan notifikasi sukses
     */
    function showCopySuccess(btn, btnText, notification) {
        // Ubah tombol
        btn.classList.add('success');
        btn.querySelector('i').className = 'fa-solid fa-circle-check';
        btnText.textContent = 'Tersalin!';

        // Tampilkan notifikasi
        notification.classList.add('show');

        // Reset setelah 2 detik
        setTimeout(function () {
            btn.classList.remove('success');
            btn.querySelector('i').className = 'fa-regular fa-copy';
            btnText.textContent = 'Salin Nomor';
            notification.classList.remove('show');
        }, 2000);
    }

    // Copy on double click (opsional)
    document.getElementById('noPendaftaran').addEventListener('dblclick', function () {
        copyNoPendaftaran();
    });
</script>

<?php 
// Hapus session setelah ditampilkan
unset($_SESSION['pendaftaran_sukses']);
require_once 'includes/footer.php'; 
?>