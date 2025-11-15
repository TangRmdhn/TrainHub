<?php
// File: api/controllers/plan_controller.php

/**
 * Mengambil semua plan milik user (dengan detail item-nya)
 */
function getUserPlans($db_connection, $user_id) {
    $stmt = $db_connection->prepare("
        SELECT plan_id, plan_name, notes, created_at 
        FROM workout_plans 
        WHERE user_id = ? 
        ORDER BY created_at DESC
    ");
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

/**
 * Mengambil DETAIL satu plan (beserta semua item-nya)
 */
function getPlanDetail($db_connection, $user_id, $plan_id) {
    // 1. Ambil info plan
    $stmt_plan = $db_connection->prepare("
        SELECT plan_id, plan_name, notes, created_at 
        FROM workout_plans 
        WHERE plan_id = ? AND user_id = ?
    ");
    $stmt_plan->bind_param("ii", $plan_id, $user_id);
    $stmt_plan->execute();
    $result_plan = $stmt_plan->get_result();
    
    if ($result_plan->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['status' => 'error', 'message' => 'Plan tidak ditemukan']);
        return;
    }
    
    $plan = $result_plan->fetch_assoc();
    $stmt_plan->close();
    
    // 2. Ambil semua item (latihan per hari)
    $stmt_items = $db_connection->prepare("
        SELECT day_index, day_title, exercise_name, exercise_order, sets, reps, duration_seconds
        FROM workout_plan_items
        WHERE plan_id = ?
        ORDER BY day_index, exercise_order
    ");
    $stmt_items->bind_param("i", $plan_id);
    $stmt_items->execute();
    $result_items = $stmt_items->get_result();
    
    // Group by day
    $schedule = [];
    $current_day = null;
    while ($item = $result_items->fetch_assoc()) {
        if ($current_day !== $item['day_index']) {
            $current_day = $item['day_index'];
            $schedule[$current_day] = [
                'day' => $item['day_title'],
                'exercises' => []
            ];
        }
        
        $schedule[$current_day]['exercises'][] = [
            'exercise_name' => $item['exercise_name'],
            'sets' => $item['sets'],
            'reps' => $item['reps'],
            'duration_seconds' => $item['duration_seconds']
        ];
    }
    $stmt_items->close();
    
    $plan['schedule'] = array_values($schedule);
    
    http_response_code(200);
    echo json_encode(['status' => 'success', 'data' => $plan]);
}

/**
 * Hapus plan
 */
function deletePlan($db_connection, $user_id, $plan_id) {
    // Karena ada FK CASCADE, hapus plan otomatis hapus items-nya
    $stmt = $db_connection->prepare("
        DELETE FROM workout_plans 
        WHERE plan_id = ? AND user_id = ?
    ");
    $stmt->bind_param("ii", $plan_id, $user_id);
    
    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => 'Plan berhasil dihapus']);
        } else {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => 'Plan tidak ditemukan']);
        }
    } else {
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus plan']);
    }
    $stmt->close();
}
?>