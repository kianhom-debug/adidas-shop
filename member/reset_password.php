<?php
session_start();
require_once '../config.php';

$token = $_GET['token'] ?? '';
$tokenRow = null;

// Delete expired tokens
$pdo->prepare("DELETE FROM token WHERE expire < NOW()")->execute();

if ($token) {
    $stmt = $pdo->prepare("SELECT * FROM token WHERE id = ?");
    $stmt->execute([$token]);
    $tokenRow = $stmt->fetch();
}

// Process password update if token is valid
if ($_SERVER["REQUEST_METHOD"] == "POST" && $tokenRow) {
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (strlen($password) >= 6 && $password === $confirm) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$hash, $tokenRow['user_id']]);
        $pdo->prepare("DELETE FROM token WHERE id = ?")->execute([$token]);
        // Optionally redirect to login after success (no message)
        // header('Location: login.php');
        // exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
<div class="auth-container">
    <div class="auth-box">
        <div class="logo"><h1>ADIDAS</h1><p>Create new password</p></div>

        <!-- Always show the form, no messages -->
        <form method="POST">
            <div class="form-group"><label>New Password</label><input type="password" name="password" minlength="6" required></div>
            <div class="form-group"><label>Confirm Password</label><input type="password" name="confirm_password" required></div>
            <button type="submit" class="btn-primary">Reset Password</button>
        </form>

        <div class="auth-link"><a href="login.php">← Back to Login</a></div>
    </div>
</div>
</body>
</html>