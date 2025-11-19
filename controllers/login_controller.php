<?php
session_start();
include '../koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    // 1. Cari user berdasarkan email
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $koneksi->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // 2. Verifikasi Password
        if (password_verify($password, $row['password'])) {
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
                        window.location.href='screening.php';
                      </script>";
            } else {
                // Kalau data sudah ada, langsung gass ke Dashboard
                header("Location: app.php");
            }
            exit;
        } else {
            // Password salah
            echo "<script>
                    alert('Password salah bro! Coba inget-inget lagi.');
                    window.location.href='login.php';
                  </script>";
        }
    } else {
        // Email tidak ditemukan
        echo "<script>
                alert('Email belum terdaftar. Daftar dulu gih!');
                window.location.href='login.php';
              </script>";
    }

    $koneksi->close();
}
?>
