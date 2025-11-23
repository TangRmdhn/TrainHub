<?php
session_start();
date_default_timezone_set('Asia/Jakarta'); // Set timezone ke WIB (UTC+7)
header('Content-Type: application/json');
include '../koneksi.php';
include '../config.php';

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

// 1. Ambil Data Header Rencana
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

// 2. Ambil Template Hari & Latihan
// Kita ambil semua hari dan latihan, terus susun di array assoc berdasarkan day_number
$days_map = [];

$sqlDays = "SELECT pd.id as day_id, pd.day_number, pd.day_title, pd.is_off,
                   pe.name as ex_name, pe.sets, pe.reps, pe.rest
            FROM plan_days pd
            LEFT JOIN plan_exercises pe ON pd.id = pe.day_id
            WHERE pd.plan_id = ?
            ORDER BY pd.day_number, pe.id";

$stmtDays = $koneksi->prepare($sqlDays);
$stmtDays->bind_param("i", $plan_id);
$stmtDays->execute();
$resDays = $stmtDays->get_result();

while ($row = $resDays->fetch_assoc()) {
    $dNum = $row['day_number'];

    if (!isset($days_map[$dNum])) {
        $days_map[$dNum] = [
            'day_title' => $row['day_title'],
            'is_off' => (bool)$row['is_off'],
            'exercises' => []
        ];
    }

    if ($row['ex_name']) {
        $days_map[$dNum]['exercises'][] = [
            'name' => $row['ex_name'],
            'sets' => $row['sets'],
            'reps' => $row['reps'],
            'rest' => $row['rest']
        ];
    }
}

// 3. Ambil Log Penyelesaian
$completed_dates = [];
$log_sql = "SELECT date FROM workout_logs WHERE plan_id = ? AND user_id = ?";
$log_stmt = $koneksi->prepare($log_sql);
$log_stmt->bind_param("ii", $plan_id, $user_id);
$log_stmt->execute();
$log_result = $log_stmt->get_result();
while ($row = $log_result->fetch_assoc()) {
    $completed_dates[] = $row['date'];
}

// 4. Bikin Timeline
$timeline = [];
$current = strtotime($plan['start_date']);
$end = strtotime($plan['finish_date']);
$today = date('Y-m-d');

$day_names_id = [
    1 => 'SENIN',
    2 => 'SELASA',
    3 => 'RABU',
    4 => 'KAMIS',
    5 => 'JUMAT',
    6 => 'SABTU',
    7 => 'MINGGU'
];

while ($current <= $end) {
    $date_str = date('Y-m-d', $current);
    $day_num = date('N', $current); // 1 (Mon) - 7 (Sun)

    // Ambil template buat hari ini
    $day_data = $days_map[$day_num] ?? [
        'day_title' => 'Unknown',
        'is_off' => false,
        'exercises' => []
    ];

    $is_off = $day_data['is_off'];
    $session_title = $day_data['day_title'];

    // Tentuin Status
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
        'day_name' => $day_names_id[$day_num],
        'session_title' => $session_title,
        'is_off' => $is_off,
        'status' => $status,
        'status_label' => $status_label,
        'exercises' => $day_data['exercises'] // Opsional: Masukin latihan kalo frontend butuh
    ];

    $current = strtotime('+1 day', $current);
}

echo json_encode([
    'plan_name' => $plan['plan_name'],
    'start_date' => $plan['start_date'],
    'finish_date' => $plan['finish_date'],
    'coach_note' => $plan['coach_note'],
    'timeline' => $timeline
]);
