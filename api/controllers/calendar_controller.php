<?php
// File: api/controllers/calendar_controller.php

/**
 * Endpoint untuk menjadwalkan sebuah plan ke tanggal tertentu.
 * METHOD: POST
 * URL: /api/calendar
 * BODY: { "plan_id": 123, "date": "2025-11-20" }
 */
function schedulePlanToDate($db, $user_id) {
    // ✅ FIX: Parse JSON body, bukan $_POST
    $data = json_decode(file_get_contents('php://input'), true);
    
    $plan_id = $data['plan_id'] ?? null;
    $scheduled_date = $data['date'] ?? null; // ⚠️ Key: 'date'

    // Debug log
    error_log("=== SCHEDULE PLAN ===");
    error_log("User ID: $user_id");
    error_log("Plan ID: $plan_id");
    error_log("Date: $scheduled_date");
    error_log("Raw input: " . file_get_contents('php://input'));

    if (!$plan_id || !$scheduled_date) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'plan_id dan date diperlukan.'
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
        // ✅ UPDATE existing schedule instead of error
        $stmt = $db->prepare("
            UPDATE user_schedules 
            SET plan_id = ?
            WHERE user_id = ? AND scheduled_date = ?
        ");
        $stmt->bind_param("iis", $plan_id, $user_id, $scheduled_date);
        
        if ($stmt->execute()) {
            http_response_code(200);
            echo json_encode([
                'status' => 'success',
                'message' => 'Schedule berhasil diupdate.'
            ]);
        } else {
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => 'Gagal update schedule: ' . $stmt->error
            ]);
        }
        return;
    }

    // Insert schedule baru
    $stmt = $db->prepare("
        INSERT INTO user_schedules (user_id, plan_id, scheduled_date)
        VALUES (?, ?, ?)
    ");
    $stmt->bind_param("iis", $user_id, $plan_id, $scheduled_date);

    if ($stmt->execute()) {
        http_response_code(201);
        echo json_encode([
            'status' => 'success',
            'message' => 'Workout berhasil dijadwalkan.',
            'schedule_id' => $stmt->insert_id
        ]);
        error_log("Schedule created successfully. ID: " . $stmt->insert_id);
    } else {
        http_response_code(500);
        error_log("Insert error: " . $stmt->error);
        echo json_encode([
            'status' => 'error',
            'message' => 'Gagal menyimpan jadwal: ' . $stmt->error
        ]);
    }
}

/**
 * Endpoint untuk mengambil semua jadwal dalam 1 bulan.
 * METHOD: GET
 * URL: /api/calendar?month=2025-11
 */
function getSchedulesForMonth($db, $user_id, $month) {
    
    error_log("=== GET SCHEDULES ===");
    error_log("User ID: $user_id");
    error_log("Month: $month");

    // Validasi format YYYY-MM
    if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Format month harus YYYY-MM.'
        ]);
        return;
    }

    $start = $month . "-01";
    $end = date("Y-m-t", strtotime($start)); // t = last day of month

    error_log("Date range: $start to $end");

    $stmt = $db->prepare("
        SELECT s.schedule_id, s.plan_id, s.scheduled_date,
               s.is_completed, s.workout_notes, s.completed_at,
               p.plan_name, p.notes as description
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

    error_log("Found schedules: " . count($schedules));
    error_log("Schedules: " . json_encode($schedules));

    http_response_code(200);
    echo json_encode([
        'status' => 'success',
        'month' => $month,
        'data' => $schedules  // ✅ FIX: Key 'data', bukan 'schedules'!
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

    error_log("=== MARK COMPLETE ===");
    error_log("User ID: $user_id");
    error_log("Schedule ID: $schedule_id");
    error_log("Notes: $notes");

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
            error_log("Schedule marked as complete");
        } else {
            http_response_code(404);
            echo json_encode([
                "status"  => "error",
                "message" => "Jadwal tidak ditemukan atau bukan milikmu."
            ]);
            error_log("Schedule not found or unauthorized");
        }

    } else {
        http_response_code(500);
        echo json_encode([
            "status"  => "error",
            "message" => "Gagal update jadwal: " . $stmt->error
        ]);
        error_log("Update error: " . $stmt->error);
    }

    $stmt->close();
}
