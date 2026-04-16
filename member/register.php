<?php
session_start();
require_once '../config.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    // Validation
    $errors = [];

    if (empty($name)) {
        $errors[] = "Full name is required.";
    } 

    if (empty($email)) {
        $errors[] = "Email address is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters. (Current length: " . strlen($password) . ")";
    }

    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $error = "Email already exists!";
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            if ($stmt->execute([$name, $email, $hash])) {
                $success = "Registration successful! <a href='login.php'>Login here</a>";
            } else {
                $error = "Registration failed. Please try again.";
            }
        }
    } else {
        $error = implode("<br>", $errors);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Adidas Shop</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="logo">
                <h1>ADIDAS</h1>
                <p>Create your account</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= $error ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="form-group">
                    <label>Full Name (max 100 characters)</label>
                    <input type="text" name="name" maxlength="100" required>
                </div>
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" required>
                </div>
                <div class="form-group">
                    <label>Password (minimum 6 characters)</label>
                    <input type="password" name="password" minlength="6" required>
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn-primary">Register</button>
            </form>
            
            <div class="auth-link">
                Already have an account? <a href="login.php">Login</a>
            </div>
        </div>
    </div>
</body>
</html>