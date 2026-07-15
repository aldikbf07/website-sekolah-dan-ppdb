<?php
require_once 'config/database.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$no_daftar = isset($_GET['no']) ? trim($_GET['no']) : '';

if (!$id && !$no_daftar) {
    die('Parameter tidak valid.');
}

try {
    $pdo = getConnection();
    
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM pendaftar WHERE id = ?");
        $stmt->execute([$id]);
    } else {
        $stmt = $pdo->prepare("SELECT * FROM pendaftar WHERE no_pendaftaran = ?");
        $stmt->execute([$no_daftar]);
    }
    
    $data = $stmt->fetch();
    
    if (!$data) {
        die('Data pendaftar tidak ditemukan.');
    }
    
} catch (PDOException $e) {
    die('Terjadi kesalahan database.');
}

// Data sekolah
$nama_sekolah = 'MI MUHAMMADIYAH BOJONGSANA';
$alamat_sekolah = 'Panusupan RT 02/07, Kec. Rembang, Kab. Purbalingga, Jawa Tengah 53356';
$telp_sekolah = '0812-3456-7890';
$email_sekolah = 'mimuhammadiyahbojongsana@gmail.com';
$tahun_ajaran = date('Y') . '/' . (date('Y') + 1);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Bukti Pendaftaran - <?php echo h($data['no_pendaftaran']); ?></title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700&family=Raleway:wght@600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary: #1a5f3a;
            --primary-light: #e8f5e9;
        }
        
        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Outfit', sans-serif;
            background: #f1f5f9;
            padding: 30px 20px;
            color: #1e293b;
        }
        
        /* ============ PRINT STYLES ============ */
        @media print {
            body {
                background: white;
                padding: 0;
                margin: 0;
            }
            
            .no-print { display: none !important; }
            
            .bukti-container {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
                border-radius: 0 !important;
                max-width: 100% !important;
                margin: 0 !important;
                page-break-inside: avoid;
            }
            
            @page {
                size: A4;
                margin: 1.5cm;
            }
        }
        
        /* ============ SCREEN STYLES ============ */
        .bukti-container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.08);
            overflow: hidden;
        }
        
        /* Header */
        .bukti-header {
            background: linear-gradient(135deg, #1a5f3a, #2d8a4e);
            padding: 30px 35px;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }
        
        .bukti-header::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        
        .bukti-header::after {
            content: '';
            position: absolute;
            bottom: -30px;
            left: -30px;
            width: 100px;
            height: 100px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }
        
        .bukti-logo {
            width: 60px;
            height: 60px;
            background: rgba(255,255,255,0.2);
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 10px;
            border: 2px solid rgba(255,255,255,0.3);
        }
        
        .bukti-header h4 {
            font-family: 'Raleway', sans-serif;
            font-weight: 700;
            font-size: 1.1rem;
            margin: 0 0 2px;
            color: white;
        }
        
        .bukti-header p {
            font-size: 0.8rem;
            color: rgba(255,255,255,0.8);
            margin: 0;
        }
        
        .bukti-header .school-contact {
            font-size: 0.75rem;
            color: rgba(255,255,255,0.7);
            margin-top: 6px;
        }
        
        /* Title */
        .bukti-title-section {
            padding: 20px 35px;
            text-align: center;
            border-bottom: 2px dashed #e2e8f0;
        }
        
        .bukti-title {
            font-family: 'Raleway', sans-serif;
            font-weight: 800;
            font-size: 1.3rem;
            color: #1a5f3a;
            letter-spacing: 1px;
            margin: 0;
        }
        
        .bukti-subtitle {
            font-size: 0.85rem;
            color: #64748b;
            margin: 4px 0 0;
        }
        
        /* Body */
        .bukti-body {
            padding: 25px 35px;
        }
        
        .bukti-row {
            display: flex;
            gap: 25px;
        }
        
        .bukti-main {
            flex: 1;
        }
        
        .bukti-side {
            width: 140px;
            flex-shrink: 0;
            text-align: center;
        }
        
        /* Info Table */
        .info-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .info-table td {
            padding: 7px 0;
            font-size: 0.9rem;
            vertical-align: top;
        }
        
        .info-table .info-label {
            width: 38%;
            color: #64748b;
            font-weight: 500;
        }
        
        .info-table .info-separator {
            width: 5%;
            text-align: center;
            color: #94a3b8;
        }
        
        .info-table .info-value {
            font-weight: 600;
            color: #1e293b;
        }
        
        /* No Pendaftaran */
        .no-pendaftaran-box {
            background: var(--primary-light);
            border: 2px dashed #1a5f3a;
            border-radius: 10px;
            padding: 10px 16px;
            text-align: center;
            margin-bottom: 20px;
        }
        
        .no-pendaftaran-box .no-label {
            font-size: 0.75rem;
            color: #64748b;
            font-weight: 600;
            letter-spacing: 1px;
            text-transform: uppercase;
            display: block;
            margin-bottom: 2px;
        }
        
        .no-pendaftaran-box .no-value {
            font-family: 'Raleway', sans-serif;
            font-weight: 800;
            font-size: 1.3rem;
            color: #1a5f3a;
            letter-spacing: 1px;
        }
        
        /* Foto */
        .foto-preview {
            width: 100px;
            height: 130px;
            border: 2px solid #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            display: inline-block;
            background: #f8fafc;
        }
        
        .foto-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .foto-preview .foto-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            color: #cbd5e1;
        }
        
        .foto-label {
            font-size: 0.75rem;
            color: #64748b;
            margin-top: 6px;
        }
        
        /* Footer */
        .bukti-footer {
            padding: 20px 35px;
            border-top: 2px dashed #e2e8f0;
        }
        
        .footer-note {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.82rem;
            color: #92400e;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .footer-note i {
            font-size: 1.1rem;
            flex-shrink: 0;
        }
        
        .ttd-section {
            display: flex;
            justify-content: space-between;
        }
        
        .ttd-kiri {
            font-size: 0.85rem;
            color: #64748b;
        }
        
        .ttd-kanan {
            text-align: center;
            font-size: 0.85rem;
        }
        
        .ttd-kanan .ttd-nama {
            font-weight: 700;
            margin-top: 50px;
        }
        
        .ttd-kanan .ttd-jabatan {
            font-size: 0.8rem;
            color: #64748b;
        }
        
        /* Buttons */
        .btn-cetak {
            background: #1a5f3a;
            color: white;
            border: none;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        
        .btn-cetak:hover {
            background: #14532d;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(26,95,58,0.3);
        }
        
        .btn-back {
            background: white;
            color: #475569;
            border: 1px solid #e2e8f0;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }
        
        .btn-back:hover {
            background: #f8fafc;
            color: #1e293b;
        }
        
        /* ============ RESPONSIVE ============ */
        @media (max-width: 767px) {
            body { padding: 10px; }
            
            .bukti-header { padding: 20px; }
            .bukti-title-section { padding: 15px 20px; }
            .bukti-body { padding: 20px; }
            .bukti-footer { padding: 15px 20px; }
            
            .bukti-row { flex-direction: column; }
            .bukti-side { width: 100%; margin-bottom: 16px; }
            .foto-preview { width: 80px; height: 100px; }
            
            .bukti-title { font-size: 1.1rem; }
            .info-table td { font-size: 0.82rem; }
            .no-pendaftaran-box .no-value { font-size: 1.1rem; }
            
            .ttd-section { flex-direction: column; gap: 30px; }
            .ttd-kanan .ttd-nama { margin-top: 30px; }
        }
    </style>
</head>
<body>
    
    <!-- Action Buttons - Screen Only -->
    <div class="text-center mb-4 no-print">
        <div class="d-inline-flex gap-3">
            <button class="btn-cetak" onclick="window.print()">
                <i class="fa-solid fa-print"></i> Cetak Bukti
            </button>
            <a href="cek-status.php" class="btn-back">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
        </div>
    </div>
    
    <!-- Bukti Container -->
    <div class="bukti-container">
        
        <!-- Header Sekolah -->
        <div class="bukti-header">
            <img src="assets/images/icon/logo_miyasa.png" alt="logo sekolah" style="width: 60px; height: 60px;" onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fa-solid fa-school\'></i>';" class="mb-2">
            <h4><?php echo $nama_sekolah; ?></h4>
            <p><?php echo $alamat_sekolah; ?></p>
            <p class="school-contact">Telp: <?php echo $telp_sekolah; ?> | Email: <?php echo $email_sekolah; ?></p>
        </div>
        
        <!-- Judul -->
        <div class="bukti-title-section">
            <h2 class="bukti-title">BUKTI PENDAFTARAN PPDB</h2>
            <p class="bukti-subtitle">Tahun Ajaran <?php echo $tahun_ajaran; ?></p>
        </div>
        
        <!-- Body -->
        <div class="bukti-body">
            
            <!-- No Pendaftaran -->
            <div class="no-pendaftaran-box">
                <span class="no-label">Nomor Pendaftaran</span>
                <span class="no-value"><?php echo h($data['no_pendaftaran']); ?></span>
            </div>
            
            <div class="bukti-row">
                <!-- Data Utama -->
                <div class="bukti-main">
                    <table class="info-table">
                        <tr>
                            <td class="info-label">Nama Lengkap</td>
                            <td class="info-separator">:</td>
                            <td class="info-value"><?php echo h($data['nama_lengkap']); ?></td>
                        </tr>
                        <tr>
                            <td class="info-label">Tempat, Tgl Lahir</td>
                            <td class="info-separator">:</td>
                            <td class="info-value"><?php echo h($data['tempat_lahir'] . ', ' . date('d F Y', strtotime($data['tanggal_lahir']))); ?></td>
                        </tr>
                        <tr>
                            <td class="info-label">Jenis Kelamin</td>
                            <td class="info-separator">:</td>
                            <td class="info-value"><?php echo h($data['jenis_kelamin']); ?></td>
                        </tr>
                        <tr>
                            <td class="info-label">Agama</td>
                            <td class="info-separator">:</td>
                            <td class="info-value"><?php echo h($data['agama']); ?></td>
                        </tr>
                        <tr>
                            <td class="info-label">Asal TK/RA</td>
                            <td class="info-separator">:</td>
                            <td class="info-value"><?php echo h($data['asal_tk'] ?: '-'); ?></td>
                        </tr>
                        <tr>
                            <td class="info-label">Nama Orang Tua</td>
                            <td class="info-separator">:</td>
                            <td class="info-value"><?php echo h($data['nama_ayah'] . ' / ' . $data['nama_ibu']); ?></td>
                        </tr>
                        <tr>
                            <td class="info-label">Nomor HP</td>
                            <td class="info-separator">:</td>
                            <td class="info-value"><?php echo h($data['no_hp']); ?></td>
                        </tr>
                        <tr>
                            <td class="info-label">Tanggal Daftar</td>
                            <td class="info-separator">:</td>
                            <td class="info-value"><?php echo date('d F Y H:i', strtotime($data['created_at'])); ?></td>
                        </tr>
                    </table>
                </div>
                
                <!-- Foto -->
                <div class="bukti-side">
                    <div class="foto-preview">
                        <?php if ($data['file_foto']): ?>
                        <img src="uploads/foto/<?php echo h($data['file_foto']); ?>" alt="Foto" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="foto-placeholder" style="display: none;"><i class="fa-solid fa-user"></i></div>
                        <?php else: ?>
                        <div class="foto-placeholder"><i class="fa-solid fa-user"></i></div>
                        <?php endif; ?>
                    </div>
                    <p class="foto-label">Pas Foto</p>
                </div>
            </div>
        </div>
        
        <!-- Footer -->
        <div class="bukti-footer">
            <div class="footer-note">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span><strong>Penting:</strong> Simpan bukti pendaftaran ini sebagai syarat verifikasi dan daftar ulang.</span>
            </div>
            
            <div class="ttd-section">
                <div class="ttd-kiri">
                    <p>Dicetak pada: <?php echo date('d F Y H:i'); ?></p>
                    <p>Oleh: Sistem PPDB Online</p>
                </div>
                <div class="ttd-kanan">
                    <p class="ttd-nama">Panitia PPDB</p>
                    <p class="ttd-jabatan">MI Muhammadiyah Bojongsana</p>
                </div>
            </div>
        </div>
        
    </div>
    
    <!-- Bottom Buttons -->
    <div class="text-center mt-4 no-print">
        <button class="btn-cetak" onclick="window.print()">
            <i class="fa-solid fa-print"></i> Cetak Bukti
        </button>
    </div>
    
</body>
</html>