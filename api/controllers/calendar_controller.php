<?php
// File: api/controllers/calendar_controller.php

/**
 * Endpoint untuk menjadwalkan sebuah plan ke tanggal tertentu.
 * METHOD: POST
 * URL: /api/calendar
 * BODY: { "plan_id": 123, "date": "2025-11-20" }
 */
// function schedulePlanToDate($db_connection, $user_id) {
//     $data = json_decode(file_get_contents('php://input'), true);

//     if (empty($data['plan_id']) || empty($data['date'])) {
//         http_response_code(400);
//         echo json_encode(['status' => 'error', 'message' => 'Butuh "plan_id" dan "date".']);
//         return;
//     }

//     $plan_id = $data['plan_id'];
//     $date = $data['date']; // Format YYYY-MM-DD

//     // Cek dulu jangan-jangan udah ada jadwal di tanggal itu (dari SQL UNIQUE KEY)
//     $stmt = $db_connection->prepare(
//         "INSERT INTO user_schedules (user_id, plan_id, scheduled_date) 
//          VALUES (?, ?, ?)
//          ON DUPLICATE KEY UPDATE plan_id = VALUES(plan_id)" // Kalo udah ada, timpa aja
//     );
//     $stmt->bind_param("iis", $user_id, $plan_id, $date);

//     if ($stmt->execute()) {
//         http_response_code(201); // Created (atau 200 OK kalo update)
//         echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil disimpan.']);
//     } else {
//         http_response_code(500);
//         echo json_encode(['status' => 'error', 'message' => 'Gagal simpan jadwal: ' . $stmt->error]);
//     }
//     $stmt->close();
// }
function schedulePlanToDate($db, $user_id) {
    $plan_id = $_POST['plan_id'] ?? null;
    $scheduled_date = $_POST['scheduled_date'] ?? null;

    if (!$plan_id || !$scheduled_date) {
        echo json_encode([
            'status' => 'error',
            'message' => 'plan_id dan scheduled_date diperlukan.'
        ]);
        return;
    }

    // Cek apakah tanggal sudah ada
    $stmt = $db->prepare("
        SELECT schedule_id 
        FROM user_schedules 
        WHERE user_id = ? AND scheduled_date = ?
    ");
    $stmt->bind_param("is", $user_id, $scheduled_date);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Kamu sudah memiliki jadwal workout pada tanggal ini.'
        ]);
        return;
    }

    // Insert schedule baru
    $stmt = $db->prepare("
        INSERT INTO user_schedules (user_id, plan_id, scheduled_date)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("iis", $user_id, $plan_id, $scheduled_date);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Workout berhasil dijadwalkan.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menyimpan jadwal.'
        ]);
    }
}

/**
 * Endpoint untuk mengambil semua jadwal dalam 1 bulan.
 * METHOD: GET
 * URL: /api/calendar?month=2025-11
 */
// function getSchedulesForMonth($db_connection, $user_id, $month) {
//     // $month formatnya "YYYY-MM"
//     // Kita cari yang awalnya kayak gitu
//     $date_start = $month . "-01";
//     $date_end = $month . "-31"; // (Cara simpel, MySQL bakal handle)

//     $stmt = $db_connection->prepare(
//         "SELECT 
//             s.schedule_id, s.scheduled_date, s.is_completed,
//             p.plan_id, p.plan_name 
//          FROM user_schedules s
//          JOIN workout_plans p ON s.plan_id = p.plan_id
//          WHERE s.user_id = ? 
//          AND s.scheduled_date BETWEEN ? AND ?"
//     );
//     $stmt->bind_param("iss", $user_id, $date_start, $date_end);
//     $stmt->execute();
//     $result = $stmt->get_result();

//     $schedules = [];
//     while ($row = $result->fetch_assoc()) {
//         $schedules[] = $row;
//     }
//     $stmt->close();

//     http_response_code(200);
//     echo json_encode(['status' => 'success', 'data' => $schedules]);
// }

function getSchedulesForMonth($db, $user_id, $month) {

    // Validasi format YYYY-MM
    if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Format month harus YYYY-MM.'
        ]);
        return;
    }

    $start = $month . "-01";
    $end = date("Y-m-t", strtotime($start)); // t = last day of month

    $stmt = $db->prepare("
        SELECT s.schedule_id, s.plan_id, s.scheduled_date,
               s.is_completed, s.workout_notes, s.completed_at,
               p.plan_name, p.description
        FROM user_schedules s
        JOIN workout_plans p ON s.plan_id = p.plan_id
        WHERE s.user_id = ?
        AND s.scheduled_date BETWEEN ? AND ?
        ORDER BY s.scheduled_date ASC
    ");
    $stmt->bind_param("iss", $user_id, $start, $end);
    $stmt->execute();

    $result = $stmt->get_result();
    $schedules = [];

    while ($row = $result->fetch_assoc()) {
        $schedules[] = $row;
    }

    echo json_encode([
        'status' => 'success',
        'month' => $month,
        'schedules' => $schedules
    ]);
}

/**
 * Endpoint untuk menandai jadwal sebagai "Selesai".
 * METHOD: PUT
 * URL: /api/calendar/complete?schedule_id=1
 */
function markScheduleAsComplete($db, $user_id, $schedule_id) {

    $payload = json_decode(file_get_contents("php://input"), true);
    $notes = $payload["notes"] ?? null;

    $stmt = $db->prepare("
        UPDATE user_schedules
        SET 
            is_completed = 1,
            workout_notes = ?,
            completed_at = NOW()
        WHERE schedule_id = ? AND user_id = ?
    ");
    $stmt->bind_param("sii", $notes, $schedule_id, $user_id);

    if ($stmt->execute()) {

        if ($stmt->affected_rows > 0) {
            http_response_code(200);
            echo json_encode([
                "status"  => "success",
                "message" => "Workout berhasil ditandai selesai!"
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                "status"  => "error",
                "message" => "Jadwal tidak ditemukan atau bukan milikmu."
            ]);
        }

    } else {
        http_response_code(500);
        echo json_encode([
            "status"  => "error",
            "message" => "Gagal update jadwal: " . $stmt->error
        ]);
    }

    $stmt->close();
}

?>