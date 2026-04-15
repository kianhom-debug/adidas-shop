<?php
session_start();
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adidas Shop - Official Website</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="index.php">ADIDAS</a>
            </div>
            <nav class="main-nav">
                <ul>
                    <li><a href="products.php?type=Shoes">SHOES</a></li>
                    <li><a href="products.php?category_id=3">CLOTHING</a></li>
                    <li><a href="products.php?category_id=1">MEN</a></li>
                    <li><a href="products.php?category_id=2">WOMEN</a></li>
                    
                    <li><a href="products.php?show=all">ALL</a></li>

                    <li><a href="admin/index.php">ADMIN</a></li>
                </ul>
            </nav>
                <div class="header-actions" style="display: flex; align-items: center; gap: 15px;">
                    <form action="products.php" method="GET" style="display: flex; align-items: center; margin-top: 8px;">
                <input type="text" name="search" placeholder="Search products..." 
                style="padding: 7px; border: 1px solid #ccc; border-radius: 4px;">
                <button type="submit" class="search-btn" style="background: none; border: none; cursor: pointer; padding-left: 5px;">🔍</button>
                    </form>
                    
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="member/profile.php" class="user-icon">👤 <?= htmlspecialchars($_SESSION['user_name']) ?></a>
                    <a href="member/logout.php" class="logout-btn">Logout</a>
                <?php else: ?>
                    <a href="member/login.php" class="login-btn">Login</a>
                <?php endif; ?>
                <a href="member/cart.php" class="cart-icon">🛒</a>
            </div>
        </div>
    </header>

    <!-- Hero Banner -->
    <section class="hero">
        <div class="hero-content">
            <h1>ORIGINALS</h1>
            <h2>NEW SEASON</h2>
            <p>Discover the latest collection</p>
            <a href="#" class="btn-shop">SHOP NOW →</a>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="categories">
        <div class="container">
            <h2 class="section-title">SHOP BY CATEGORY</h2>
            <div class="category-grid">
                <div class="category-card">
                    <div class="category-image">👟</div>
                    <h3>Shoes</h3>
                    <p>Explore latest footwear</p>
                </div>
                <div class="category-card">
                    <div class="category-image">👕</div>
                    <h3>Clothing</h3>
                    <p>T-shirts, hoodies & more</p>
                </div>
                <div class="category-card">
                    <div class="category-image">🎒</div>
                    <h3>Accessories</h3>
                    <p>Bags, hats & gear</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Products -->
<section class="featured-products">
    <div class="container">
        <h2 class="section-title">FEATURED PRODUCTS</h2>
        <div class="product-grid">
            <?php
            try {
                $sql = "SELECT * FROM product LIMIT 8";
                $stmt = $pdo->query($sql);
                $products = $stmt->fetchAll();
            } catch (PDOException $e) {
                die("Error: " . $e->getMessage());
            }
            ?>

            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <div class="product-image">
                        <img src="uploads/products/<?= htmlspecialchars($product['photo']) ?>.jpeg" 
                             alt="<?= htmlspecialchars($product['name']) ?>" 
                             style="width:100%; height:200px; object-fit:cover;">
                    </div>

                    <div class="product-info">
                        <h3><?= htmlspecialchars($product['name']) ?></h3>
                        <div class="price">
                            <span class="current-price">RM <?= number_format($product['price'], 2) ?></span>
                        </div>
                        <a href="member/product_detail.php?id=<?= $product['id'] ?>" class="btn-view">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

    <!-- Footer -->
    <footer class="main-footer">
        <div class="container">
            <div class="footer-links">
                <div class="footer-column">
                    <h4>PRODUCTS</h4>
                    <ul>
                        <li><a href="#">Shoes</a></li>
                        <li><a href="#">Clothing</a></li>
                        <li><a href="#">Accessories</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>SUPPORT</h4>
                    <ul>
                        <li><a href="#">Help</a></li>
                        <li><a href="#">Returns</a></li>
                        <li><a href="#">Shipping</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>COMPANY</h4>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Careers</a></li>
                        <li><a href="#">Sustainability</a></li>
                    </ul>
                </div>
                <div class="footer-column">
                    <h4>FOLLOW US</h4>
                    <div class="social-links">
                        <a href="#">Instagram</a>
                        <a href="#">Facebook</a>
                        <a href="#">Twitter</a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2024 Adidas Shop. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>