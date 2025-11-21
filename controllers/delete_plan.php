<?php
session_start();
header('Content-Type: application/json');
include '../koneksi.php';

$response = ['success' => false, 'message' => 'Permintaan tidak valid.'];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Sesi tidak valid, silakan login ulang.';
    echo json_encode($response);
    header("Location: /login");
    exit;
}
$user_id = $_SESSION['user_id'];

// Ambil data dari body request (JSON)
$data = json_decode(file_get_contents('php://input'), true);
$plan_id = $data['plan_id'] ?? null;

if ($plan_id) {
    // Query hapus, pastikan cuma user yang punya yang bisa hapus
    $sql = "DELETE FROM user_plans WHERE id = ? AND user_id = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ii", $plan_id, $user_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            $response['success'] = true;
            $response['message'] = 'Rencana berhasil dihapus.';
        } else {
            $response['message'] = 'Gagal menghapus: Rencana tidak ditemukan atau bukan milik Anda.';
        }
    } else {
        $response['message'] = 'Gagal menghapus: ' . $stmt->error;
    }
    $stmt->close();
} else {
    $response['message'] = 'ID Rencana tidak ditemukan.';
}

$koneksi->close();
echo json_encode($response);
