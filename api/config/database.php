<?php
// File: api/config/database.php

// 1. Detail Koneksi Database Lokal (XAMPP/MAMP)
$db_host = "localhost";  // atau "localhost"
$db_user = "root";        // User default XAMPP
$db_pass = "";            // Password default XAMPP (biasanya kosong)
$db_name = "trainhub_db"; // Nama DB yang barusan lu buat

// 2. Buat Koneksi pake MySQLi (Modern)
$db_connection = new mysqli($db_host, $db_user, $db_pass, $db_name);

// 3. Cek Koneksi (WAJIB!)
if ($db_connection->connect_error) {
    // Jika koneksi GAGAL, hentikan script dan kasih pesan error
    
    // Kirim response error sebagai JSON
    http_response_code(500); // Internal Server Error
    header('Content-Type: application/json');
    
    echo json_encode([
        'status' => 'error',
        'message' => 'Koneksi ke database GAGAL.',
        'error_detail' => $db_connection->connect_error
    ]);
    
    die(); // Matikan script
    
} else {
    // Jika KONEK, pastikan format datanya bener (biar
    // emoji dan teks aneh ga rusak)
    $db_connection->set_charset("utf8mb4");
}

// Catatan: 
// Variabel $db_connection ini nanti bakal di-'require' 
// atau di-'include' di file api/index.php atau di controller lu.
?>