<?php
// File: api/controllers/workout_controller.php

/**
 * Menyimpan plan yang di-generate AI (atau dibuat manual) ke database.
 * Ini dipanggil SETELAH front-end nerima JSON dari ai_controller.
 */
function saveWorkoutPlan($db_connection, $user_id) {
    $data = json_decode(file_get_contents('php://input'), true);

    // 1. Validasi data JSON yang dikirim (harus sama kayak struktur AI)
    if (empty($data['plan_name']) || empty($data['schedule'])) {
        http_response_code(400); // Bad Request
        echo json_encode(['status' => 'error', 'message' => 'Struktur plan tidak lengkap. Butuh "plan_name" dan "schedule".']);
        return;
    }

    $plan_name = $data['plan_name'];
    $notes = $data['notes'] ?? '';
    $schedule = $data['schedule']; // Ini array [ {day: "...", exercises: [...]}, ... ]

    // Kita harus pake Transaksi. Kalo 1 item gagal, semua di-rollback.
    $db_connection->begin_transaction();

    try {
        // 2. Insert ke tabel 'workout_plans' (Master Plan)
        $stmt_plan = $db_connection->prepare("INSERT INTO workout_plans (user_id, plan_name, notes) VALUES (?, ?, ?)");
        $stmt_plan->bind_param("iss", $user_id, $plan_name, $notes);
        $stmt_plan->execute();
        
        $plan_id = $db_connection->insert_id; // Ambil ID plan yang barusan dibuat
        $stmt_plan->close();

        // 3. Siapin statement untuk 'workout_plan_items'
        $stmt_item = $db_connection->prepare(
            "INSERT INTO workout_plan_items 
            (plan_id, day_index, day_title, exercise_name, exercise_order, sets, reps, duration_seconds) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
        );

        // 4. Loop semua 'schedule' (hari) dari JSON
        foreach ($schedule as $day_index => $day) {
            $day_title = $day['day'];
            
            // 5. Loop semua 'exercises' di dalam hari itu
            foreach ($day['exercises'] as $exercise_order => $exercise) {
                $exercise_name = $exercise['exercise_name'];
                $sets = $exercise['sets'] ?? null;
                $reps = $exercise['reps'] ?? null;
                $duration = $exercise['duration_seconds'] ?? null;

                // Bind parameter ke statement item
                $stmt_item->bind_param("iissisii", 
                    $plan_id, 
                    $day_index, 
                    $day_title, 
                    $exercise_name, 
                    $exercise_order, 
                    $sets, 
                    $reps, 
                    $duration
                );
                $stmt_item->execute();
            }
        }
        $stmt_item->close();

        // 6. Kalo semua berhasil, commit transaksinya
        $db_connection->commit();
        http_response_code(201); // Created
        echo json_encode([
            'status' => 'success', 
            'message' => 'Rencana berhasil disimpan!',
            'plan_id' => $plan_id
        ]);

    } catch (Exception $e) {
        // 7. Kalo ada error, batalin semua (rollback)
        $db_connection->rollback();
        http_response_code(500);
        echo json_encode([
            'status' => 'error', 
            'message' => 'Gagal menyimpan rencana: ' . $e->getMessage()
        ]);
    }
}

/**
 * Mengambil semua plan yang dimiliki user
 */
function getWorkoutPlans($db_connection, $user_id) {
    // (Ini versi simpel, cuma ambil list nama plan)
    // (Nanti bisa di-expand buat ambil semua detail item-nya)
    
    $stmt = $db_connection->prepare("SELECT plan_id, plan_name, notes, created_at FROM workout_plans WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $plans = [];
    while ($row = $result->fetch_assoc()) {
        $plans[] = $row;
    }
    $stmt->close();

    http_response_code(200);
    echo json_encode(['status' => 'success', 'data' => $plans]);
}
?>