<?php
session_start();
require_once '../config.php'; 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - Adidas Shop</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            padding: 20px 0;
        }

        .section-header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 10px;
        }
        .section-title {
            font-size: 24px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .no-results {
            text-align: center;
            padding: 50px;
            grid-column: 1/-1;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }
    </style>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="../index.php">ADIDAS</a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="product_list.php?type=Shoes">SHOES</a></li>
                    <li><a href="product_list.php?type=Clothing">CLOTHING</a></li>
                    <li><a href="product_list.php?category_id=1">MEN</a></li>
                    <li><a href="product_list.php?category_id=2">WOMEN</a></li>
                    <li><a href="product_list.php?show=all">ALL</a></li>
                </ul>
            </nav>
            <div class="header-actions">
                <form action="product_list.php" method="GET" style="display: flex; align-items: center;">
                    <input type="text" name="search" placeholder="Search products..." 
                           value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                           style="padding: 7px; border: 1px solid #ccc; border-radius: 4px;">
                    <button type="submit" class="search-btn" style="background: none; border: none; cursor: pointer; padding-left: 5px;">🔍</button>
                </form>
                
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <a href="../admin/index.php" style="color: #000; text-decoration: none; font-weight: bold;">ADMIN PANEL</a>
                    <?php else: ?>
                        <a href="history.php" style="color: #000; text-decoration: none; font-weight: bold;">My Orders</a>
                    <?php endif; ?>
                    <a href="profile.php" class="user-icon">👤 <?= htmlspecialchars($_SESSION['user_name'] ?? 'User') ?></a>
                    <a href="logout.php" class="logout-btn">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="login-btn">Login</a>
                <?php endif; ?>
                <a href="cart.php" class="cart-icon">🛒</a>
            </div>
        </div>
    </header>

    <main class="container" style="margin-top: 50px;">
        <?php
        $type = $_GET['type'] ?? null;
        $cat_id = $_GET['category_id'] ?? null;
        $search = $_GET['search'] ?? null;
        $show_all = isset($_GET['show']) && $_GET['show'] === 'all';

        $display_title = "ALL PRODUCTS";
        if ($type) $display_title = strtoupper($type);
        if ($cat_id == 1) $display_title = "MEN'S COLLECTION";
        if ($cat_id == 2) $display_title = "WOMEN'S COLLECTION";
        if ($search) $display_title = "SEARCH RESULTS";

        $sql = "SELECT * FROM product WHERE 1=1";
        $params = [];
        if ($type) { $sql .= " AND type = ?"; $params[] = $type; }
        if ($cat_id) { $sql .= " AND category_id = ?"; $params[] = $cat_id; }
        if ($search) { $sql .= " AND name LIKE ?"; $params[] = "%$search%"; }
        $sql .= " ORDER BY id DESC"; 

        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $products = $stmt->fetchAll();
        } catch (PDOException $e) { $products = []; }
        ?>

        <div class="section-header">
            <h2 class="section-title"><?= $display_title ?></h2>
        </div>

        <div class="product-grid">
            <?php if (count($products) > 0): ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="../uploads/products/<?= htmlspecialchars($product['photo']) ?>" 
                                 alt="<?= htmlspecialchars($product['name']) ?>" 
                                 style="width:100%; height:250px; object-fit:cover;">
                        </div>
                        <div class="product-info">
                            <h3 style="margin: 10px 0;"><?= htmlspecialchars($product['name']) ?></h3>
                            <div class="price" style="margin-bottom: 15px;">
                                <span class="current-price">RM <?= number_format($product['price'], 2) ?></span>
                            </div>
                            <a href="product_detail.php?id=<?= $product['id'] ?>" class="btn-view" 
                               style="display:block; text-align:center; padding:10px; background:#000; color:#fff; text-decoration:none; font-weight:bold;">
                               VIEW DETAILS
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="no-results">
                    <h3>No products found.</h3>
                    <p>Try a different filter or <a href="product_list.php?show=all">view all</a>.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include '../footer.php'; ?>
</body>
</html>