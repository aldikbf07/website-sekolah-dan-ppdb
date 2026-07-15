<?php
require_once '../../config/auth.php';
requireLogin();
require_once '../../config/database.php';
require_once '../../config/wilayah.php';

require_once '../../libs/dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$id) {
    $_SESSION['error'] = 'ID tidak valid';
    header('Location: index.php');
    exit();
}

try {
    $pdo = getConnection();
    
    $stmt = $pdo->prepare("SELECT * FROM pendaftar WHERE id = ? AND status = 'diterima'");
    $stmt->execute([$id]);
    $pendaftar = $stmt->fetch();
    
    if (!$pendaftar) {
        $_SESSION['error'] = 'Pendaftar tidak ditemukan atau belum diterima';
        header('Location: index.php');
        exit();
    }
    
    $no_dokumen = '0425/' . date('Y') . '/' . str_pad($id, 3, '0', STR_PAD_LEFT);
    
    $stmt = $pdo->prepare("SELECT * FROM dokumen_penerimaan WHERE pendaftar_id = ?");
    $stmt->execute([$id]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        $_SESSION['error'] = 'Dokumen sudah ada. No: ' . $existing['no_dokumen'];
        header('Location: detail.php?id=' . $id);
        exit();
    }
    
    $upload_dir = '../../uploads/dokumen/';
    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
    
    // Path logo sekolah
    $logo_path = '../../assets/images/icon/logo_miyasa.png';
    $logo_base64 = '';
    
    // Konversi logo ke base64 jika file ada
    if (file_exists($logo_path)) {
        $logo_data = file_get_contents($logo_path);
        $logo_type = pathinfo($logo_path, PATHINFO_EXTENSION);
        $logo_base64 = 'data:image/' . $logo_type . ';base64,' . base64_encode($logo_data);
    }
    
    // Ambil nama wilayah dari API/cache
    $wilayah = getWilayahNames($pendaftar);
    
    $html = generateSurat($pendaftar, $no_dokumen, $logo_base64, $wilayah);
    
    $options = new Options();
    $options->set('isHtml5ParserEnabled', true);
    $options->set('isRemoteEnabled', true);
    $options->set('defaultFont', 'Times');
    $options->set('isPhpEnabled', true);
    $options->set('dpi', 96);
    
    $dompdf = new Dompdf($options);
    $dompdf->loadHtml($html);
    $dompdf->setPaper('A4', 'portrait');
    $dompdf->render();
    
    $filename = 'Surat_Diterima_' . $id . '_' . time() . '.pdf';
    $filepath = $upload_dir . $filename;
    file_put_contents($filepath, $dompdf->output());
    
    $stmt = $pdo->prepare("INSERT INTO dokumen_penerimaan (pendaftar_id, no_dokumen, file_dokumen) VALUES (?, ?, ?)");
    $stmt->execute([$id, $no_dokumen, $filename]);
    
    $_SESSION['success'] = 'Dokumen PDF berhasil dibuat!';
    header('Location: detail.php?id=' . $id);
    exit();
    
} catch (Exception $e) {
    $_SESSION['error'] = 'Gagal membuat dokumen: ' . $e->getMessage();
    header('Location: index.php');
    exit();
}

/**
 * Generate HTML Surat Keterangan Diterima
 */
