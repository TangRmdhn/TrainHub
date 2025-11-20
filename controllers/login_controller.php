<?php
session_start();
include '../koneksi.php';

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
                        window.location.href='../views/screening.php';
                      </script>";
            } else {
                // Kalau data sudah ada, langsung gass ke Dashboard
                header("Location: ../views/app.php");
            }
            exit;
        } else {
            // Password salah
            echo "<script>
                    alert('Password salah bro! Coba inget-inget lagi.');
                    window.location.href='../views/login.php';
                  </script>";
        }
    } else {
        // Email tidak ditemukan
        echo "<script>
                alert('Email belum terdaftar. Daftar dulu gih!');
                window.location.href='../views/login.php';
              </script>";
    }

    $koneksi->close();
}
