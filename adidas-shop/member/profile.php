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

// Handle success/error messages
$success = $_SESSION['profile_success'] ?? '';
$error = $_SESSION['profile_error'] ?? '';
unset($_SESSION['profile_success'], $_SESSION['profile_error']);

include '../header.php';
?>

<div class="profile-container" style="display:flex;max-width:1200px;margin:0 auto;padding:40px 20px;">
    <div class="profile-sidebar" style="flex:0 0 280px;">
        <div class="profile-photo" style="text-align:center;">
            <?php if (!empty($user['profile_photo'])): ?>
                <img src="../uploads/profiles/<?= htmlspecialchars($user['profile_photo']) ?>" 
                     alt="Profile Photo" id="profile-photo"
                     style="width:150px;height:150px;border-radius:50%;object-fit:cover;">
            <?php else: ?>
                <div class="profile-photo-placeholder" 
                     style="width:150px;height:150px;border-radius:50%;background:#0066cc;color:white;display:flex;align-items:center;justify-content:center;font-size:48px;margin:0 auto;">
                    <?= strtoupper(substr($user['username'], 0, 1)) ?>
                </div>
            <?php endif; ?>
            
            <div class="photo-upload" style="margin-top:15px;">
                <input type="file" id="photo-upload" accept="image/jpeg,image/png,image/gif" style="display:none">
                <button type="button" id="upload-btn" style="padding:6px 12px;background:#f0f0f0;border:none;border-radius:4px;cursor:pointer;">Change Photo</button>
                <?php if (!empty($user['profile_photo'])): ?>
                    <form method="POST" action="delete_photo.php" style="display:inline;">
                        <button type="submit" style="padding:6px 12px;background:#dc3545;color:white;border:none;border-radius:4px;cursor:pointer;">Delete</button>
                    </form>
                <?php endif; ?>
            </div>
            <div id="upload-progress" style="display:none;color:blue;margin-top:10px;">Uploading...</div>
        </div>
        
        <div class="profile-menu" style="margin-top:20px;">
            <ul style="list-style:none;padding:0;">
                <li><a href="profile.php" style="display:block;padding:10px;background:#0066cc;color:white;text-decoration:none;border-radius:4px;">Profile Info</a></li>
                <li><a href="profile_edit.php" style="display:block;padding:10px;color:#333;text-decoration:none;">Edit Profile</a></li>
                <li><a href="change_password.php" style="display:block;padding:10px;color:#333;text-decoration:none;">Change Password</a></li>
                <li><a href="logout.php" style="display:block;padding:10px;color:#333;text-decoration:none;">Logout</a></li>
            </ul>
        </div>
    </div>
    
    <div class="profile-content" style="flex:1;background:#fff;border-radius:8px;padding:30px;margin-left:30px;box-shadow:0 2px 10px rgba(0,0,0,0.1);">
        <?php if ($success): ?>
            <div class="alert alert-success" style="background:#d4edda;color:#155724;padding:12px;border-radius:4px;margin-bottom:20px;"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-error" style="background:#f8d7da;color:#721c24;padding:12px;border-radius:4px;margin-bottom:20px;"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <div class="profile-info">
            <h2 style="margin-top:0;">Profile Information</h2>
            
            <table style="width:100%;">
                <tr>
                    <th style="width:150px;text-align:left;padding:12px 0;">Username:</th>
                    <td style="padding:12px 0;"><?= htmlspecialchars($user['username']) ?></td>
                </tr>
                <tr>
                    <th style="text-align:left;padding:12px 0;">Full Name:</th>
                    <td style="padding:12px 0;"><?= htmlspecialchars($user['full_name']) ?></td>
                </tr>
                <tr>
                    <th style="text-align:left;padding:12px 0;">Email:</th>
                    <td style="padding:12px 0;"><?= htmlspecialchars($user['email']) ?></td>
                </tr>
                <tr>
                    <th style="text-align:left;padding:12px 0;">Member Since:</th>
                    <td style="padding:12px 0;"><?= date('F j, Y', strtotime($user['created_at'])) ?></td>
                </tr>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="../js/profile.js"></script>

<?php include '../footer.php'; ?>