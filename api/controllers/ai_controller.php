<?php
// echo "AI Controller Loaded<br>";
// flush();
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

function generateAIPlan($db_connection)
{

  // Ambil JSON input
  $data = json_decode(file_get_contents('php://input'), true);
  $userPrompt = $data['prompt'] ?? null;

  if (!$userPrompt) {
    http_response_code(400);
    echo json_encode(['error' => 'Prompt cannot be empty']);
    return;
  }

  // Ambil data exercises
  $exerciseList = [];
  $result = mysqli_query($db_connection, "SELECT name, category FROM exercises");

  while ($row = mysqli_fetch_assoc($result)) {
    $exerciseList[] = $row['name'] . " (" . $row['category'] . ")";
  }

  $exerciseString = !empty($exerciseList)
    ? implode(", ", $exerciseList)
    : "No exercises available";

  // Prompt AI
  $systemPrompt = "
You are TrainHub AI, a professional fitness planner.
User request: {$userPrompt}

Generate JSON in this format:
{
  \"plan_name\": \"...\",
  \"notes\": \"...\",
  \"schedule\": [
    {
      \"day\": \"Day 1: ...\",
      \"exercises\": [
        { \"exercise_name\": \"...\", \"sets\": 3, \"reps\": 10 }
      ]
    }
  ]
}

Use ONLY these exercises:
{$exerciseString}

Respond ONLY with JSON.
";

  // Gemini API
  $GEMINI_API_KEY = "AIzaSyAfw7W-_eKZPWDTs9JGG4N5F16gpErqAVE";
  $GEMINI_API_URL = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . $GEMINI_API_KEY;

  $postData = [
    'contents' => [
      [
        'role' => 'user',
        'parts' => [
          ['text' => $systemPrompt]
        ]
      ]
    ]
  ];

  // cURL
  $ch = curl_init($GEMINI_API_URL);

  curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
    CURLOPT_POSTFIELDS => json_encode($postData),
    CURLOPT_SSL_VERIFYPEER => false
  ]);

  $response = curl_exec($ch);
  file_put_contents(__DIR__ . "/ai_debug.log", $response);

  $curlErr = curl_error($ch);
  curl_close($ch);

  if ($response === false) {
    http_response_code(500);
    echo json_encode(['error' => 'cURL error: ' . $curlErr]);
    return;
  }

  // Decode response  
  $result = json_decode($response, true);

  // Extract text safely (Gemini has multiple formats)
  $text = null;
  if (isset($result['candidates'][0]['content']['parts'][0]['text'])) {
    $text = $result['candidates'][0]['content']['parts'][0]['text'];
  } else if (isset($result['candidates'][0]['output'][0]['text'])) {
    $text = $result['candidates'][0]['output'][0]['text'];
  } else if (isset($result['candidates'][0]['text'])) {
    $text = $result['candidates'][0]['text'];
  }

  if (!$text) {
    http_response_code(500);
    echo json_encode([
      'error' => 'Invalid response from AI',
      'raw' => $result
    ]);
    return;
  }

  $jsonPlanText = $result['candidates'][0]['content']['parts'][0]['text'];

  // Hapus code block markdown jika ada
  $jsonPlanText = preg_replace('/```json|```/', '', $jsonPlanText);
  $jsonPlanText = trim($jsonPlanText);

  header('Content-Type: application/json');

  if ($decoded = json_decode($jsonPlanText, true)) {
    echo json_encode($decoded);
  } else {
    http_response_code(500);
    echo json_encode([
      'error' => 'AI returned non-JSON response',
      'raw' => $jsonPlanText
    ]);
  }
}
