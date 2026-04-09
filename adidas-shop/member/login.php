<?php
// 开启错误显示（开发阶段用）
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../config.php'

session_start();
require_once '../config.php';

// 处理登录
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // 查用户
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    // 验证密码
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user'] = $user;

        echo "<p style='color:green;'>Login success!</p>";
    } else {
        echo "<p style='color:red;'>Invalid email or password!</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
</head>
<body>

<h2>Login</h2>

<form method="POST">
    Email: <input type="email" name="email" required><br><br>
    Password: <input type="password" name="password" required><br><br>
    <button type="submit">Login</button>
</form>

</body>
</html>