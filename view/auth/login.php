<?php
session_start();
include '../../functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $conn = connectDB();
    $stmt = $conn->prepare("SELECT id, username, password FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $username, $hashed_password);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) { 
            $_SESSION['user_id'] = $id;
            $_SESSION['username'] = $username;
            header("Location: ../pages/dashboard.php");
            exit;
        } else {
            echo "Password salah!";
        }
    } else {
        echo "Email tidak ditemukan!";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="shortcut icon" type="image/png" href="../../assets/images/favicon.png">
    <title>Login</title>
</head>
<body>
    <section>
        <div class="login-box">
            <form action="" method="post">
                <h2>LOGIN</h2>
                <div class="input-box">
                    <input type="text" name="email" id="email" required>
                    <label style="color: white;">Email</label> 
                </div>
                <div class="input-box">
                    <input type="password" name="password" id="password" required>
                    <label style="color: white;">Password</label> 
                </div>
                <button>Login</button> 
                <div class="register-link">
                    <p>Don't hava a account?<a href="/view/auth/register.php"> Register</a></p>
                </div>
            </form>
        </div>
    </section>
</body>
</html>