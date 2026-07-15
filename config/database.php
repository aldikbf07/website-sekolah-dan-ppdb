<?php
/**
 * Konfigurasi Database
 * MI Muhammadiyah Bojongsana
 */

// Konfigurasi database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'db_sekolah_mi');

/**
 * Membuat koneksi database dengan PDO
 * @return PDO
 */
function getConnection() {
    static $pdo = null;
    
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die("Koneksi database gagal: " . $e->getMessage());
        }
    }
    
    return $pdo;
}

/**
 * Fungsi helper untuk sanitasi output
 * @param string $string
 * @return string
 */
function h($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Fungsi untuk mendapatkan pengaturan dari database
 * @param string $key
 * @return string
 */
function getPengaturan($key) {
    try {
        $pdo = getConnection();
        $stmt = $pdo->prepare("SELECT value FROM pengaturan WHERE `key` = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['value'] : '';
    } catch (PDOException $e) {
        return '';
    }
}

/**
 * Fungsi untuk format tanggal Indonesia
 * @param string $date
 * @return string
 */
function formatTanggal($date) {
    if (empty($date)) return '';
    
    $bulan = [
        1 => 'Januari', 
        2 => 'Februari', 
        3 => 'Maret', 
        4 => 'April', 
        5 => 'Mei', 
        6 => 'Juni',
        7 => 'Juli', 
        8 => 'Agustus', 
        9 => 'September', 
        10 => 'Oktober', 
        11 => 'November', 
        12 => 'Desember'
    ];
    
    $timestamp = strtotime($date);
    if (!$timestamp) return $date; // Return original if invalid date
    
    $tanggal = date('j', $timestamp);
    $bulan_index = (int)date('n', $timestamp);
    $tahun = date('Y', $timestamp);
    
    return $tanggal . ' ' . $bulan[$bulan_index] . ' ' . $tahun;
}

/**
 * Fungsi untuk upload file
 * @param array $file
 * @param string $destination
 * @param array $allowed_types
 * @param int $max_size
 * @return array
 */
function uploadFile($file, $destination, $allowed_types = ['image/jpeg', 'image/png', 'image/gif'], $max_size = 2097152) {
    $result = [
        'success' => false,
        'filename' => '',
        'error' => ''
    ];
    
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        $result['success'] = true; // No file is okay for optional uploads
        return $result;
    }
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $result['error'] = 'Error uploading file. Error code: ' . $file['error'];
        return $result;
    }
    
    // Validasi tipe file
    $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
    $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    
    if (!in_array($file_extension, $allowed_extensions)) {
        $result['error'] = 'Tipe file tidak diizinkan. Hanya JPG, PNG, dan GIF';
        return $result;
    }
    
    // Validasi ukuran
    if ($file['size'] > $max_size) {
        $result['error'] = 'Ukuran file terlalu besar. Maksimal 2MB';
        return $result;
    }
    
    // Generate nama file unik
    $filename = uniqid() . '_' . time() . '.' . $file_extension;
    $full_path = rtrim($destination, '/') . '/' . $filename;
    
    // Buat folder jika belum ada
    if (!is_dir($destination)) {
        mkdir($destination, 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $full_path)) {
        $result['success'] = true;
        $result['filename'] = $filename;
    } else {
        $result['error'] = 'Gagal menyimpan file';
    }
    
    return $result;
}

/**
 * Fungsi untuk mendapatkan base URL
 * @return string
 */
function getBaseUrl() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $script = dirname($_SERVER['SCRIPT_NAME']);
    return rtrim($protocol . $host . $script, '/');
}

/**
 * Fungsi untuk redirect
 * @param string $url
 */
function redirect($url) {
    header('Location: ' . $url);
    exit();
}

/**
 * Fungsi untuk menampilkan pesan flash
 */
function setFlash($type, $message) {
    $_SESSION['flash_' . $type] = $message;
}

function getFlash($type) {
    if (isset($_SESSION['flash_' . $type])) {
        $message = $_SESSION['flash_' . $type];
        unset($_SESSION['flash_' . $type]);
        return $message;
    }
    return null;
}
?>