<?php
// ============================================
// TrainHub - Authentication Controller
// ============================================
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/jwt.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

// === REGISTER ===
function registerUser($db_connection) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validation
    if (empty($data['username']) || empty($data['email']) || empty($data['password'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Semua field harus diisi.']);
        return;
    }
    
    $username = trim($data['username']);
    $email = trim($data['email']);
    $password = $data['password'];
    
    // Validate
    if (strlen($username) < 3) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Username minimal 3 karakter.']);
        return;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Format email tidak valid.']);
        return;
    }
    
    if (strlen($password) < 6) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Password minimal 6 karakter.']);
        return;
    }
    
    // Check duplicate
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
    
    // Hash password
    $password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    
    // Insert user
    $stmt_insert = $db_connection->prepare("INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)");
    $stmt_insert->bind_param("sss", $username, $email, $password_hash);
    
    if ($stmt_insert->execute()) {
        http_response_code(201);
        echo json_encode([
            'status' => 'success',
            'message' => 'Registrasi berhasil! Silakan login.',
            'user' => [
                'id' => $stmt_insert->insert_id,
                'username' => $username,
                'email' => $email
            ]
        ]);
        error_log("New user registered: {$username} ({$email})");
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal mendaftarkan user.']);
    }
    
    $stmt_insert->close();
}

// === LOGIN ===
function loginUser($db_connection) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Validation
    if (empty($data['email']) || empty($data['password'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Email dan password harus diisi.']);
        return;
    }
    
    $email = trim($data['email']);
    $password = $data['password'];
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Format email tidak valid.']);
        return;
    }
    
    // Find user
    $stmt = $db_connection->prepare("SELECT id, username, email, password_hash FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Email atau password salah.']);
        $stmt->close();
        error_log("Failed login attempt for: {$email}");
        return;
    }
    
    $user = $result->fetch_assoc();
    $stmt->close();
    
    // Verify password
    if (!password_verify($password, $user['password_hash'])) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => 'Email atau password salah.']);
        error_log("Wrong password for user: {$user['username']}");
        return;
    }
    
    // Generate JWT
    $issuedat_claim = time();
    $expire_claim = $issuedat_claim + JWT_EXPIRATION;
    
    $payload = [
        'iss' => $_SERVER['HTTP_HOST'] ?? 'localhost',
        'aud' => $_SERVER['HTTP_HOST'] ?? 'localhost',
        'iat' => $issuedat_claim,
        'exp' => $expire_claim,
        'data' => [
            'user_id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email']
        ]
    ];
    
    try {
        $jwt_token = JWT::encode($payload, JWT_SECRET_KEY, JWT_ALGORITHM);
        
        http_response_code(200);
        echo json_encode([
            'status' => 'success',
            'message' => 'Login berhasil!',
            'token' => $jwt_token,
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email']
            ],
            'token_expires_at' => date('Y-m-d H:i:s', $expire_claim)
        ]);
        
        error_log("User logged in: {$user['username']}");
        
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal membuat token.']);
        error_log("JWT encoding error: " . $e->getMessage());
    }
}

// === VALIDATE TOKEN ===
function validateToken($token) {
    try {
        $decoded = JWT::decode($token, new Key(JWT_SECRET_KEY, JWT_ALGORITHM));
        return (array) $decoded->data;
    } catch (Exception $e) {
        error_log("JWT validation error: " . $e->getMessage());
        return false;
    }
}
?>