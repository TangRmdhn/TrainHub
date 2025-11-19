<?php
session_start();
header('Content-Type: application/json');
include '../koneksi.php';

$response = ['success' => false, 'message' => 'Permintaan tidak valid.'];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Sesi tidak valid, silakan login ulang.';
    echo json_encode($response);
    exit;
}
$user_id = $_SESSION['user_id'];

// Ambil data dari body request (JSON)
$data = json_decode(file_get_contents('php://input'), true);
$plan_id = $data['plan_id'] ?? null;
$new_date = $data['new_date'] ?? null;

if ($plan_id && $new_date) {
    
    // 1. Cek dulu, tanggal baru udah keisi belum?
    $check = "SELECT id FROM user_plans WHERE user_id = ? AND plan_date = ?";
    $stmt_check = $koneksi->prepare($check);
    $stmt_check->bind_param("is", $user_id, $new_date);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        // Tanggal udah keisi
        $response['message'] = 'Gagal pindah: Tanggal ' . $new_date . ' sudah ada jadwal lain.';
    } else {
        // 2. Kalo aman, baru UPDATE
        $sql = "UPDATE user_plans SET plan_date = ? WHERE id = ? AND user_id = ?";
        $stmt_update = $koneksi->prepare($sql);
        $stmt_update->bind_param("sii", $new_date, $plan_id, $user_id);
        
        if ($stmt_update->execute()) {
            if ($stmt_update->affected_rows > 0) {
                $response['success'] = true;
                $response['message'] = 'Jadwal berhasil dipindah.';
            } else {
                $response['message'] = 'Rencana tidak ditemukan atau tidak ada perubahan.';
            }
        } else {
            $response['message'] = 'Gagal update database: ' . $stmt_update->error;
        }
        $stmt_update->close();
    }
    $stmt_check->close();

} else {
    $response['message'] = 'Data plan ID atau tanggal baru tidak lengkap.';
}

$koneksi->close();
echo json_encode($response);
?>

