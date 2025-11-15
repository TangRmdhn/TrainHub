<?php
session_start();
header('Content-Type: application/json');
include 'koneksi.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}
$user_id = $_SESSION['user_id'];

// Ambil query rentang tanggal (start dan end)
$start_date = $_GET['start'] ?? date('Y-m-d');
$end_date = $_GET['end'] ?? date('Y-m-d', strtotime($start_date . ' +6 days'));

$plans = [];
// Querynya diganti jadi BETWEEN
$sql = "SELECT id, plan_date, plan_name, plan_json, is_completed 
        FROM user_plans 
        WHERE user_id = ? AND plan_date BETWEEN ? AND ?";
        
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("iss", $user_id, $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // Kita decode JSON per hari di sini
        $row['plan_json'] = json_decode($row['plan_json'], true); 
        $plans[] = $row;
    }
}

$stmt->close();
$koneksi->close();

echo json_encode($plans);
?>