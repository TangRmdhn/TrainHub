<?php
session_start();
include '../koneksi.php';

// Pastikan user sudah login
if (!isset($_SESSION['login_status']) || $_SESSION['login_status'] !== true) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    
    // Ambil data dari form
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $weight = $_POST['weight'];
    $height = $_POST['height'];
    $fitness_goal = $_POST['fitness_goal'];
    $fitness_level = $_POST['fitness_level'];
    $equipment_access = $_POST['equipment_access'];
    $days_per_week = $_POST['days_per_week'];
    $minutes_per_session = $_POST['minutes_per_session'];
    $injuries = mysqli_real_escape_string($koneksi, $_POST['injuries']);

    // Query Update ke table users
    $sql = "UPDATE users SET 
            gender = '$gender',
            age = '$age',
            weight = '$weight',
            height = '$height',
            fitness_goal = '$fitness_goal',
            fitness_level = '$fitness_level',
            equipment_access = '$equipment_access',
            days_per_week = '$days_per_week',
            minutes_per_session = '$minutes_per_session',
            injuries = '$injuries'
            WHERE id = '$user_id'";

    if ($koneksi->query($sql) === TRUE) {
        // Sukses simpan data, arahkan ke dashboard
        // Nanti di dashboard (app.php) kita tinggal fetch data ini buat dikirim ke Python
        header("Location: app.php");
    } else {
        echo "Error updating record: " . $koneksi->error;
    }

    $koneksi->close();
}
?>
