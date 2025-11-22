<?php
session_start();
include '../koneksi.php';
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    // 1. Cari user berdasarkan email (Prepared Statement)
    $stmt = $koneksi->prepare("SELECT id, username, email, password, fitness_goal FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // 2. Verifikasi Password
        if (password_verify($password, $row['password'])) {
            // Prevent Session Fixation
            session_regenerate_id(true);

            // Set Session Login
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['login_status'] = true;

            // 3. CEK SCREENING (LOGIC BARU)
            // Kita cek apakah user sudah pernah isi screening (cek kolom fitness_goal)
            if (empty($row['fitness_goal'])) {
                // Kalau data masih kosong, arahkan ke Screening
                echo "<script>
                        alert('Login berhasil! Yuk, lengkapi data latihanmu dulu agar AI bisa bekerja.');
                        window.location.href='" . url("/screening") . "';
                      </script>";
            } else {
                // Kalau data sudah ada, langsung gass ke Dashboard
                header("Location: " . url("/app"));
            }
            exit;
        } else {
            // Password salah
            echo "<script>
                    alert('Password salah! Coba lagi.');
                    window.location.href='" . url("/login") . "';
                  </script>";
        }
    } else {
        // Email tidak ditemukan
        echo "<script>
                alert('Email belum terdaftar. Daftar terlebih dahulu!');
                window.location.href='" . url("/login") . "';
              </script>";
    }

    $koneksi->close();
}
