<?php
session_start();
header('Content-Type: application/json');
include '../koneksi.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
$user_id = $_SESSION['user_id'];

$start_date = $_GET['start'] ?? date('Y-m-d');
$end_date = $_GET['end'] ?? date('Y-m-d', strtotime($start_date . ' +6 days'));

// Ambil Template Plan yang aktif dalam rentang request
$sql = "SELECT id, start_date, finish_date, plan_name, monday, tuesday, wednesday, thursday, friday, saturday, sunday 
        FROM user_plans 
        WHERE user_id = ? 
        AND start_date <= ? 
        AND finish_date >= ?";

$stmt = $koneksi->prepare($sql);
$stmt->bind_param("iss", $user_id, $end_date, $start_date);
$stmt->execute();
$result = $stmt->get_result();

$events = [];

while ($row = $result->fetch_assoc()) {
    $plan_start = $row['start_date'];
    $plan_finish = $row['finish_date'];

    // Loop setiap hari dari start_date request sampai end_date request
    // TAPI dibatasi juga oleh start_date plan dan finish_date plan

    $loop_start = max($start_date, $plan_start);
    $loop_end = min($end_date, $plan_finish);

    if ($loop_start <= $loop_end) {
        $current = strtotime($loop_start);
        $end_ts = strtotime($loop_end);

        // Ambil log completion untuk user ini dalam rentang tanggal ini
        $completed_dates = [];
        $log_sql = "SELECT date FROM workout_logs WHERE user_id = ? AND plan_id = ? AND date BETWEEN ? AND ?";
        $log_stmt = $koneksi->prepare($log_sql);
        $log_stmt->bind_param("iiss", $user_id, $row['id'], $loop_start, $loop_end);
        $log_stmt->execute();
        $log_result = $log_stmt->get_result();
        while ($log_row = $log_result->fetch_assoc()) {
            $completed_dates[] = $log_row['date'];
        }
        $log_stmt->close();

        while ($current <= $end_ts) {
            $date_str = date('Y-m-d', $current);
            $day_of_week_num = date('N', $current); // 1 (Mon) - 7 (Sun)

            $day_map = [
                1 => 'monday',
                2 => 'tuesday',
                3 => 'wednesday',
                4 => 'thursday',
                5 => 'friday',
                6 => 'saturday',
                7 => 'sunday'
            ];
            $col_name = $day_map[$day_of_week_num];

            if (!empty($row[$col_name])) {
                $day_data = json_decode($row[$col_name], true);
                $is_rest = isset($day_data['is_off_day']) && $day_data['is_off_day'];
                $is_completed = in_array($date_str, $completed_dates);

                $bg_color = $is_rest ? '#6B7280' : '#F97316';
                if ($is_completed) {
                    $bg_color = '#22c55e'; // Green for completed
                }

                $events[] = [
                    'id' => $row['id'] . '_' . $date_str, // Unique ID per tanggal
                    'title' => $day_data['session_title'],
                    'start' => $date_str,
                    'backgroundColor' => $bg_color,
                    'borderColor' => $bg_color,
                    'extendedProps' => [
                        'details' => $day_data,
                        'plan_id' => $row['id'],
                        'is_completed' => $is_completed
                    ]
                ];
            }

            $current = strtotime('+1 day', $current);
        }
    }
}

$stmt->close();
$koneksi->close();

echo json_encode($events);

