<?php
// File: api/controllers/auth_controller.php

// Panggil autoloader dari Composer
require_once __DIR__ . '/../vendor/autoload.php'; 
use \Firebase\JWT\JWT;

// Fungsi untuk registrasi (udah oke dari file lu)
function registerUser($db_connection) {
    // ... (kode registrasi lu udah bener, pake password_hash) ...
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
        http_response_code(400); 
        echo json_encode(['status' => 'error', 'message' => 'Username, email, dan password tidak boleh kosong.']);
        return;
    }

    $username = $data['username'];
    $email = $data['email'];
    $password = $data['password'];

    $stmt_check = $db_connection->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt_check->bind_param("ss", $email, $username);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        http_response_code(409); 
        echo json_encode(['status' => 'error', 'message' => 'Email atau username sudah terdaftar.']);
        $stmt_check->close();
        return;
    }
    $stmt_check->close();

    $password_hash = password_hash($password, PASSWORD_BCRYPT);

    $stmt_insert = $db_connection->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt_insert->bind_param("sss", $username, $email, $password_hash);

    if ($stmt_insert->execute()) {
        http_response_code(201); 
        echo json_encode(['status' => 'success', 'message' => 'Registrasi berhasil! Silakan login.']);
    } else {
        http_response_code(500); 
        echo json_encode(['status' => 'error', 'message' => 'Gagal mendaftarkan user: ' . $stmt_insert->error]);
    }
    $stmt_insert->close();
}


// Fungsi untuk login (INI YANG DI-UPDATE)
function loginUser($db_connection) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['email']) || empty($data['password'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Email dan password tidak boleh kosong.']);
        return;
    }

    $email = $data['email'];
    $password = $data['password'];

    $stmt = $db_connection->prepare("SELECT id, username, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Email tidak ditemukan.']);
        $stmt->close();
        return;
    }

    $user = $result->fetch_assoc();
    $stmt->close();

    if (password_verify($password, $user['password_hash'])) {
        // === BAGIAN BARU: GENERATE JWT ===
        
        // Kunci rahasia ini HARUS SAMA kayak yang nanti buat verifikasi
        // Simpen di file config/env nanti, jangan di sini
        $secret_key = "KUNCI_RAHASIA_SUPER_AMAN_LU_TANG"; 
        $issuer_claim = "http://localhost/trainhub"; // (opsional)
        $audience_claim = "http://localhost/trainhub"; // (opsional)
        $issuedat_claim = time(); // waktu token dibuat
        $expire_claim = $issuedat_claim + (60 * 60 * 24); // Expired dalam 1 hari

        $payload = [
            'iss' => $issuer_claim,
            'aud' => $audience_claim,
            'iat' => $issuedat_claim,
            'exp' => $expire_claim,
            'data' => [
                'user_id' => $user['id'],
                'username' => $user['username']
            ]
        ];

        // Buat tokennya
        $jwt_token = JWT::encode($payload, $secret_key, 'HS256');

        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => 'Login berhasil!',
            'token' => $jwt_token, // <--- KIRIM TOKEN KE FRONT-END
            'user' => [
                'id' => $user['id'],
                'username' => $user['username']
            ]
        ]);
        // ===================================

    } else {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Password salah.']);
    }
}
?>