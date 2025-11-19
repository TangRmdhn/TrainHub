<?php
session_start();
header('Content-Type: application/json');
include '../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$plan_id = $_GET['plan_id'] ?? null;

if (!$plan_id) {
    echo json_encode(['error' => 'Plan ID required']);
    exit;
}

// 1. Ambil Data Plan
$sql = "SELECT * FROM user_plans WHERE id = ? AND user_id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("ii", $plan_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['error' => 'Plan not found']);
    exit;
}

$plan = $result->fetch_assoc();

// 2. Ambil Log Completion
$completed_dates = [];
$log_sql = "SELECT date FROM workout_logs WHERE plan_id = ? AND user_id = ?";
$log_stmt = $koneksi->prepare($log_sql);
$log_stmt->bind_param("ii", $plan_id, $user_id);
$log_stmt->execute();
$log_result = $log_stmt->get_result();
while ($row = $log_result->fetch_assoc()) {
    $completed_dates[] = $row['date'];
}

// 3. Generate Timeline
$timeline = [];
$current = strtotime($plan['start_date']);
$end = strtotime($plan['finish_date']);
$today = date('Y-m-d');

while ($current <= $end) {
    $date_str = date('Y-m-d', $current);
    $day_num = date('N', $current); // 1 (Mon) - 7 (Sun)

    $day_map = [
        1 => 'monday',
        2 => 'tuesday',
        3 => 'wednesday',
        4 => 'thursday',
        5 => 'friday',
        6 => 'saturday',
        7 => 'sunday'
    ];
    $col_name = $day_map[$day_num];

    $day_data = json_decode($plan[$col_name], true);
    $is_off = $day_data['is_off_day'] ?? false;
    $session_title = $day_data['session_title'] ?? 'Unknown';

    // Determine Status
    $status = 'upcoming';
    $status_label = 'Upcoming';

    if ($is_off) {
        $status = 'rest';
        $status_label = 'Rest Day';
    } elseif (in_array($date_str, $completed_dates)) {
        $status = 'completed';
        $status_label = 'Completed';
    } elseif ($date_str < $today) {
        $status = 'missed';
        $status_label = 'Missed';
    } elseif ($date_str === $today) {
        $status = 'today';
        $status_label = 'Today';
    }

    $timeline[] = [
        'date' => $date_str,
        'day_name' => $day_data['day_name'] ?? '',
        'session_title' => $session_title,
        'is_off' => $is_off,
        'status' => $status,
        'status_label' => $status_label
    ];

    $current = strtotime('+1 day', $current);
}

echo json_encode([
    'plan_name' => $plan['plan_name'],
    'start_date' => $plan['start_date'],
    'finish_date' => $plan['finish_date'],
    'timeline' => $timeline
]);
