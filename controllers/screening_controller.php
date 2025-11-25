<!-- API untuk menyimpan data screening -->

<?php
session_start();
include '../koneksi.php';
include '../config.php';

// Pastiin user udah login
if (!isset($_SESSION['login_status']) || $_SESSION['login_status'] !== true) {
    header("Location: " . url("/login"));
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

    // Cek apakah data profile udah ada
    $checkSql = "SELECT id FROM user_profiles WHERE user_id = '$user_id'";
    $checkResult = $koneksi->query($checkSql);

    if ($checkResult->num_rows > 0) {
        // Update
        $sql = "UPDATE user_profiles SET 
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
                WHERE user_id = '$user_id'";
    } else {
        // Insert
        $sql = "INSERT INTO user_profiles (user_id, gender, age, weight, height, fitness_goal, fitness_level, equipment_access, days_per_week, minutes_per_session, injuries)
                VALUES ('$user_id', '$gender', '$age', '$weight', '$height', '$fitness_goal', '$fitness_level', '$equipment_access', '$days_per_week', '$minutes_per_session', '$injuries')";
    }

    if ($koneksi->query($sql) === TRUE) {
        // Sukses simpan data, arahkan ke dashboard
        header("Location: " . url("/app"));
    } else {
        echo "Error updating record: " . $koneksi->error;
    }

    $koneksi->close();
}
