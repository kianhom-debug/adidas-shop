<?php
session_start();
require_once '../config.php';

// Check if user is logged in
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
    
    // Verify current password
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
        } else {
            $errors['general'] = 'Failed to change password';
        }
    }
}

include '../header.php';
?>

<div class="profile-container" style="display:flex;max-width:1200px;margin:0 auto;padding:40px 20px;">
    <div class="profile-sidebar" style="flex:0 0 280px;">
        <div class="profile-menu">
            <ul style="list-style:none;padding:0;">
                <li><a href="profile.php" style="display:block;padding:10px;color:#333;text-decoration:none;">Profile Info</a></li>
                <li><a href="profile_edit.php" style="display:block;padding:10px;color:#333;text-decoration:none;">Edit Profile</a></li>
                <li><a href="change_password.php" style="display:block;padding:10px;background:#0066cc;color:white;text-decoration:none;border-radius:4px;">Change Password</a></li>
                <li><a href="logout.php" style="display:block;padding:10px;color:#333;text-decoration:none;">Logout</a></li>
            </ul>
        </div>
    </div>
    
    <div class="profile-content" style="flex:1;background:#fff;border-radius:8px;padding:30px;margin-left:30px;box-shadow:0 2px 10px rgba(0,0,0,0.1);">
        <?php if ($success): ?>
            <div class="alert alert-success" style="background:#d4edda;color:#155724;padding:12px;border-radius:4px;margin-bottom:20px;"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error" style="background:#f8d7da;color:#721c24;padding:12px;border-radius:4px;margin-bottom:20px;">
                <?php foreach ($errors as $error): ?>
                    <p style="margin:0;"><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <div class="profile-form">
            <h2 style="margin-top:0;">Change Password</h2>
            
            <form method="POST" action="">
                <div class="form-group" style="margin-bottom:15px;">
                    <label for="current_password" style="display:block;margin-bottom:5px;">Current Password</label>
                    <input type="password" name="current_password" id="current_password" 
                           style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;" required>
                </div>
                
                <div class="form-group" style="margin-bottom:15px;">
                    <label for="new_password" style="display:block;margin-bottom:5px;">New Password</label>
                    <input type="password" name="new_password" id="new_password" 
                           style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;" required>
                    <small style="color:#666;">Password must be at least 6 characters</small>
                </div>
                
                <div class="form-group" style="margin-bottom:15px;">
                    <label for="confirm_password" style="display:block;margin-bottom:5px;">Confirm New Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" 
                           style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;" required>
                </div>
                
                <button type="submit" style="background:#0066cc;color:white;padding:10px 20px;border:none;border-radius:4px;cursor:pointer;">Update Password</button>
                <a href="profile.php" style="background:#6c757d;color:white;padding:10px 20px;text-decoration:none;border-radius:4px;margin-left:10px;">Cancel</a>
            </form>
        </div>
    </div>
</div>

<script>
document.getElementById('confirm_password').addEventListener('keyup', function() {
    var password = document.getElementById('new_password').value;
    var confirm = this.value;
    
    if (password !== confirm) {
        this.style.borderColor = 'red';
    } else {
        this.style.borderColor = 'green';
    }
});
</script>

<?php include '../footer.php'; ?>