function generateSurat($p, $no_dokumen, $logo_base64, $wilayah) {
    $nama_sekolah = 'MI MUHAMMADIYAH BOJONGSANA';
    $status_sekolah = 'MADRASAH IBTIDAIYAH';
    $yayasan = 'MAJELIS PENDIDIKAN DASAR DAN MENENGAH';
    $organisasi = 'PIMPINAN CABANG MUHAMMADIYAH REMBANG';
    $alamat_sekolah = 'Panusupan RT 02/07, Kec. Rembang, Kab. Purbalingga, Kode Pos 53356';
    $telp = '0812-3456-7890';
    $email = 'mimuhammadiyahbojongsana@gmail.com';
    $kepala_sekolah = 'Yatno Spd.I';
    $nip_kepala =  '-';
    $tanggal_surat = date('d F Y');
    $tempat_surat = 'Purbalingga';
    $ttl = $p['tempat_lahir'] . ', ' . date('d F Y', strtotime($p['tanggal_lahir']));
    $tahun_ajaran = date('Y') . '/' . (date('Y') + 1);
    
    // Alamat lengkap dengan nama wilayah
    $alamat_lengkap = $p['alamat'] . ', Kec. ' . $wilayah['kecamatan'] . ', Kab. ' . $wilayah['kabupaten'] . ', Prov. ' . $wilayah['provinsi'];
    
    // Logo HTML
    $logo_html = '';
    if (!empty($logo_base64)) {
        $logo_html = '<img src="' . $logo_base64 . '" alt="Logo" style="width:65px;height:65px;">';
    } else {
        $logo_html = '<div style="width:65px;height:65px;border:2px solid #ccc;border-radius:50%;display:inline-block;text-align:center;font-size:7pt;color:#999;line-height:65px;">LOGO</div>';
    }
    
    $html = '

<!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <title>Surat Keterangan Diterima - ' . h($p['nama_lengkap']) . '</title>
    </head>
    <body>
    <style>
            @page {
                size: A4;
                margin: 20mm 20mm 20mm 25mm;
            }
            
            body { 
                font-family: "Times New Roman", Times, serif; 
                line-height: 1.5; 
                color: #000;
                margin: 0;
                padding: 0;
            }
            
            /* Struktur Kop Surat */
            .table-kop { 
                width: 100%; 
                border-collapse: collapse; 
                border-bottom: 4px double #000; 
                margin-bottom: 20px; 
            }
            .table-kop td { 
                padding: 0; 
                vertical-align: middle; 
            }
            .kop-logo-cell { 
                width: 80px; 
                text-align: left; 
            }
            .kop-logo { 
                height: 75px; 
                width: auto; 
                display: block; 
            }
            .kop-text { 
                text-align: center; 
            }
            .kop-space-cell { 
                width: 80px; 
            }

            /* Tipografi Kop Surat */
            .kop-text .line1 { 
                font-size: 11pt; 
                font-weight: bold; 
                margin: 0; 
                text-transform: uppercase; 
                letter-spacing: 0.3px; 
            }
            .kop-text .line2 { 
                font-size: 11pt; 
                font-weight: bold; 
                margin: 1px 0 0 0; 
                text-transform: uppercase; 
                letter-spacing: 0.3px; 
            }
            .kop-text .line3 { 
                font-size: 14pt; 
                font-weight: bold; 
                margin: 3px 0 0 0; 
                text-transform: uppercase; 
            }
            .kop-text .status { 
                font-size: 10pt; 
                font-weight: bold; 
                margin: 2px 0 0 0; 
                letter-spacing: 0.5px; 
            }
            .kop-text .alamat { 
                font-size: 9pt; 
                font-weight: normal; 
                margin: 3px 0 0 0; 
                line-height: 1.3; 
                color: #000; 
            }

            /* Konten Isi Surat */
            .title-surat { 
                text-align: center; 
                font-weight: bold; 
                font-size: 13pt; 
                text-transform: uppercase; 
                margin-top: 25px; 
                letter-spacing: 0.5px; 
            }
            .nomor-surat { 
                text-align: center; 
                font-weight: bold; 
                font-size: 10pt; 
                margin-bottom: 25px; 
            }
            .pembuka { 
                text-align: justify; 
                font-size: 11pt; 
                line-height: 1.6; 
                margin-bottom: 15px; 
            }
            
            /* Tabel Data Siswa */
            .table-data { 
                margin: 15px 0 20px 30px; 
                border-collapse: collapse; 
                font-size: 11pt; 
                width: 85%; 
            }
            .table-data td { 
                padding: 3px 5px; 
                vertical-align: top; 
            }
            .table-data td.label-data { 
                width: 135px; 
            }
            .table-data td.titik-dua { 
                width: 15px; 
                text-align: center; 
            }

            .pernyataan { 
                font-size: 11pt; 
                text-align: justify; 
                margin-top: 20px; 
                margin-bottom: 25px; 
                line-height: 1.6; 
            }
            .penutup { 
                font-size: 11pt; 
                margin-bottom: 50px; 
            }
            
            /* Bagian Tanda Tangan */
            .ttd-container { 
                float: right; 
                width: 230px; 
                text-align: left; 
                font-size: 11pt; 
                margin-right: 10px; 
            }
            .ttd-container .tanggal { 
                margin-bottom: 2px; 
            }
            .ttd-container .jabatan { 
                margin-bottom: 65px; 
            }
            .ttd-container .nama-ttd {
                font-weight: bold; 
                text-decoration: underline;
            }
            
            .clearfix { clear: both; }
    </style>

        <table class="table-kop">
            <tr>
                <td class="kop-logo-cell">
                    ' . $logo_html . '
                </td>
                
                <td class="kop-text">
                    <div class="line1">MAJELIS PENDIDIKAN DASAR MENENGAH & PENDIDIKAN NONFORMAL</div>
                    <div class="line2">PIMPINAN CABANG MUHAMMADIYAH LOSARI - REMBANG</div>
                    <div class="line3">MADRASAH IBTIDAIYAH MUHAMMADIYAH BOJONGSANA</div>
                    <div class="status">STATUS : TERAKREDITASI B</div>
                    <div class="alamat">
                        Alamat : Bojongsana 02/07 Panusupan, Rembang, Purbalingga 53356<br>
                        NSM. 111233030134, NPSN.60710669 e-mail : miyasa.637@gmail.com<br>
                        Telp. 085325383888
                    </div>
                </td>

                <td class="kop-space-cell"></td>
            </tr>
        </table>

        <div class="title-surat">SURAT KETERANGAN</div>
        <div class="nomor-surat">Nomor : ' . $no_dokumen . '</div>

        <div class="pembuka">
            Berdasarkan kegiatan yang dilaksanakan oleh Panitia Penerimaan Siswa Baru Tahun Pelajaran ' . $tahun_ajaran . ', maka sebagai tindak lanjut hal tersebut kami menetapkan bahwa siswa berikut ini :
        </div>

        <table class="table-data">
            <tr>
                <td class="label-data">Nama Lengkap</td>
                <td class="titik-dua">:</td>
                <td><strong>' . h($p['nama_lengkap']) . '</strong></td>
            </tr>
            <tr>
                <td class="label-data">Tempat, Tgl Lahir</td>
                <td class="titik-dua">:</td>
                <td>' . h($p['tempat_lahir'] . ', ' . date('d F Y', strtotime($p['tanggal_lahir']))) . '</td>
            </tr>
            <tr>
                <td class="label-data">Jenis Kelamin</td>
                <td class="titik-dua">:</td>
                <td>' . h($p['jenis_kelamin']) . '</td>
            </tr>
            <tr>
                <td class="label-data">Asal Sekolah</td>
                <td class="titik-dua">:</td>
                <td>' . h($p['asal_tk'] ?: '-') . '</td>
            </tr>
            <tr>
                <td class="label-data">Alamat</td>
                <td class="titik-dua">:</td>
                <td>' . h($alamat_lengkap) . '</td>
            </tr>
            <tr>
                <td class="label-data">Nama Orang Tua</td>
                <td class="titik-dua">:</td>
                <td>' . h($p['nama_ayah'] . ' / ' . $p['nama_ibu']) . '</td>
            </tr>
            <tr>
                <td class="label-data">No. Pendaftaran</td>
                <td class="titik-dua">:</td>
                <td>' . h($p['no_pendaftaran']) . '</td>
            </tr>
        </table>

        <div class="pernyataan">
            Dinyatakan <strong>" DITERIMA "</strong> sebagai siswa baru di MI Muhammadiyah Bojongsana tahun pelajaran ' . $tahun_ajaran . ' di kelas I (Satu).
        </div>

        <div class="penutup">
            Selamat bergabung dan belajar dengan penuh semangat.
        </div>

        <div class="ttd-container">
            <div class="tanggal">Bojongsana, ' . $tanggal_surat . '</div>
            <div class="jabatan">Kepala Madrasah,</div>
            
            <div class="nama-ttd">' . $kepala_sekolah . '</div>
            <div>NIP. ' . $nip_kepala . '</div>
        </div>
        
        <div class="clearfix"></div>

    </body>
    </html>';
    
    return $html;
}
?>


