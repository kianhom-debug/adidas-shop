<?php
$page_title = "ADIDAS - Manage Orders";
include 'admin_header.php';


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_id'])) {
    $updateId = (int)$_POST['update_id'];
    $newStatus = $_POST['status'];
    
    try {
        $pdo->beginTransaction();
        
        $stmtCheck = $pdo->prepare("SELECT status FROM `order` WHERE id = ?");
        $stmtCheck->execute([$updateId]);
        $oldStatus = $stmtCheck->fetchColumn();
        
        $stmt = $pdo->prepare("UPDATE `order` SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $updateId]);
        
        if ($oldStatus !== 'Cancelled' && $newStatus === 'Cancelled') {
            $stmtStock = $pdo->prepare("UPDATE product p JOIN item i ON p.id = i.product_id SET p.stock = p.stock + i.unit WHERE i.order_id = ?");
            $stmtStock->execute([$updateId]);
        }
    
        elseif ($oldStatus === 'Cancelled' && $newStatus !== 'Cancelled') {
             $stmtStock = $pdo->prepare("UPDATE product p JOIN item i ON p.id = i.product_id SET p.stock = p.stock - i.unit WHERE i.order_id = ?");
             $stmtStock->execute([$updateId]);
        }
        
        $pdo->commit();
        $_SESSION['success'] = "Order #$updateId status updated to $newStatus.";
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['success'] = "Error updating order.";
    }
    
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
                                <div style="display: flex; gap: 10px; align-items: center;">
                                    <a href="../member/detail.php?id=<?= $o['id'] ?>" style="background: #0066cc; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px; font-size: 12px; font-weight: bold;">VIEW DETAIL</a>
                                    
                                    <?php if ($o['status'] === 'Cancelled'): ?>
                                        <span style="background: #f8d7da; color: #721c24; padding: 5px 10px; border-radius: 3px; font-size: 12px; font-weight: bold; border: 1px solid #f5c6cb;">
                                            CANCELLED (LOCKED)
                                        </span>
                                    <?php else: ?>
                                        <form method="POST" style="display:flex; gap:5px; align-items: center; margin:0;">
                                            <input type="hidden" name="update_id" value="<?= $o['id'] ?>">
                                            <select name="status" class="form-control" style="width: auto; padding: 5px;">
                                                <option value="Pending" <?= $o['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="Completed" <?= $o['status'] == 'Completed' ? 'selected' : '' ?>>Completed</option>
                                                <option value="Cancelled" <?= $o['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                            </select>
                                            <button type="submit" class="btn-shop" style="padding: 5px 10px; font-size: 12px; border: none; cursor: pointer;">UPDATE</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
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
<?php 
    include '../footer.php'; 
?>