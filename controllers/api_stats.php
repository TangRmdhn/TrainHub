<?php
session_start();
date_default_timezone_set('Asia/Jakarta'); // Set timezone ke WIB (UTC+7)
header('Content-Type: application/json');
include '../koneksi.php';
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    header("Location: " . url("/login"));
    exit;
}

$user_id = $_SESSION['user_id'];

// 1. Total Latihan
$sql_total = "SELECT COUNT(*) as total FROM workout_logs WHERE user_id = ?";
$stmt = $koneksi->prepare($sql_total);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$total_workouts = $stmt->get_result()->fetch_assoc()['total'];

// 2. Streak Saat Ini
$sql_dates = "SELECT DISTINCT date FROM workout_logs WHERE user_id = ? ORDER BY date DESC";
$stmt = $koneksi->prepare($sql_dates);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_dates = $stmt->get_result();

$dates = [];
$today = date('Y-m-d');
while ($row = $result_dates->fetch_assoc()) {
    // ambil data hari ini
    if ($row['date'] <= $today) {
        $dates[] = $row['date'];
    }
}

$streak = 0;
if (!empty($dates)) {
    $today = date('Y-m-d');
    $yesterday = date('Y-m-d', strtotime('-1 day'));

    // Cek apakah streak masih aktif (latihan terakhir hari ini atau kemarin)
    if ($dates[0] === $today || $dates[0] === $yesterday) {
        $streak = 1;
        $current_check = $dates[0];

        for ($i = 1; $i < count($dates); $i++) {
            $prev_date = $dates[$i];
            $expected_prev = date('Y-m-d', strtotime('-1 day', strtotime($current_check)));

            if ($prev_date === $expected_prev) {
                $streak++;
                $current_check = $prev_date;
            } else {
                break;
            }
        }
    }
}

// 3. Data Grafik (30 Hari Terakhir)
$chart_labels = [];
$chart_data = [];
$last_30_days = [];

for ($i = 29; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $last_30_days[$date] = 0;
    $chart_labels[] = date('d M', strtotime($date));
}

$sql_chart = "SELECT date, COUNT(*) as count FROM workout_logs 
              WHERE user_id = ? AND date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) 
              GROUP BY date";
$stmt = $koneksi->prepare($sql_chart);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result_chart = $stmt->get_result();

while ($row = $result_chart->fetch_assoc()) {
    if (isset($last_30_days[$row['date']])) {
        $last_30_days[$row['date']] = (int)$row['count'];
    }
}

$chart_data = array_values($last_30_days);

echo json_encode([
    'total_workouts' => $total_workouts,
    'current_streak' => $streak,
    'chart_labels' => $chart_labels,
    'chart_data' => $chart_data
]);
