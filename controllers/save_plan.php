<?php
session_start();
header('Content-Type: application/json');
include '../koneksi.php';

$response = ['success' => false, 'message' => 'Permintaan tidak valid.'];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Sesi tidak valid, silakan login ulang.';
    echo json_encode($response);
    header("Location: ../login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $plan_name_template = mysqli_real_escape_string($koneksi, $_POST['plan_name']);
    $plan_json_template = $_POST['plan_json']; // Ini JSON string 7-hari
    $start_date_string = $_POST['start_date']; // Ini tanggal SENIN
    $duration_weeks = isset($_POST['duration_weeks']) ? (int)$_POST['duration_weeks'] : 4; // Default 4 minggu jika tidak ada

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

        // 1. Extract Template (Ambil hari dari Minggu 1 saja)
        $template_days = [
            'monday' => null,
            'tuesday' => null,
            'wednesday' => null,
            'thursday' => null,
            'friday' => null,
            'saturday' => null,
            'sunday' => null
        ];

        // Karena AI sekarang cuma balikin 1 minggu, kita bisa langsung loop
        foreach ($weekly_schedule as $day) {
            $day_map = [
                1 => 'monday',
                2 => 'tuesday',
                3 => 'wednesday',
                4 => 'thursday',
                5 => 'friday',
                6 => 'saturday',
                7 => 'sunday'
            ];
            $day_col = $day_map[$day['day_number']] ?? null;

            if ($day_col) {
                $template_days[$day_col] = json_encode($day);
            }
        }

        // 2. Calculate Finish Date
        // Durasi = duration_weeks * 7 hari
        // finish_date = start_date + (duration - 1) days
        $duration_days = $duration_weeks * 7;
        $finish_date_string = date('Y-m-d', strtotime($start_date_string . ' + ' . ($duration_days - 1) . ' days'));

        // 3. Insert Single Row Template
        $sql = "INSERT INTO user_plans (user_id, plan_name, start_date, finish_date, monday, tuesday, wednesday, thursday, friday, saturday, sunday) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $koneksi->prepare($sql);

        $stmt->bind_param(
            "issssssssss",
            $user_id,
            $plan_name_template,
            $start_date_string,
            $finish_date_string,
            $template_days['monday'],
            $template_days['tuesday'],
            $template_days['wednesday'],
            $template_days['thursday'],
            $template_days['friday'],
            $template_days['saturday'],
            $template_days['sunday']
        );

        if (!$stmt->execute()) {
            throw new Exception("Error insert DB: " . $stmt->error);
        }

        $stmt->close();
        $koneksi->commit();
        $response['success'] = true;
        $response['message'] = 'Berhasil menyimpan template rencana latihan!';
    } catch (Exception $e) {
        $koneksi->rollback(); // Batalin semua kalo ada 1 yang error
        $response['message'] = 'Gagal menyimpan ke database: ' . $e->getMessage();
    }
}

$koneksi->close();
echo json_encode($response);
