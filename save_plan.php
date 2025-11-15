<?php
session_start();
header('Content-Type: application/json');
include 'koneksi.php';

$response = ['success' => false, 'message' => 'Permintaan tidak valid.'];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Sesi tidak valid, silakan login ulang.';
    echo json_encode($response);
    exit;
}
$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $plan_name_template = mysqli_real_escape_string($koneksi, $_POST['plan_name']);
    $plan_json_template = $_POST['plan_json']; // Ini JSON string 7-hari
    $start_date_string = $_POST['start_date']; // Ini tanggal SENIN

    // 1. Decode JSON template dari AI
    $template = json_decode($plan_json_template, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $response['message'] = 'Error: Format data rencana (JSON) tidak valid.';
        echo json_encode($response);
        exit;
    }

    $weekly_schedule = $template['weekly_schedule'] ?? [];
    $start_date = new DateTime($start_date_string);
    $koneksi->begin_transaction(); // Mulai transaksi, biar aman

    try {
        $inserted_count = 0;
        
        // 2. Loop 7 hari (Senin-Minggu)
        for ($i = 0; $i < 7; $i++) {
            $current_date = clone $start_date;
            $current_date->modify("+$i days"); // Tambah 0 hari, 1 hari, 2 hari...
            $current_date_string = $current_date->format('Y-m-d');
            
            $day_template = $weekly_schedule[$i] ?? null; // Ambil data hari ke-i
            
            // Kalo hari libur (is_off_day == true), kita GAK USAH simpen ke DB.
            if ($day_template && $day_template['is_off_day'] === false) {
                
                // 3. Cek dulu, tanggal ini udah keisi belum?
                $check = "SELECT id FROM user_plans WHERE user_id = ? AND plan_date = ?";
                $stmt_check = $koneksi->prepare($check);
                $stmt_check->bind_param("is", $user_id, $current_date_string);
                $stmt_check->execute();
                $result = $stmt_check->get_result();
                $stmt_check->close();

                if ($result->num_rows > 0) {
                    // KALO UDAH KEISI:
                    // Kita anggap user mau TIMPA/UPDATE jadwalnya
                    $existing_plan = $result->fetch_assoc();
                    $plan_id_to_update = $existing_plan['id'];
                    
                    $plan_name = $day_template['session_title'] ?? 'Latihan';
                    $plan_json_daily = json_encode($day_template); // Simpan JSON per hari
                    
                    $sql_update = "UPDATE user_plans SET plan_name = ?, plan_json = ? WHERE id = ?";
                    $stmt_update = $koneksi->prepare($sql_update);
                    $stmt_update->bind_param("ssi", $plan_name, $plan_json_daily, $plan_id_to_update);
                    $stmt_update->execute();
                    $stmt_update->close();
                    
                } else {
                    // KALO BELUM KEISI: INSERT BARU
                    $plan_name = $day_template['session_title'] ?? 'Latihan';
                    $plan_json_daily = json_encode($day_template); // Simpan JSON per hari
                    
                    $sql_insert = "INSERT INTO user_plans (user_id, plan_name, plan_date, plan_json) VALUES (?, ?, ?, ?)";
                    $stmt_insert = $koneksi->prepare($sql_insert);
                    $stmt_insert->bind_param("isss", $user_id, $plan_name, $current_date_string, $plan_json_daily);
                    $stmt_insert->execute();
                    $stmt_insert->close();
                }
                $inserted_count++;
            }
        }

        // 4. Selesai
        $koneksi->commit(); // Simpan semua perubahan
        $response['success'] = true;
        $response['message'] = "Berhasil menerapkan $inserted_count sesi latihan ke kalender!";
        $response['date'] = $start_date_string; // Tanggal buat highlight

    } catch (Exception $e) {
        $koneksi->rollback(); // Batalin semua kalo ada 1 yang error
        $response['message'] = 'Gagal menyimpan ke database: ' . $e->getMessage();
    }
}

$koneksi->close();
echo json_encode($response);
?>