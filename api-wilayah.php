<?php
/**
 * API Proxy Wilayah Indonesia
 * Menggunakan API dari emsifa.com/api-wilayah-indonesia
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$type = $_GET['type'] ?? '';
$id = $_GET['id'] ?? '';

// Cache sederhana (opsional, bisa menggunakan file cache)
$cache_dir = __DIR__ . '/cache/';
if (!is_dir($cache_dir)) mkdir($cache_dir, 0755, true);

$cache_file = $cache_dir . md5($type . $id) . '.json';
$cache_time = 86400; // 24 jam

// Cek cache
if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
    echo file_get_contents($cache_file);
    exit();
}

$base_url = 'https://www.emsifa.com/api-wilayah-indonesia/api';

try {
    switch ($type) {
        case 'provinces':
            $url = $base_url . '/provinces.json';
            break;
        case 'regencies':
            $url = $base_url . '/regencies/' . $id . '.json';
            break;
        case 'districts':
            $url = $base_url . '/districts/' . $id . '.json';
            break;
        case 'villages':
            $url = $base_url . '/villages/' . $id . '.json';
            break;
        default:
            echo json_encode(['error' => 'Invalid type']);
            exit();
    }
    
    $response = @file_get_contents($url);
    
    if ($response === false) {
        // Fallback ke data lokal jika API gagal
        $fallback = getFallbackData($type, $id);
        echo json_encode($fallback);
    } else {
        // Simpan cache
        file_put_contents($cache_file, $response);
        echo $response;
    }
    
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

/**
 * Fallback data jika API tidak tersedia
 */
function getFallbackData($type, $id) {
    $data = [
        'provinces' => [
            ['id' => '33', 'name' => 'JAWA TENGAH'],
            ['id' => '32', 'name' => 'JAWA BARAT'],
            ['id' => '34', 'name' => 'DAERAH ISTIMEWA YOGYAKARTA'],
            ['id' => '35', 'name' => 'JAWA TIMUR'],
            ['id' => '36', 'name' => 'BANTEN'],
            ['id' => '31', 'name' => 'DKI JAKARTA'],
        ],
        'regencies_33' => [
            ['id' => '3301', 'name' => 'KABUPATEN CILACAP'],
            ['id' => '3302', 'name' => 'KABUPATEN BANYUMAS'],
            ['id' => '3303', 'name' => 'KABUPATEN PURBALINGGA'],
            ['id' => '3304', 'name' => 'KABUPATEN BANJARNEGARA'],
            ['id' => '3305', 'name' => 'KABUPATEN KEBUMEN'],
        ],
        'districts_3303' => [
            ['id' => '330301', 'name' => 'Kemangkon'],
            ['id' => '330302', 'name' => 'Bukateja'],
            ['id' => '330303', 'name' => 'Kejobong'],
            ['id' => '330304', 'name' => 'Pengadegan'],
            ['id' => '330305', 'name' => 'Karangjambu'],
            ['id' => '330306', 'name' => 'Kaligondang'],
            ['id' => '330307', 'name' => 'Purbalingga'],
            ['id' => '330308', 'name' => 'Kalimanah'],
            ['id' => '330309', 'name' => 'Padamara'],
            ['id' => '330310', 'name' => 'Kutasari'],
            ['id' => '330311', 'name' => 'Bojongsari'],
            ['id' => '330312', 'name' => 'Mrebet'],
            ['id' => '330313', 'name' => 'Bobotsari'],
            ['id' => '330314', 'name' => 'Kertanegara'],
            ['id' => '330315', 'name' => 'Karangreja'],
            ['id' => '330316', 'name' => 'Karanganyar'],
            ['id' => '330317', 'name' => 'Karangmoncol'],
            ['id' => '330318', 'name' => 'Rembang'],
        ],
    ];
    
    if ($type == 'provinces') return $data['provinces'];
    if ($type == 'regencies' && $id == '33') return $data['regencies_33'];
    if ($type == 'districts' && $id == '3303') return $data['districts_3303'];
    
    return [];
}

?>