<?php
// regist_controller.php
session_start();
include '../koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    // 1. Cek apakah email sudah terdaftar (Prepared Statement)
    $stmtCheck = $koneksi->prepare("SELECT email FROM users WHERE email = ?");
    $stmtCheck->bind_param("s", $email);
    $stmtCheck->execute();
    $result = $stmtCheck->get_result();

    if ($result->num_rows > 0) {
        echo "<script>
                alert('Email sudah terdaftar! Silakan gunakan email lain.');
                window.location.href='register.php';
              </script>";
        exit;
    }

    // 2. Enkripsi password (Hashing)
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 3. Masukkan data ke database (Prepared Statement)
    $stmtInsert = $koneksi->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmtInsert->bind_param("sss", $username, $email, $hashed_password);

    if ($stmtInsert->execute()) {
        echo "<script>
                alert('Registrasi berhasil! Silakan login.');
                window.location.href='../views/login.php';
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $koneksi->error;
    }

    $koneksi->close();
}
