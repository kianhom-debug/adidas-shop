<?php
session_start();
require_once '../config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Get user data
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Handle form submission
$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    if (empty($fullName)) {
        $errors['full_name'] = 'Full name is required';
    }
    if (empty($username)) {
        $errors['username'] = 'Username is required';
    } elseif (strlen($username) < 3) {
        $errors['username'] = 'Username must be at least 3 characters';
    }
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }
    
    if (empty($errors)) {
        $stmt = $pdo->prepare("UPDATE users SET full_name = ?, username = ?, email = ? WHERE id = ?");
        if ($stmt->execute([$fullName, $username, $email, $userId])) {
            $_SESSION['user_name'] = $username;
            $_SESSION['user_email'] = $email;
            $success = 'Profile updated successfully!';
            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
        } else {
            $errors['general'] = 'Failed to update profile';
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
                <li><a href="profile_edit.php" style="display:block;padding:10px;background:#0066cc;color:white;text-decoration:none;border-radius:4px;">Edit Profile</a></li>
                <li><a href="change_password.php" style="display:block;padding:10px;color:#333;text-decoration:none;">Change Password</a></li>
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
            <h2 style="margin-top:0;">Edit Profile</h2>
            
            <form method="POST" action="">
                <div class="form-group" style="margin-bottom:15px;">
                    <label for="username" style="display:block;margin-bottom:5px;">Username</label>
                    <input type="text" name="username" id="username" 
                           value="<?= htmlspecialchars($user['username']) ?>" 
                           style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;" required>
                </div>
                
                <div class="form-group" style="margin-bottom:15px;">
                    <label for="full_name" style="display:block;margin-bottom:5px;">Full Name</label>
                    <input type="text" name="full_name" id="full_name" 
                           value="<?= htmlspecialchars($user['full_name']) ?>" 
                           style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;" required>
                </div>
                
                <div class="form-group" style="margin-bottom:15px;">
                    <label for="email" style="display:block;margin-bottom:5px;">Email Address</label>
                    <input type="email" name="email" id="email" 
                           value="<?= htmlspecialchars($user['email']) ?>" 
                           style="width:100%;padding:8px;border:1px solid #ddd;border-radius:4px;" required>
                </div>
                
                <button type="submit" style="background:#0066cc;color:white;padding:10px 20px;border:none;border-radius:4px;cursor:pointer;">Save Changes</button>
                <a href="profile.php" style="background:#6c757d;color:white;padding:10px 20px;text-decoration:none;border-radius:4px;margin-left:10px;">Cancel</a>
            </form>
        </div>
    </div>
</div>

<?php include '../footer.php'; ?>