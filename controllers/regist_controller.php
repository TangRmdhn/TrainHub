<!-- API untuk registrasi -->

<?php
session_start();
include '../koneksi.php';
include '../config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    // 1. Cek apakah email udah terdaftar (Prepared Statement)
    $stmtCheck = $koneksi->prepare("SELECT email FROM users WHERE email = ?");
    $stmtCheck->bind_param("s", $email);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
                alert('Email sudah terdaftar! Silakan gunakan email lain.');
                window.location.href='" . url("/register") . "';
              </script>";
        exit;
    }

    // 2. Enkripsi password (Hashing)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 3. Masukin data ke database (Prepared Statement)
    $stmtInsert = $koneksi->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmtInsert->bind_param("sss", $username, $email, $hashed_password);

    if ($stmtInsert->execute()) {
        echo "<script>
                alert('Registrasi berhasil! Silakan login.');
                window.location.href='" . url("/login") . "';
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $koneksi->error;
    }

    $koneksi->close();
}
