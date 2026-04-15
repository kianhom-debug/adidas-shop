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
            $_SESSION['role'] = $user['role'];
            
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
            exit();
            
            header('Location: profile.php');
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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('../uploads/images/bg(1).jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .login-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 100%;
            max-width: 450px;
            padding: 40px;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo h1 {
            font-size: 32px;
            letter-spacing: 3px;
            color: #000;
        }
        
        .logo p {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        
        .error-message {
            background: #f8d7da;
            color: #721c24;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #f5c6cb;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #0066cc;
        }
        
        .captcha-box {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .captcha-question {
            background: #e9ecef;
            padding: 10px;
            border-radius: 4px;
            font-size: 18px;
            margin-bottom: 10px;
            text-align: center;
            font-weight: bold;
        }
        
        .checkbox {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        
        .checkbox input {
            width: auto;
            margin-right: 10px;
        }
        
        .checkbox label {
            margin-bottom: 0;
            cursor: pointer;
        }
        
        .btn-login {
            width: 100%;
            padding: 12px;
            background: #000;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn-login:hover {
            background: #0066cc;
        }
        
        .register-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        
        .register-link a {
            color: #0066cc;
            text-decoration: none;
        }
        
        .register-link a:hover {
            text-decoration: underline;
        }
        
        /* Debug info - remove after testing */
        .debug {
            background: #f0f0f0;
            padding: 10px;
            margin-bottom: 20px;
            font-size: 12px;
            border-radius: 5px;
            display: none;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>ADIDAS</h1>
            <p>Login to your account</p>
        </div>
        
        <!-- Debug info (hidden, remove after testing) -->
        <div class="debug">
            Session ID: <?= session_id() ?><br>
            CAPTCHA Answer: <?= $_SESSION['captcha_answer'] ?? 'NOT SET' ?>
        </div>
        
        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" required>
            </div>
            
            <div class="captcha-box">
                <label>Security Check</label>
                <div class="captcha-question">
                    <?= htmlspecialchars($captcha_question) ?> = ?
                </div>
                <input type="text" name="captcha" placeholder="Enter the answer" required style="width:100%;padding:10px;border:1px solid #ddd;border-radius:4px;">
            </div>
            
            <div class="checkbox">
                <input type="checkbox" name="remember" id="remember">
                <label for="remember">Remember Me</label>
            </div>
            
            <button type="submit" class="btn-login">Login</button>
        </form>
        
        <div class="register-link">
            Don't have an account? <a href="register.php">Register</a>
        </div>
    </div>
</body>
</html>