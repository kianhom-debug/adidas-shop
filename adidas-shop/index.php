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
                    <li><a href="#">SHOES</a></li>
                    <li><a href="#">MEN</a></li>
                    <li><a href="#">WOMEN</a></li>
                    <li><a href="#">KIDS</a></li>
                    <li><a href="#">SPORTS</a></li>
                    <li><a href="#">BRANDS</a></li>
                    <li><a href="#">OUTLET</a></li>
                </ul>
            </nav>
            <div class="header-actions">
                <a href="#" class="search-icon">🔍</a>
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
                // Sample products (replace with database query)
                $sample_products = [
                    ['name' => 'Ultraboost 22', 'price' => 699.00, 'image' => '👟'],
                    ['name' => 'Superstar', 'price' => 399.00, 'image' => '👟'],
                    ['name' => 'Stan Smith', 'price' => 459.00, 'image' => '👟'],
                    ['name' => 'NMD R1', 'price' => 599.00, 'image' => '👟'],
                    ['name' => 'Originals Hoodie', 'price' => 299.00, 'image' => '👕'],
                    ['name' => 'Tiro Track Pants', 'price' => 199.00, 'image' => '👖'],
                    ['name' => 'Backpack', 'price' => 159.00, 'image' => '🎒'],
                    ['name' => 'Cap', 'price' => 89.00, 'image' => '🧢'],
                ];
                
                foreach ($sample_products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <div class="product-icon"><?= $product['image'] ?></div>
                        </div>
                        <div class="product-info">
                            <h3><?= htmlspecialchars($product['name']) ?></h3>
                            <div class="price">
                                <span class="current-price">RM <?= number_format($product['price'], 2) ?></span>
                            </div>
                            <a href="member/product_detail.php" class="btn-view">View Details</a>
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