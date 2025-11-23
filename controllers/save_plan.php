<?php
session_start();
header('Content-Type: application/json');
include '../koneksi.php';
include '../config.php';

$response = ['success' => false, 'message' => 'Permintaan tidak valid.'];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Sesi tidak valid, silakan login ulang.';
    echo json_encode($response);
    exit;
}
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $plan_name_template = mysqli_real_escape_string($koneksi, $_POST['plan_name']);
    $plan_json_template = $_POST['plan_json']; // Ini JSON string 7-hari
    $start_date_string = $_POST['start_date']; // Ini tanggal SENIN
    $duration_weeks = isset($_POST['duration_weeks']) ? (int)$_POST['duration_weeks'] : 4;

    // 1. Decode JSON template dari AI
    $template = json_decode($plan_json_template, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $response['message'] = 'Error: Format data rencana (JSON) tidak valid.';
        echo json_encode($response);
        exit;
    }

    $koneksi->begin_transaction();

    try {
        $weekly_schedule = $template['weekly_schedule'] ?? [];
        $coach_note = $template['coach_note'] ?? '';

        // 2. Hitung Tanggal Selesai
        $duration_days = $duration_weeks * 7;
        $finish_date_string = date('Y-m-d', strtotime($start_date_string . ' + ' . ($duration_days - 1) . ' days'));

        // 3. Insert Header Rencana (user_plans)
        $sqlPlan = "INSERT INTO user_plans (user_id, plan_name, start_date, finish_date, coach_note) VALUES (?, ?, ?, ?, ?)";
        $stmtPlan = $koneksi->prepare($sqlPlan);
        $stmtPlan->bind_param("issss", $user_id, $plan_name_template, $start_date_string, $finish_date_string, $coach_note);

        if (!$stmtPlan->execute()) {
            throw new Exception("Error insert Plan: " . $stmtPlan->error);
        }
        $plan_id = $koneksi->insert_id;
        $stmtPlan->close();

        // 4. Insert Hari & Latihan
        $sqlDay = "INSERT INTO plan_days (plan_id, day_number, day_title, is_off) VALUES (?, ?, ?, ?)";
        $stmtDay = $koneksi->prepare($sqlDay);

        $sqlEx = "INSERT INTO plan_exercises (day_id, name, sets, reps, rest) VALUES (?, ?, ?, ?, ?)";
        $stmtEx = $koneksi->prepare($sqlEx);

        foreach ($weekly_schedule as $day) {
            $day_number = $day['day_number']; // 1-7
            $day_title = $day['session_title'] ?? ($day['day_name'] ?? 'Day ' . $day_number);
            $is_off = isset($day['is_off_day']) && $day['is_off_day'] ? 1 : 0;

            // Insert Hari
            $stmtDay->bind_param("iisi", $plan_id, $day_number, $day_title, $is_off);
            if (!$stmtDay->execute()) {
                throw new Exception("Error insert Day: " . $stmtDay->error);
            }
            $day_id = $koneksi->insert_id;

            // Insert Latihan kalo bukan hari libur
            if (!$is_off && !empty($day['exercises'])) {
                foreach ($day['exercises'] as $ex) {
                    $name = $ex['name'];
                    $sets = $ex['sets'];
                    $reps = $ex['reps'];
                    $rest = $ex['rest'] ?? '-';

                    $stmtEx->bind_param("issss", $day_id, $name, $sets, $reps, $rest);
                    if (!$stmtEx->execute()) {
                        throw new Exception("Error insert Exercise: " . $stmtEx->error);
                    }
                }
            }
        }

        $stmtDay->close();
        $stmtEx->close();

        $koneksi->commit();
        $response['success'] = true;
        $response['message'] = 'Berhasil menyimpan template rencana latihan!';
    } catch (Exception $e) {
        $koneksi->rollback();
        $response['message'] = 'Gagal menyimpan ke database: ' . $e->getMessage();
    }
}

$koneksi->close();
echo json_encode($response);
