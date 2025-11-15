<?php
// (File ini dipanggil oleh router utama)

function generateAIPlan($db_connection) {
    // 1. Ambil prompt dari user (yang dikirim dari JS)
    $data = json_decode(file_get_contents('php://input'), true);
    $userPrompt = $data['prompt'];

    // 2. [SUPER PENTING] Ambil konteks dari Database lu!
    // AI nggak tau database lu. Kita kasih tau dia daftar latihan yang ada.
    $exerciseList = [];
    $result = mysqli_query($db_connection, "SELECT name, category FROM exercises");
    while ($row = mysqli_fetch_assoc($result)) {
        $exerciseList[] = $row['name'] . " (" . $row['category'] . ")";
    }
    $exerciseString = implode(", ", $exerciseList);

    // 3. Siapin Prompt Engineering (Perintah buat Gemini)
    // Ini adalah 'otak' dari fitur lu.
    $systemPrompt = "You are TrainHub AI, a professional fitness planner.
    A user wants a workout plan. The user's request is: '{$userPrompt}'.

    You MUST generate a workout plan in JSON format.
    The JSON structure MUST follow this exact format:
    {
      \"plan_name\": \"[Nama Plan dari AI]\",
      \"notes\": \"[Catatan singkat dari AI]\",
      \"schedule\": [
        {
          \"day\": \"Day 1: [Fokus Latihan]\",
          \"exercises\": [
            { \"exercise_name\": \"[Nama Latihan]\", \"sets\": 3, \"reps\": 10 },
            { \"exercise_name\": \"[Nama Latihan]\", \"duration_seconds\": 60 }
          ]
        },
        {
          \"day\": \"Day 2: Rest\",
          \"exercises\": []
        }
      ]
    }

    IMPORTANT: Only use exercises from this available list:
    [{$exerciseString}]

    If the user asks for an exercise not in the list, substitute it with the closest alternative from the list.
    Respond ONLY with the JSON object, without any other text or markdown.";

    
    // 4. Panggil Gemini API pake cURL
    $GEMINI_API_KEY = "PAKE_API_KEY_LU_DI_SINI";
    $GEMINI_API_URL = "https://generativelanguage.googleapis.com/v1beta/models/gemini-pro:generateContent?key=" . $GEMINI_API_KEY;

    $postData = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $systemPrompt]
                ]
            ]
        ]
    ];

    $ch = curl_init($GEMINI_API_URL);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // (Hanya untuk localhost, matikan di produksi)

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        http_response_code(500);
        echo json_encode(['error' => 'cURL error: ' . curl_error($ch)]);
        return;
    }

    // 5. Parsing response dari Gemini
    $result = json_decode($response, true);
    
    // Hati-hati, format response Gemini ada di dalam 'candidates'
    if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
        http_response_code(500);
        echo json_encode(['error' => 'Invalid response from AI API', 'details' => $result]);
        return;
    }

    $jsonPlanText = $result['candidates'][0]['content']['parts'][0]['text'];

    // 6. Kirim JSON murni hasil buatan AI ke Front-end
    header('Content-Type: application/json');
    
    // Kita cek dulu apa beneran JSON
    $decodedJson = json_decode($jsonPlanText);
    if (json_last_error() === JSON_ERROR_NONE) {
        // Jika valid, kirim sebagai JSON
        echo $jsonPlanText; 
    } else {
        // Jika AI ngasih teks aneh, kirim sebagai error
        http_response_code(500);
        echo json_encode(['error' => 'AI returned non-JSON response', 'raw_response' => $jsonPlanText]);
    }
}
?>