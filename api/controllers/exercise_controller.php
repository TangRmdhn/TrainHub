<?php
// File: api/controllers/exercise_controller.php

/**
 * Mengambil semua data latihan dari database.
 * Nanti kita bisa tambahin filter berdasarkan kategori, dll.
 */
function getAllExercises($db_connection) {
    
    // Query untuk mengambil semua latihan, diurutkan berdasarkan kategori lalu nama
    $sql = "SELECT id, name, category, difficulty, media_url, equipment, default_duration_seconds 
            FROM exercises 
            ORDER BY category, name";

    $result = $db_connection->query($sql);

    if (!$result) {
        // Jika query gagal
        http_response_code(500); // Internal Server Error
        echo json_encode([
            'status' => 'error', 
            'message' => 'Query database gagal: ' . $db_connection->error
        ]);
        return;
    }

    $exercises = [];
    // Loop semua hasil query dan masukkan ke array
    while ($row = $result->fetch_assoc()) {
        $exercises[] = $row;
    }

    // Kirim data sebagai JSON
    http_response_code(200); // OK
    echo json_encode([
        'status' => 'success',
        'data' => $exercises
    ]);
    
    $result->close();
}

/**
 * (Contoh untuk nanti)
 * Mengambil satu data latihan spesifik berdasarkan ID.
 */
function getExerciseById($db_connection, $id) {
    // ... (Logika untuk ambil 1 exercise) ...
}
?>