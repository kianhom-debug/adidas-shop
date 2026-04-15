<?php
session_start();
require_once '../config.php';
require_once '../UploadHelper.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'Not logged in']);
    exit;
}

if (!isset($_FILES['photo']) || $_FILES['photo']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'error' => 'No file uploaded']);
    exit;
}

$userId = $_SESSION['user_id'];
$result = UploadHelper::uploadProfilePhoto($_FILES['photo'], $userId);

if ($result['success']) {
    $stmt = $pdo->prepare("UPDATE users SET profile_photo = ? WHERE id = ?");
    $stmt->execute([$result['filename'], $userId]);
    echo json_encode(['success' => true, 'photo_url' => '../uploads/profiles/' . $result['filename']]);
} else {
    echo json_encode(['success' => false, 'error' => $result['error']]);
}