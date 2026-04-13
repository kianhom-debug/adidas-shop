<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    if (!password_verify($currentPassword, $user['password'])) {
        $errors['current_password'] = 'Current password is incorrect';
    }
    
    if (empty($newPassword)) {
        $errors['new_password'] = 'New password is required';
    } elseif (strlen($newPassword) < 6) {
        $errors['new_password'] = 'Password must be at least 6 characters';
    }
    
    if ($newPassword !== $confirmPassword) {
        $errors['confirm_password'] = 'Passwords do not match';
    }
    
    if (empty($errors)) {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        if ($stmt->execute([$hashedPassword, $userId])) {
            $success = 'Password changed successfully!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Adidas Shop</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="../index.php">ADIDAS</a>
            </div>
            <div class="header-actions">
                <a href="profile.php">← Back to Profile</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </header>

    <div class="profile-container">
        <div class="profile-sidebar">
            <div class="profile-menu">
                <ul>
                    <li><a href="profile.php">Profile Info</a></li>
                    <li><a href="profile_edit.php">Edit Profile</a></li>
                    <li><a href="change_password.php" class="active">Change Password</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
        
        <div class="profile-content">
            <?php if ($success): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="alert alert-error">
                    <?php foreach ($errors as $error): ?>
                        <p><?= $error ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            
            <div class="profile-form">
                <h2>Change Password</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" required>
                        <small style="color:#666;">Password must be at least 6 characters</small>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" class="btn-primary">Update Password</button>
                    <a href="profile.php" class="btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>

    <footer class="main-footer">
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; 2024 Adidas Shop. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>