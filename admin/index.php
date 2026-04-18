<?php
session_start();
require_once '../config.php';
require_once 'auth_check.php';


try {
    $total_products = $pdo->query("SELECT COUNT(*) FROM product")->fetchColumn();
    $total_cats = $pdo->query("SELECT COUNT(*) FROM category")->fetchColumn();
    $low_stock = $pdo->query("SELECT COUNT(*) FROM product WHERE stock < 10")->fetchColumn();
} catch (PDOException $e) {
    $error = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ADIDAS - Admin Dashboard</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="../index.php">ADIDAS ADMIN</a>
            </div>
            <div class="header-actions">
                <span>Welcome, <?= ($_SESSION['user_name'] ?? 'Admin') ?></span>
                <a href="../member/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="admin-layout"> 
            <aside class="admin-sidebar">
                <h3>Management</h3>
                <ul class="admin-nav-list">
                    <li><a href="index.php" class="active">🏠 Dashboard</a></li>
                    <li><a href="manage_product.php">📦 Manage Products</a></li>
                    <li><a href="add_product.php">➕ Add New Product</a></li>
                    <li><a href="category_maintenance.php">📂 Category Maintenance</a></li>
                    <li><a href="manage_orders.php">🛒 Manage Orders</a></li>
                    <li><a href="manage_member.php">👤 Manage Members</a></li>
                </ul>
            </aside>

            <main class="admin-main">
                <h2 class="section-title" style="text-align:left;">SYSTEM OVERVIEW</h2>
                
                <div class="category-grid"> 
                    <div class="stat-card">
                        <h3>Total Products</h3>
                        <span class="stat-num"><?= $total_products ?></span>
                        <p>Active items in store</p>
                    </div>
                    
                    <div class="stat-card warning-card">
                        <h3>Low Stock Alert</h3>
                        <span class="stat-num"><?= $low_stock ?></span>
                        <p>Items needing restock</p>
                    </div>

                    <div class="stat-card">
                        <h3>Categories</h3>
                        <span class="stat-num"><?= $total_cats ?></span>
                        <p>Product groupings</p>
                    </div>
                </div>

                <div class="quick-actions" style="margin-top: 40px;">
                    <h3>Quick Actions</h3>
                    <hr>
                    <div style="margin-top:20px;">
                        <a href="add_product.php" class="btn-shop">ADD PRODUCT</a>
                        <a href="manage_product.php" class="btn-shop" style="background:#fff; color:#000; border:1px solid #000; margin-left:10px;">MANAGE INVENTORY</a>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
<?php 
    include '../footer.php'; 
?>