<?php
session_start();
require_once '../config.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if ($password != $confirm) {
        $error = "Passwords do not match!";
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "Email already exists!";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $stmt->execute([$name, $email, $hash]);
            $success = "Registration successful! <a href='login.php'>Login here</a>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Adidas Shop</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        
        .register-container {
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
        
        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border: 1px solid #c3e6cb;
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
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #0066cc;
        }
        
        .btn-register {
            width: 100%;
            padding: 12px;
            background: #000;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .btn-register:hover {
            background: #0066cc;
        }
        
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        
        .login-link a {
            color: #0066cc;
            text-decoration: none;
        }
        
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="register-container">
        <div class="logo">
            <h1>ADIDAS</h1>
            <p>Create your account</p>
        </div>
        
        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message"><?= $success ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label>Full Name</label>
                <input type="text" name="name" required>
            </div>
            
            <div class="form-group">
                <label>Email Address</label>
                <input type="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" required>
            </div>
            
            <button type="submit" class="btn-register">Register</button>
        </form>
        
        <div class="login-link">
            Already have an account? <a href="login.php">Login</a>
        </div>
    </div>
</body>
</html>