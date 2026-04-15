<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
require_once '../config.php';
require_once '../CaptchaHelper.php';
require_once '../RememberToken.php';

$error = '';
$captcha_question = CaptchaHelper::generateMathCaptcha();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $captcha_answer = $_POST['captcha'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (!CaptchaHelper::verifyMathCaptcha($captcha_answer)) {
        $error = "Invalid CAPTCHA! Please try again.";
        $captcha_question = CaptchaHelper::generateMathCaptcha();
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role'] = $user['role'] ?? 'user';
            
            if ($remember) {
                $rememberToken = new RememberToken($pdo);
                $token = $rememberToken->create($user['id']);
                setcookie('remember_token', $token, time() + 86400 * 30, '/');
            }
            
            if ($_SESSION['role'] === 'admin') {
                header("Location: ../admin/index.php");
            } else {
                header('Location: profile.php');
            }
            exit;
        } else {
            $error = "Invalid email or password!";
            $captcha_question = CaptchaHelper::generateMathCaptcha();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Adidas Shop</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="logo">
                <h1>ADIDAS</h1>
                <p>Login to your account</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                
                <div class="captcha-group">
                    <label>Security Check</label>
                    <div class="captcha-question">
                        <?= htmlspecialchars($captcha_question) ?> = ?
                    </div>
                    <input type="text" name="captcha" placeholder="Enter the answer" required>
                </div>
                
                <div class="checkbox">
                    <input type="checkbox" name="remember" id="remember">
                    <label for="remember">Remember Me</label>
                </div>
                
                <button type="submit" class="btn-primary">Login</button>
            </form>
            
            <div class="auth-link">
                <a href="forgot_password.php">Forgot Password?</a>
            </div>
            <div class="auth-link">
                Don't have an account? <a href="register.php">Register</a>
            </div>
        </div>
    </div>
</body>
</html>