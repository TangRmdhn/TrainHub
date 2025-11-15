<?php
// File: api/index.php (Versi UPDATED - Tambah Plans Endpoint)

// Set header global
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

// Bawa koneksi DB dan Middleware
require_once 'config/database.php'; 
require_once 'core/auth_middleware.php';

$path = $_GET['path'] ?? '';
$request_method = $_SERVER['REQUEST_METHOD'];
$auth_data = null; 

// Router
switch ($path) {
    // === Auth Endpoints (Publik) ===
    case 'auth/login':
        if ($request_method == 'POST') {
            require_once 'controllers/auth_controller.php';
            loginUser($db_connection);
        }
        break;
        
    case 'auth/register':
        if ($request_method == 'POST') {
            require_once 'controllers/auth_controller.php';
            registerUser($db_connection);
        }
        break;

    // === AI Endpoint (Protected) ===
    case 'ai/generate-plan':
        $auth_data = checkAuth();
        if ($auth_data && $request_method == 'POST') {
            require_once 'controllers/ai_controller.php';
            generateAIPlan($db_connection); 
        }
        break;

    // === Exercise Endpoints (Protected) ===
    case 'exercises':
        $auth_data = checkAuth();
        if ($auth_data && $request_method == 'GET') {
            require_once 'controllers/exercise_controller.php';
            getAllExercises($db_connection);
        }
        break;

    // === Workout Plan Endpoints (Fitur 3) (Protected) ===
    case 'workout-plan':
        $auth_data = checkAuth();
        if ($auth_data) {
            $user_id = $auth_data['user_id'];
            require_once 'controllers/workout_controller.php';

            if ($request_method == 'POST') {
                saveWorkoutPlan($db_connection, $user_id);
            } 
            elseif ($request_method == 'GET') {
                getWorkoutPlans($db_connection, $user_id);
            }
        }
        break;
    
    // === [BARU] Plans Management Endpoints (Protected) ===
    case 'plans':
        $auth_data = checkAuth();
        if ($auth_data) {
            $user_id = $auth_data['user_id'];
            require_once 'controllers/plan_controller.php';

            // GET /api/plans -> Ambil semua plans
            if ($request_method == 'GET' && !isset($_GET['plan_id'])) {
                getUserPlans($db_connection, $user_id);
            }
            // GET /api/plans?plan_id=123 -> Ambil detail 1 plan
            elseif ($request_method == 'GET' && isset($_GET['plan_id'])) {
                getPlanDetail($db_connection, $user_id, $_GET['plan_id']);
            }
            // DELETE /api/plans?plan_id=123 -> Hapus plan
            elseif ($request_method == 'DELETE' && isset($_GET['plan_id'])) {
                deletePlan($db_connection, $user_id, $_GET['plan_id']);
            }
        }
        break;
        
    // === Calendar Endpoints (Fitur 5) (Protected) ===
    case 'calendar':
        $auth_data = checkAuth();
        if ($auth_data) {
            $user_id = $auth_data['user_id'];
            require_once 'controllers/calendar_controller.php';

            if ($request_method == 'POST') {
                schedulePlanToDate($db_connection, $user_id);
            } 
            elseif ($request_method == 'GET' && isset($_GET['month'])) {
                getSchedulesForMonth($db_connection, $user_id, $_GET['month']);
            }
        }
        break;

    case 'calendar/complete':
        $auth_data = checkAuth();
        if ($auth_data && $request_method == 'PUT' && isset($_GET['schedule_id'])) {
            $user_id = $auth_data['user_id'];
            require_once 'controllers/calendar_controller.php';
            markScheduleAsComplete($db_connection, $user_id, $_GET['schedule_id']);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}

// Tutup koneksi DB di akhir script
$db_connection->close();
