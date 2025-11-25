<!-- API untuk menandai rencana sebagai selesai -->

<?php
session_start();
date_default_timezone_set('Asia/Jakarta'); // Set timezone ke WIB (UTC+7)
header('Content-Type: application/json');
include '../koneksi.php';
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    header("Location: " . url("/login"));
    exit;
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['plan_id']) || !isset($data['date'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

$plan_id = $data['plan_id'];
$date = $data['date'];

// Insert atau Ignore (kalo udah selesai)
$sql = "INSERT IGNORE INTO workout_logs (user_id, plan_id, date, status) VALUES (?, ?, ?, 'completed')";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("iis", $user_id, $plan_id, $date);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true, 'message' => 'Workout marked as completed']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Workout was already completed']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $stmt->error]);
}

$stmt->close();
$koneksi->close();
