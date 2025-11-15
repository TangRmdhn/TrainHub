<?php
// File: api/middleware/auth_middleware.php

require_once __DIR__ . '/../vendor/autoload.php';
use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

function checkAuth() {
    // 1. Ambil header Authorization
    $headers = apache_request_headers();
    $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? null;

    if (!$authHeader) {
        http_response_code(401); // Unauthorized
        echo json_encode(['status' => 'error', 'message' => 'Token tidak ditemukan.']);
        return null;
    }

    // 2. Cek format "Bearer [token]"
    if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        $jwt = $matches[1];
    } else {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Format token salah.']);
        return null;
    }

    // 3. (PENTING) Kunci ini HARUS SAMA kayak di auth_controller.php
    $secret_key = "KUNCI_RAHASIA_SUPER_AMAN_LU_TANG";

    try {
        // 4. Decode tokennya
        $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
        
        // 5. Kalo berhasil, kirim data user-nya (terutama ID)
        return (array) $decoded->data; 

    } catch (Exception $e) {
        // Kalo token-nya expired atau palsu
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Token tidak valid atau expired: ' . $e->getMessage()]);
        return null;
    }
}
?>