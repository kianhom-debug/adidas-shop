<?php
session_start();

require_once 'config.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: member/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $updateId = (int)$_POST['update_id'];
    $newStatus = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE `order` SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $updateId]);
    $_SESSION['success'] = "Order #$updateId status updated to $newStatus.";
    
    header('Location: admin_orders.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT o.*, u.name as user_name 
    FROM `order` o 
    JOIN users u ON o.user_id = u.id 
    ORDER BY o.id DESC
");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Orders</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo"><a href="index.php">ADIDAS ADMIN</a></div>
            <div class="header-actions">
                <a href="index.php">Home</a>
                <a href="member/logout.php">Logout</a>
            </div>
        </div>
    </header>

    <div class="container" style="padding: 40px 20px;">
        <h2>Manage Customer Orders</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <table class="info-table" style="width: 100%; border-collapse: collapse; margin-top:20px;">
            <tr style="border-bottom: 2px solid #eee; background:#000; color:#fff;">
                <th style="padding:10px;">Order ID</th>
                <th style="padding:10px;">Customer</th>
                <th style="padding:10px;">Date</th>
                <th style="text-align:right; padding:10px;">Total (RM)</th>
                <th style="text-align:center; padding:10px;">Action / Update Status</th>
            </tr>
            <?php foreach ($orders as $o): ?>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding:10px; font-weight:bold;">#<?= $o['id'] ?></td>
                <td style="padding:10px;"><?= htmlspecialchars($o['user_name']) ?></td>
                <td style="padding:10px;"><?= $o['datetime'] ?></td>
                <td style="text-align:right; padding:10px;"><?= number_format($o['total'], 2) ?></td>
                <td style="text-align:center; padding:10px;">
                    <form method="POST" style="display:flex; justify-content:center; gap:5px;">
                        <input type="hidden" name="update_id" value="<?= $o['id'] ?>">
                        <select name="status" style="padding:5px;">
                            <option value="Pending" <?= $o['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                            <option value="Completed" <?= $o['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                            <option value="Cancelled" <?= $o['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                        </select>
                        <button type="submit" class="btn-sm" style="background:#28a745; color:#fff; border:none; cursor:pointer;">Update</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>
