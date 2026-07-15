<?php
/**
 * Fungsi Wilayah Indonesia
 * Dengan caching untuk performa
 */

function getWilayahName($type, $id) {
    if (empty($id)) return '-';

    $formatName = function($name) {
        $formatted = mb_convert_case(strtolower($name), MB_CASE_TITLE, 'UTF-8');
        return preg_replace('/^(Kabupaten|Kota)\s+/i', '', $formatted);
    };

    // Jika sudah berupa nama (bukan numeric), return langsung dengan format judul
    if (!is_numeric($id)) return $formatName($id);

    $cache_dir = __DIR__ . '/../cache/wilayah/';
    if (!is_dir($cache_dir)) mkdir($cache_dir, 0755, true);

    $cache_file = $cache_dir . $type . '_' . $id . '.json';
    $cache_time = 86400 * 7; // 7 hari

    // Cek cache
    if (file_exists($cache_file) && (time() - filemtime($cache_file)) < $cache_time) {
        $data = json_decode(file_get_contents($cache_file), true);
        if (!empty($data) && isset($data['name'])) {
            return $formatName($data['name']);
        }
    }
    
    $base_url = 'https://www.emsifa.com/api-wilayah-indonesia/api';
    
    switch ($type) {
        case 'province':
            $url = $base_url . '/province/' . $id . '.json';
            break;
        case 'regency':
            $url = $base_url . '/regency/' . $id . '.json';
            break;
        case 'district':
            $url = $base_url . '/district/' . $id . '.json';
            break;
        case 'village':
            $url = $base_url . '/village/' . $id . '.json';
            break;
        default:
            return $id;
    }
    
    try {
        $context = stream_context_create([
            'http' => [
                'timeout' => 5,
                'method' => 'GET',
                'header' => 'User-Agent: Mozilla/5.0'
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response) {
            $data = json_decode($response, true);
            if ($data && isset($data['name'])) {
                $formattedName = mb_convert_case(strtolower($data['name']), MB_CASE_TITLE, 'UTF-8');
                // Simpan cache
                file_put_contents($cache_file, json_encode(['name' => $formattedName]));
                return $formattedName;
            }
        }
    } catch (Exception $e) {
        // Return ID jika gagal
    }
    
    return $id;
}

/**
 * Batch get wilayah names
 */
function getWilayahNames($pendaftar) {
    $kecamatan = getWilayahName('district', $pendaftar['kecamatan']);
    $kabupaten = getWilayahName('regency', $pendaftar['kabupaten']);
    $provinsi = getWilayahName('province', $pendaftar['provinsi']);
    
    return [
        'kecamatan' => $kecamatan,
        'kabupaten' => $kabupaten,
        'provinsi' => $provinsi,
    ];
}
?>