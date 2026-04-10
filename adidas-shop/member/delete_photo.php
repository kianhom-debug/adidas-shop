<?php
session_start();
require_once '../config.php';
require_once '../UploadHelper.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

// Get current photo
$stmt = $pdo->prepare("SELECT profile_photo FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

if ($user && !empty($user['profile_photo'])) {
    UploadHelper::deleteProfilePhoto($user['profile_photo']);
    $stmt = $pdo->prepare("UPDATE users SET profile_photo = NULL WHERE id = ?");
    $stmt->execute([$userId]);
    $_SESSION['profile_success'] = 'Profile photo deleted!';
}

header('Location: profile.php');
exit;