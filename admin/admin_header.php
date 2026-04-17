<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../config.php';
require_once 'auth_check.php';

$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title ?? 'ADIDAS ADMIN'; ?></title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="../index.php">ADIDAS ADMIN</a>
            </div>
            <div class="header-actions">
                <span>Welcome, <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?></span>
                <a href="../member/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

<div class="admin-content-wrapper">
    <div class="container">
        <div class="admin-layout"> 
            <aside class="admin-sidebar">
                <h3>Management</h3>
                <ul class="admin-nav-list">
                    <li><a href="index.php" class="<?php echo $current_page == 'index.php' ? 'active' : ''; ?>">🏠 Dashboard</a></li>
                    <li><a href="manage_product.php" class="<?php echo $current_page == 'manage_product.php' ? 'active' : ''; ?>">📦 Manage Products</a></li>
                    <li><a href="add_product.php" class="<?php echo $current_page == 'add_product.php' ? 'active' : ''; ?>">➕ Add Product</a></li>
                    <li><a href="category_maintenance.php" class="<?php echo $current_page == 'category_maintenance.php' ? 'active' : ''; ?>">📂 Categories</a></li>
                    <li><a href="manage_orders.php" class="<?php echo $current_page == 'manage_orders.php' ? 'active' : ''; ?>">🛒 Orders</a></li>
                </ul>
            </aside>
            <main class="admin-main">