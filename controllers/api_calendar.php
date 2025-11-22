<?php
session_start();
header('Content-Type: application/json');
include '../koneksi.php';
include '../config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];

$start_date = $_GET['start'] ?? date('Y-m-d');
$end_date = $_GET['end'] ?? date('Y-m-d', strtotime($start_date . ' +6 days'));

// 1. Ambil Template Plan yang aktif dalam rentang request
$sql = "SELECT id, start_date, finish_date, plan_name
        FROM user_plans
        WHERE user_id = ?
        AND start_date <= ?
        AND finish_date >= ?";

$stmt = $koneksi->prepare($sql);
$stmt->bind_param("iss", $user_id, $end_date, $start_date);
$stmt->execute();
$result = $stmt->get_result();

$events = [];

while ($plan = $result->fetch_assoc()) {
    $plan_id = $plan['id'];
    $plan_start = $plan['start_date'];
    $plan_finish = $plan['finish_date'];

    // 2. Ambil Detail Hari & Exercise untuk Plan ini
    // Optimization: Bisa di-cache atau di-query sekali saja jika struktur kompleks.
    // Tapi untuk simpel, kita query per plan (biasanya cuma 1 plan aktif).
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

    // 3. Loop Tanggal
    $loop_start = max($start_date, $plan_start);
    $loop_end = min($end_date, $plan_finish);

    if ($loop_start <= $loop_end) {
        $current = strtotime($loop_start);
        $end_ts = strtotime($loop_end);

        // Ambil log completion
        $completed_dates = [];
        $log_sql = "SELECT date FROM workout_logs WHERE user_id = ? AND plan_id = ? AND date BETWEEN ? AND ?";
        $log_stmt = $koneksi->prepare($log_sql);
        $log_stmt->bind_param("iiss", $user_id, $plan_id, $loop_start, $loop_end);
        $log_stmt->execute();
        $log_result = $log_stmt->get_result();
        while ($log_row = $log_result->fetch_assoc()) {
            $completed_dates[] = $log_row['date'];
        }

        while ($current <= $end_ts) {
            $date_str = date('Y-m-d', $current);
            $day_of_week_num = date('N', $current); // 1-7

            // Ambil data dari map
            if (isset($days_map[$day_of_week_num])) {
                $day_data = $days_map[$day_of_week_num];
                
                $is_rest = $day_data['is_off'];
                $is_completed = in_array($date_str, $completed_dates);

                $bg_color = $is_rest ? '#6B7280' : '#F97316';
                if ($is_completed) {
                    $bg_color = '#22c55e';
                }

                $events[] = [
                    'id' => $plan_id . '_' . $date_str,
                    'title' => $day_data['day_title'],
                    'start' => $date_str,
                    'backgroundColor' => $bg_color,
                    'borderColor' => $bg_color,
                    'extendedProps' => [
                        'details' => $day_data, // Contains exercises
                        'plan_id' => $plan_id,
                        'is_completed' => $is_completed
                    ]
                ];
            }

            $current = strtotime('+1 day', $current);
        }
    }
}

echo json_encode($events);
