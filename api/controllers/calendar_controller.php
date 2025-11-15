<?php
// File: api/controllers/calendar_controller.php

/**
 * Endpoint untuk menjadwalkan sebuah plan ke tanggal tertentu.
 * METHOD: POST
 * URL: /api/calendar
 * BODY: { "plan_id": 123, "date": "2025-11-20" }
 */
function schedulePlanToDate($db_connection, $user_id) {
    $data = json_decode(file_get_contents('php://input'), true);

    if (empty($data['plan_id']) || empty($data['date'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'message' => 'Butuh "plan_id" dan "date".']);
        return;
    }

    $plan_id = $data['plan_id'];
    $date = $data['date']; // Format YYYY-MM-DD

    // Cek dulu jangan-jangan udah ada jadwal di tanggal itu (dari SQL UNIQUE KEY)
    $stmt = $db_connection->prepare(
        "INSERT INTO user_schedules (user_id, plan_id, scheduled_date) 
         VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE plan_id = VALUES(plan_id)" // Kalo udah ada, timpa aja
    );
    $stmt->bind_param("iis", $user_id, $plan_id, $date);

    if ($stmt->execute()) {
        http_response_code(201); // Created (atau 200 OK kalo update)
        echo json_encode(['status' => 'success', 'message' => 'Jadwal berhasil disimpan.']);
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal simpan jadwal: ' . $stmt->error]);
    }
    $stmt->close();
}

/**
 * Endpoint untuk mengambil semua jadwal dalam 1 bulan.
 * METHOD: GET
 * URL: /api/calendar?month=2025-11
 */
function getSchedulesForMonth($db_connection, $user_id, $month) {
    // $month formatnya "YYYY-MM"
    // Kita cari yang awalnya kayak gitu
    $date_start = $month . "-01";
    $date_end = $month . "-31"; // (Cara simpel, MySQL bakal handle)

    $stmt = $db_connection->prepare(
        "SELECT 
            s.schedule_id, s.scheduled_date, s.is_completed,
            p.plan_id, p.plan_name 
         FROM user_schedules s
         JOIN workout_plans p ON s.plan_id = p.plan_id
         WHERE s.user_id = ? 
         AND s.scheduled_date BETWEEN ? AND ?"
    );
    $stmt->bind_param("iss", $user_id, $date_start, $date_end);
    $stmt->execute();
    $result = $stmt->get_result();

    $schedules = [];
    while ($row = $result->fetch_assoc()) {
        $schedules[] = $row;
    }
    $stmt->close();

    http_response_code(200);
    echo json_encode(['status' => 'success', 'data' => $schedules]);
}

/**
 * Endpoint untuk menandai jadwal sebagai "Selesai".
 * METHOD: PUT
 * URL: /api/calendar/complete?schedule_id=1
 */
function markScheduleAsComplete($db_connection, $user_id, $schedule_id) {
    
    // Kita juga bisa tambahin $data['workout_notes'] kalo user ngasih catatan
    $data = json_decode(file_get_contents('php://input'), true);
    $notes = $data['notes'] ?? null;
    $is_completed = 1; // true

    $stmt = $db_connection->prepare(
        "UPDATE user_schedules 
         SET is_completed = ?, workout_notes = ?
         WHERE schedule_id = ? AND user_id = ?" // PENTING: Cek user_id biar orang gak bisa update jadwal orang lain
    );
    $stmt->bind_param("isii", $is_completed, $notes, $schedule_id, $user_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Workout ditandai selesai!']);
        } else {
            http_response_code(404); // Not Found (atau 403 Forbidden)
            echo json_encode(['status' => 'error', 'message' => 'Jadwal tidak ditemukan atau bukan milik Anda.']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal update jadwal: ' . $stmt->error]);
    }
    $stmt->close();
}
?>