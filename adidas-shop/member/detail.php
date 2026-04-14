<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: history.php');
    exit;
}

$orderId = (int)$_GET['id'];
$userId = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM `order` WHERE id = ? AND user_id = ?");
$stmt->execute([$orderId, $userId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    header('Location: history.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT i.*, p.name 
    FROM item AS i 
    JOIN product AS p ON i.product_id = p.id 
    WHERE i.order_id = ?
");
$stmt->execute([$orderId]);
$items = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order Detail - Adidas Shop</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo"><a href="../index.php">ADIDAS</a></div>
            <div class="header-actions">
                <a href="history.php">← Back to History</a>
            </div>
        </div>
    </header>

    <div class="container" style="padding: 40px 20px;">
        <h2>Order Detail #<?= $order['id'] ?></h2>
        
        <div style="background:#f9f9f9; padding:20px; border-radius:8px; margin-bottom:20px;">
            <p><strong>Date:</strong> <?= $order['datetime'] ?></p>
            <p><strong>Status:</strong> <?= $order['status'] ?></p>
            <p><strong>Total Items:</strong> <?= $order['count'] ?></p>
            <p><strong>Total Amount:</strong> RM <?= number_format($order['total'], 2) ?></p>
        </div>

        <table class="info-table" style="width: 100%; border-collapse: collapse;">
            <tr style="border-bottom: 2px solid #eee;">
                <th style="text-align:left; padding:10px;">Product Name</th>
                <th style="text-align:right; padding:10px;">Unit Price (RM)</th>
                <th style="text-align:center; padding:10px;">Quantity</th>
                <th style="text-align:right; padding:10px;">Subtotal (RM)</th>
            </tr>
            <?php foreach ($items as $i): ?>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding:10px;"><?= htmlspecialchars($i['name']) ?></td>
                <td style="text-align:right; padding:10px;"><?= number_format($i['price'], 2) ?></td>
                <td style="text-align:center; padding:10px;"><?= $i['unit'] ?></td>
                <td style="text-align:right; padding:10px;"><?= number_format($i['subtotal'], 2) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
