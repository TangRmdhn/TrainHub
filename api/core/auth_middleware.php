<?php
// File: api/core/auth_middleware.php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/jwt.php';

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

    try {
        // 3. Decode tokennya
        $decoded = JWT::decode($jwt, new Key(JWT_SECRET_KEY, JWT_ALGORITHM));
        
        // 4. Return user data
        return (array) $decoded->data; 

    } catch (Exception $e) {
        // Token invalid atau expired
        http_response_code(401);
        echo json_encode([
            'status' => 'error', 
            'message' => 'Token tidak valid atau expired: ' . $e->getMessage()
        ]);
        return null;
    }
}
?>