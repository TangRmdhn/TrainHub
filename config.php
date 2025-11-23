<?php


// Deteksi environment berdasarkan hostname
$hostname = $_SERVER['HTTP_HOST'] ?? 'localhost';
$isProduction = ($hostname === 'trainhub.web.id' || $hostname === 'www.trainhub.web.id');

// Set base path
if ($isProduction) {
    define('BASE_PATH', '');
} else {
    define('BASE_PATH', '/trainhub');
}

// Konfigurasi URL API
if ($isProduction) {
    // API Production (Hugging Face Space)
    define('API_URL', 'https://samsas-trainhub.hf.space');
} else {
    // API Development Lokal
    define('API_URL', 'http://127.0.0.1:8000');
}

function url($path)
{
    // Pastiin path dimulai dengan /
    if (substr($path, 0, 1) !== '/') {
        $path = '/' . $path;
    }
    return BASE_PATH . $path;
}

function asset($path)
{
    if (substr($path, 0, 1) !== '/') {
        $path = '/' . $path;
    }
    // Cuma tambahin version query string buat file CSS/JS biar ga break URL API
    if (preg_match('/\.(css|js)$/i', $path)) {
        return BASE_PATH . $path . '?v=' . time();
    }
    return BASE_PATH . $path;
}
