<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$userId = $_SESSION['user_id'];

if (isset($_POST['cancel_id'])) {
    $cancelId = (int)$_POST['cancel_id'];
    
    try {
        $pdo->beginTransaction();
        
        $stmt = $pdo->prepare("UPDATE `order` SET status = 'Cancelled' WHERE id = ? AND user_id = ? AND status = 'Pending'");
        $stmt->execute([$cancelId, $userId]);
        
        if ($stmt->rowCount() > 0) {

            $stmtStockRestore = $pdo->prepare("
                UPDATE product p
                JOIN item i ON p.id = i.product_id
                SET p.stock = p.stock + i.unit
                WHERE i.order_id = ?
            ");
            $stmtStockRestore->execute([$cancelId]);
            
            $_SESSION['success'] = "Order #$cancelId cancelled. Stock has been restored.";
        }
        
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Failed to cancel order.";
    }
    
    header('Location: history.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM `order` WHERE user_id = ? ORDER BY id DESC");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order History - Adidas Shop</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo"><a href="../index.php">ADIDAS</a></div>
            <div class="header-actions">
                <a href="cart.php">🛒 Cart</a>
                <a href="profile.php">👤 <?= htmlspecialchars($_SESSION['user_name']) ?></a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </header>

    <div class="container" style="padding: 40px 20px;">
        <h2>My Order History</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <table class="info-table" style="width: 100%; border-collapse: collapse; margin-top:20px;">
            <tr style="border-bottom: 2px solid #eee; background:#f9f9f9;">
                <th style="padding:10px;">Order ID</th>
                <th style="padding:10px;">Date</th>
                <th style="text-align:center; padding:10px;">Items</th>
                <th style="text-align:right; padding:10px;">Total (RM)</th>
                <th style="text-align:center; padding:10px;">Status</th>
                <th style="text-align:center; padding:10px;">Actions</th>
            </tr>
            <?php foreach ($orders as $o): ?>
            <tr style="border-bottom: 1px solid #eee;">
                <td style="padding:10px; font-weight:bold;">#<?= $o['id'] ?></td>
                <td style="padding:10px;"><?= $o['datetime'] ?></td>
                <td style="text-align:center; padding:10px;"><?= $o['count'] ?></td>
                <td style="text-align:right; padding:10px;"><?= number_format($o['total'], 2) ?></td>
                <td style="text-align:center; padding:10px;">
                    <span style="padding:4px 8px; border-radius:4px; font-size:12px;
                        <?= $o['status'] == 'Pending' ? 'background:#fff3cd; color:#856404;' : 
                          ($o['status'] == 'Completed' ? 'background:#d4edda; color:#155724;' : 'background:#f8d7da; color:#721c24;') ?>">
                        <?= $o['status'] ?>
                    </span>
                </td>
                <td style="text-align:center; padding:10px;">
                    <a href="detail.php?id=<?= $o['id'] ?>" class="btn-sm" style="background:#0066cc; color:#fff; text-decoration:none;">Detail</a>
                    
                    <?php if ($o['status'] === 'Pending'): ?>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Are you sure you want to cancel this order?');">
                        <input type="hidden" name="cancel_id" value="<?= $o['id'] ?>">
                        <button type="submit" class="btn-sm btn-danger" style="background:#dc3545; color:white; border:none; cursor:pointer;">Cancel</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</body>
</html>