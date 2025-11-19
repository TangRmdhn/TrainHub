<?php
// regist_controller.php
session_start();
include '../koneksi.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($koneksi, $_POST['username']);
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    // 1. Cek apakah email sudah terdaftar
    $checkEmail = "SELECT * FROM users WHERE email = '$email'";
    $result = $koneksi->query($checkEmail);

    if ($result->num_rows > 0) {
        echo "<script>
                alert('Email sudah terdaftar! Silakan gunakan email lain.');
                window.location.href='register.php';
              </script>";
        exit;
    }

    // 2. Enkripsi password (Hashing)
    // Jangan pernah simpan password mentah, Tang!
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // 3. Masukkan data ke database
    $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashed_password')";

    if ($koneksi->query($sql) === TRUE) {
        echo "<script>
                alert('Registrasi berhasil! Silakan login.');
                window.location.href='../views/login.php';
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $koneksi->error;
    }

    $koneksi->close();
}
?>

