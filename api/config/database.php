<?php
// ============================================
// TrainHub - Database Configuration
// Modern MySQLi connection with error handling
// ============================================

// === ENVIRONMENT DETECTION ===
// Auto-detect development vs production
$is_development = ($_SERVER['SERVER_NAME'] === 'localhost' || $_SERVER['SERVER_NAME'] === '127.0.0.1');

// === DATABASE CREDENTIALS ===
if ($is_development) {
    // Development (XAMPP/MAMP/Local)
    $db_host = "localhost";
    $db_user = "root";
    $db_pass = ""; // Kosong untuk XAMPP default
    $db_name = "trainhub_db";
    $db_port = 3306; // Default MySQL port
} else {
    // Production (Hosting/Server)
    // TODO: Ganti dengan credentials production lu
    $db_host = getenv('DB_HOST') ?: "localhost";
    $db_user = getenv('DB_USER') ?: "root";
    $db_pass = getenv('DB_PASS') ?: "";
    $db_name = getenv('DB_NAME') ?: "trainhub_db";
    $db_port = getenv('DB_PORT') ?: 3306;
}

// === CREATE CONNECTION ===
// Suppress errors dengan @ biar nggak expose credentials
$db_connection = @new mysqli($db_host, $db_user, $db_pass, $db_name, $db_port);

// === ERROR HANDLING ===
if ($db_connection->connect_error) {
    // Log error ke file (jangan tampilkan di production)
    error_log("Database Connection Failed: " . $db_connection->connect_error);
    
    // Response berbeda untuk development vs production
    http_response_code(500); // Internal Server Error
    header('Content-Type: application/json');
    
    if ($is_development) {
        // Development: Tampilkan detail error
        echo json_encode([
            'status' => 'error',
            'message' => 'Koneksi ke database GAGAL.',
            'error_detail' => $db_connection->connect_error,
            'hint' => 'Pastikan MySQL sudah running di XAMPP/MAMP',
            'connection_info' => [
                'host' => $db_host,
                'user' => $db_user,
                'database' => $db_name,
                'port' => $db_port
            ]
        ], JSON_PRETTY_PRINT);
    } else {
        // Production: Generic error message (jangan expose detail)
        echo json_encode([
            'status' => 'error',
            'message' => 'Service temporarily unavailable. Please try again later.',
            'error_code' => 'DB_CONNECTION_FAILED'
        ]);
    }
    
    die(); // Stop execution
}

// === SET CHARACTER ENCODING ===
// Penting untuk support emoji dan karakter UTF-8
if (!$db_connection->set_charset("utf8mb4")) {
    error_log("Error loading character set utf8mb4: " . $db_connection->error);
    
    http_response_code(500);
    header('Content-Type: application/json');
    
    echo json_encode([
        'status' => 'error',
        'message' => 'Database configuration error.',
        'error_detail' => $is_development ? $db_connection->error : null
    ]);
    
    die();
}

// === SET TIMEZONE (Optional tapi recommended) ===
// Pastikan timezone DB sama dengan PHP
$db_connection->query("SET time_zone = '+00:00'"); // UTC
// Atau sesuaikan dengan timezone lu:
// $db_connection->query("SET time_zone = '+07:00'"); // WIB (Jakarta)

// === CONNECTION SUCCESS (Development mode only) ===
if ($is_development && isset($_GET['test_db'])) {
    // Test endpoint: /api/test?test_db=1
    http_response_code(200);
    header('Content-Type: application/json');
    
    echo json_encode([
        'status' => 'success',
        'message' => 'Database connected successfully!',
        'info' => [
            'host' => $db_host,
            'database' => $db_name,
            'charset' => $db_connection->character_set_name(),
            'server_version' => $db_connection->server_info,
            'protocol_version' => $db_connection->protocol_version
        ]
    ], JSON_PRETTY_PRINT);
    
    $db_connection->close();
    die();
}

// === HELPER FUNCTIONS ===

/**
 * Execute prepared statement with error handling
 * 
 * @param mysqli $db Database connection
 * @param string $sql SQL query with placeholders
 * @param string $types Parameter types (e.g., "iss" = int, string, string)
 * @param array $params Parameters to bind
 * @return mysqli_result|false
 */
function db_execute($db, $sql, $types = '', $params = []) {
    $stmt = $db->prepare($sql);
    
    if (!$stmt) {
        error_log("Prepare failed: " . $db->error);
        return false;
    }
    
    if ($types && $params) {
        $stmt->bind_param($types, ...$params);
    }
    
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        $stmt->close();
        return false;
    }
    
    $result = $stmt->get_result();
    $stmt->close();
    
    return $result;
}

/**
 * Sanitize input untuk mencegah XSS
 * 
 * @param string $data Input data
 * @return string Sanitized data
 */
function sanitize_input($data) {
    global $db_connection;
    
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    
    return $data;
}

/**
 * Check if database connection is still alive
 * 
 * @return bool
 */
function is_db_connected() {
    global $db_connection;
    return $db_connection && $db_connection->ping();
}

// === REGISTER SHUTDOWN FUNCTION ===
// Auto-close connection saat script selesai
register_shutdown_function(function() {
    global $db_connection;
    
    if ($db_connection && $db_connection->ping()) {
        $db_connection->close();
    }
});

// === OPTIONAL: Log successful connection (development only) ===
if ($is_development) {
    error_log("Database connected: {$db_name}@{$db_host}");
}

// Connection berhasil, lanjut ke controller
?>