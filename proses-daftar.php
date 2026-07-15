<?php
// Pastikan session start di awal
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'config/database.php';

// Cek apakah form di-submit
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: form-ppdb.php');
    exit();
}

$errors = [];
$upload_dir_foto = 'uploads/foto/';
$upload_dir_akta = 'uploads/akta/';
$upload_dir_kk = 'uploads/kk/';

// Buat folder jika belum ada
foreach ([$upload_dir_foto, $upload_dir_akta, $upload_dir_kk] as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Ambil data POST
$data = [
    'nama_lengkap' => trim($_POST['nama_lengkap'] ?? ''),
    'tempat_lahir' => trim($_POST['tempat_lahir'] ?? ''),
    'tanggal_lahir' => trim($_POST['tanggal_lahir'] ?? ''),
    'jenis_kelamin' => trim($_POST['jenis_kelamin'] ?? ''),
    'agama' => trim($_POST['agama'] ?? 'Islam'),
    'anak_ke' => trim($_POST['anak_ke'] ?? ''),
    'status_keluarga' => trim($_POST['status_keluarga'] ?? ''),
    'asal_tk' => trim($_POST['asal_tk'] ?? ''),
    'provinsi' => trim($_POST['provinsi'] ?? ''),
    'kabupaten' => trim($_POST['kabupaten'] ?? ''),
    'kecamatan' => trim($_POST['kecamatan'] ?? ''),
    'alamat' => trim($_POST['alamat'] ?? ''),
    'no_sttb' => trim($_POST['no_sttb'] ?? ''),
    'nama_ayah' => trim($_POST['nama_ayah'] ?? ''),
    'nik_ayah' => trim($_POST['nik_ayah'] ?? ''),
    'nama_ibu' => trim($_POST['nama_ibu'] ?? ''),
    'nik_ibu' => trim($_POST['nik_ibu'] ?? ''),
    'pekerjaan_ayah' => trim($_POST['pekerjaan_ayah'] ?? ''),
    'pekerjaan_ibu' => trim($_POST['pekerjaan_ibu'] ?? ''),
    'alamat_ortu' => trim($_POST['alamat_ortu'] ?? ''),
    'no_hp' => trim($_POST['no_hp'] ?? ''),
];

// Validasi
$required = [
    'nama_lengkap' => 'Nama lengkap',
    'tempat_lahir' => 'Tempat lahir',
    'tanggal_lahir' => 'Tanggal lahir',
    'jenis_kelamin' => 'Jenis kelamin',
    'agama' => 'Agama',
    'provinsi' => 'Provinsi',
    'kabupaten' => 'Kabupaten/Kota',
    'kecamatan' => 'Kecamatan',
    'alamat' => 'Alamat',
    'nama_ayah' => 'Nama ayah',
    'nama_ibu' => 'Nama ibu',
    'no_hp' => 'No HP',
];

foreach ($required as $field => $label) {
    if (empty($data[$field])) {
        $errors[] = "$label wajib diisi";
    }
}

// Validasi upload file
$files = ['file_foto' => $upload_dir_foto, 'file_akta' => $upload_dir_akta, 'file_kk' => $upload_dir_kk];
$file_names = [];

foreach ($files as $field => $upload_dir) {
    if (!isset($_FILES[$field]) || $_FILES[$field]['error'] !== UPLOAD_ERR_OK) {
        $errors[] = "File " . str_replace('file_', '', $field) . " wajib diupload";
    } else {
        $result = uploadFile($_FILES[$field], $upload_dir);
        if ($result['success']) {
            $file_names[$field] = $result['filename'];
        } else {
            $errors[] = $result['error'];
        }
    }
}

// Jika ada error, kembali ke form dengan membawa data POST
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old_data'] = $data; // Simpan data lama
    header('Location: form-ppdb.php');
    exit();
}

// Generate nomor pendaftaran
$no_pendaftaran = 'PPDB-' . date('Y') . '-' . strtoupper(substr(uniqid(), -6));

try {
    $pdo = getConnection();
    $stmt = $pdo->prepare("INSERT INTO pendaftar (
        no_pendaftaran, nama_lengkap, tempat_lahir, tanggal_lahir, jenis_kelamin, agama,
        anak_ke, status_keluarga, asal_tk, provinsi, kabupaten, kecamatan, alamat, no_sttb,
        nama_ayah, nik_ayah, nama_ibu, nik_ibu, pekerjaan_ayah, pekerjaan_ibu, alamat_ortu, no_hp,
        file_foto, file_akta, file_kk
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->execute([
        $no_pendaftaran,
        $data['nama_lengkap'], $data['tempat_lahir'], $data['tanggal_lahir'],
        $data['jenis_kelamin'], $data['agama'], $data['anak_ke'], $data['status_keluarga'],
        $data['asal_tk'], $data['provinsi'], $data['kabupaten'], $data['kecamatan'],
        $data['alamat'], $data['no_sttb'], $data['nama_ayah'], $data['nik_ayah'],
        $data['nama_ibu'], $data['nik_ibu'], $data['pekerjaan_ayah'], $data['pekerjaan_ibu'],
        $data['alamat_ortu'], $data['no_hp'], $file_names['file_foto'],
        $file_names['file_akta'], $file_names['file_kk']
    ]);
    
    // SIMPAN DATA KE SESSION UNTUK HALAMAN SUKSES
    $_SESSION['pendaftaran_sukses'] = [
        'no_pendaftaran' => $no_pendaftaran,
        'nama_lengkap' => $data['nama_lengkap'],
        'tempat_lahir' => $data['tempat_lahir'],
        'tanggal_lahir' => $data['tanggal_lahir'],
        'jenis_kelamin' => $data['jenis_kelamin'],
        'agama' => $data['agama'],
        'asal_tk' => $data['asal_tk'],
        'alamat' => $data['alamat'],
        'kecamatan' => $data['kecamatan'],
        'kabupaten' => $data['kabupaten'],
        'provinsi' => $data['provinsi'],
        'nama_ayah' => $data['nama_ayah'],
        'nama_ibu' => $data['nama_ibu'],
        'no_hp' => $data['no_hp'],
        'tanggal_daftar' => date('Y-m-d H:i:s'),
    ];
    
    // Debug: cek apakah session tersimpan
    error_log('Session pendaftaran_sukses: ' . print_r($_SESSION['pendaftaran_sukses'], true));
    
    // Redirect ke halaman sukses
    header('Location: pendaftaran-sukses.php');
    exit();
    
} catch (PDOException $e) {
    error_log('Error PPDB: ' . $e->getMessage());
    $_SESSION['errors'] = ['Gagal menyimpan data. Silakan coba lagi. Error: ' . $e->getMessage()];
    $_SESSION['old_data'] = $data;
    header('Location: form-ppdb.php');
    exit();
}
?>