<?php
session_start();
include '../../functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!preg_match("/^[a-zA-Z0-9._%+-]+@bpjs\.com$/", $email)) {
        echo "Email harus menggunakan domain @bpjs.com!";
        exit;
    }

    if ($password !== $confirm_password) {
        echo "Konfirmasi password tidak cocok!";
        exit;
    }

    $hashed_password = password_hash($password, PASSWORD_DEFAULT); 

    $conn = connectDB();
    $stmt = $conn->prepare("INSERT INTO users (email, username, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $username, $hashed_password);

    if ($stmt->execute()) {
        header("Location: login.php");
        exit;
    } else {
        echo "Gagal registrasi! Mungkin email sudah digunakan.";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="shortcut icon" type="image/png" href="../../assets/images/favicon.png">
    <title>Register</title>
</head>
<body>
    <section>
        <div class="register-box">
            <form method="post">
                <h2>REGISTER</h2>
                <div class="input-box">
                    <input type="text" name="email" id="email" required>
                    <label style="color: white;">Email</label> 
                </div>
                <div class="input-box">
                    <input type="text" name="username" id="username" required>
                    <label style="color: white;">Username</label> 
                </div>
                <div class="input-box">
                    <input type="password" name="password" id="password" required>
                    <label style="color: white;">Password</label> 
                </div>
                <div class="input-box">
                    <input type="password" name="confirm_password" id="confirm_password" required>
                    <label style="color: white;">Confirm Password</label> 
                </div>
                <button type="submit">Register</button> 
                <div class="register-link">
                    <p>I hava a account? <a href="/view/auth/login.php">Login</a></p>
                </div>
            </form>
        </div>
    </section>
</body>
</html>