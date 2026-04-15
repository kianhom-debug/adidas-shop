<?php
session_start();

require_once '../config.php'; 

if (!isset($_SESSION['user_id'])) {
    header('Location: ../member/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $updateId = (int)$_POST['update_id'];
    $newStatus = $_POST['status'];
    
    $stmt = $pdo->prepare("UPDATE `order` SET status = ? WHERE id = ?");
    $stmt->execute([$newStatus, $updateId]);
    $_SESSION['success'] = "Order #$updateId status updated to $newStatus.";
    
    header('Location: manage_orders.php');
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
    <title>ADIDAS ADMIN - Manage Orders</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="../index.php">ADIDAS ADMIN</a>
            </div>
            <div class="header-actions">
                <span>Welcome, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></span>
                <a href="../member/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="admin-layout"> 
            <aside class="admin-sidebar">
                <h3>Management</h3>
                <ul class="admin-nav-list">
                    <li><a href="index.php">🏠 Dashboard</a></li>
                    <li><a href="manage_product.php">📦 Manage Products</a></li>
                    <li><a href="add_product.php">➕ Add New Product</a></li>
                    <li><a href="category_maintenance.php">📂 Category Maintenance</a></li>
                    <li><a href="manage_orders.php" class="active">🛒 Manage Orders</a></li>
                </ul>
            </aside>

            <main class="admin-main">
                <h2 class="section-title" style="text-align:left;">CUSTOMER ORDERS</h2>
                
                <?php if (isset($_SESSION['success'])): ?>
                    <div style="background-color: #d4edda; color: #155724; padding: 15px; border-radius: 4px; margin-bottom: 20px; font-weight: bold;">
                        ✅ <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer Name</th>
                            <th>Order Date</th>
                            <th>Total (RM)</th>
                            <th>Status & Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $o): ?>
                        <tr>
                            <td><strong>#<?= $o['id'] ?></strong></td>
                            <td><?= htmlspecialchars($o['user_name']) ?></td>
                            <td><?= $o['datetime'] ?></td>
                            <td><?= number_format($o['total'], 2) ?></td>
                            <td>
                                <form method="POST" style="display:flex; gap:10px; align-items: center; margin:0;">
                                    <input type="hidden" name="update_id" value="<?= $o['id'] ?>">
                                    <select name="status" class="form-control" style="width: auto; padding: 5px;">
                                        <option value="Pending" <?= $o['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="Completed" <?= $o['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                        <option value="Cancelled" <?= $o['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                    <button type="submit" class="btn-shop" style="padding: 5px 15px; font-size: 12px; border: none; cursor: pointer;">UPDATE</button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="5" style="text-align:center; padding: 20px; color: #666;">No orders found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </main>
        </div>
    </div>
</body>
</html>