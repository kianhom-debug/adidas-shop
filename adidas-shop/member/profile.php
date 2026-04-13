<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

$success = $_SESSION['profile_success'] ?? '';
$error = $_SESSION['profile_error'] ?? '';
unset($_SESSION['profile_success'], $_SESSION['profile_error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Adidas Shop</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/profile.js"></script>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="../index.php">ADIDAS</a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="#">SHOES</a></li>
                    <li><a href="#">MEN</a></li>
                    <li><a href="#">WOMEN</a></li>
                    <li><a href="#">KIDS</a></li>
                </ul>
            </nav>
            <div class="header-actions">
                <a href="profile.php">👤 <?= htmlspecialchars($_SESSION['user_name']) ?></a>
                <a href="logout.php">🚪 Logout</a>
            </div>
        </div>
    </header>

    <div class="profile-container">
        <div class="profile-sidebar">
            <div class="profile-photo">
                <?php if (!empty($user['profile_photo'])): ?>
                    <img src="../uploads/profiles/<?= htmlspecialchars($user['profile_photo']) ?>" id="profile-photo">
                <?php else: ?>
                    <div class="profile-photo-placeholder">
                        <?= strtoupper(substr($user['name'], 0, 1)) ?>
                    </div>
                <?php endif; ?>
                
                <div class="photo-upload">
                    <input type="file" id="photo-upload" accept="image/jpeg,image/png,image/gif" style="display:none">
                    <button type="button" id="upload-btn" class="btn-sm">Change Photo</button>
                    <?php if (!empty($user['profile_photo'])): ?>
                        <form method="POST" action="delete_photo.php" style="display:inline;">
                            <button type="submit" class="btn-sm btn-danger">Delete</button>
                        </form>
                    <?php endif; ?>
                </div>
                <div id="upload-progress" style="display:none; color:#0066cc; margin-top:10px;">Uploading...</div>
            </div>
            
            <div class="profile-menu">
                <ul>
                    <li><a href="profile.php" class="active">Profile Info</a></li>
                    <li><a href="profile_edit.php">Edit Profile</a></li>
                    <li><a href="change_password.php">Change Password</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
        
        <div class="profile-content">
            <?php if ($success): ?>
                <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <div class="profile-info">
                <h2>Profile Information</h2>
                <table class="info-table">
                    <tr>
                        <th>Name:</th>
                        <td><?= htmlspecialchars($user['name']) ?></td>
                    </tr>
                    <tr>
                        <th>Email:</th>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                    </tr>
                    <tr>
                        <th>Member Since:</th>
                        <td>
                            <?php 
                            if (isset($user['created_at']) && !empty($user['created_at'])) {
                                echo date('F j, Y', strtotime($user['created_at']));
                            } else {
                                echo 'Just joined';
                            }
                            ?>
                        </td>
                    </tr>
                </table>
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