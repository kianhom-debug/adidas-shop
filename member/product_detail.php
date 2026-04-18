<?php
session_start();
require_once '../config.php';

$productId = isset($_GET['id']) ? trim($_GET['id']) : '';

if (empty($productId)) {

    header('Location: ../index.php');
    exit;
}
$stmt_photos = $pdo->prepare("SELECT * FROM product_photo WHERE product_id = ?");
$stmt_photos->execute([$productId]);
$extra_photos = $stmt_photos->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM product WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {

    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $cart_id = $_POST['product_id']; 
    $unit = (int)$_POST['unit'];
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    $current_qty = isset($_SESSION['cart'][$cart_id]) ? $_SESSION['cart'][$cart_id] : 0;
    $new_total_qty = $current_qty + $unit;

    if ($new_total_qty > $product['stock']) {
        $_SESSION['error'] = "Cannot add to cart. You already have $current_qty in cart, but only {$product['stock']} available in stock!";
    } else {
        $_SESSION['cart'][$cart_id] = $new_total_qty;
        $_SESSION['success'] = "Successfully added to your cart!";
    }
    
    header('Location: product_detail.php?id=' . $cart_id);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['name']) ?> - Adidas Shop</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <?php if (isset($_SESSION['error'])): ?>
            <div style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: bold;">
                ❌ <?= $_SESSION['error'] ?> 
            </div>
                <?php unset($_SESSION['error']); ?>
                <?php endif; ?>
            <div class="logo"><a href="../index.php">ADIDAS</a></div>
            <div class="header-actions">
                <a href="cart.php">🛒 Cart (<?= isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0 ?>)</a>
                <a href="../index.php">Back to Shop</a>
            </div>
        </div>
    </header>

    <div class="container" style="padding: 40px 20px;">
        
        <?php if (isset($_SESSION['success'])): ?>
            <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: bold;">
                ✅ <?= $_SESSION['success'] ?> 
                <a href="cart.php" style="color: #0066cc; text-decoration: underline; margin-left: 10px;">View Cart</a>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <div style="display: flex; gap: 50px;">
            <div class="product-visual" style="flex: 1;">
                <div style="background: #f5f5f5; display: flex; align-items: center; justify-content: center; border-radius: 10px; height: 400px; overflow: hidden; margin-bottom: 15px;">
                    <img id="mainImage" src="../uploads/products/<?= htmlspecialchars($product['photo']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="max-width: 100%; max-height: 400px; object-fit: contain;">
                </div>
                
                <div style="display: flex; gap: 10px; overflow-x: auto; padding-bottom: 5px;">
                    <img class="photo-thumb" src="../uploads/products/<?= htmlspecialchars($product['photo']) ?>" style="width: 80px; height: 80px; object-fit: cover; border: 2px solid #000; cursor: pointer; border-radius: 5px;" onclick="changeImage(this)">
                    
                    <?php foreach($extra_photos as $photo): ?>
                        <img class="photo-thumb" src="../uploads/products/<?= htmlspecialchars($photo['filename']) ?>" style="width: 80px; height: 80px; object-fit: cover; border: 2px solid transparent; cursor: pointer; border-radius: 5px;" onclick="changeImage(this)">
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="product-details" style="flex: 1;">
                <h1 style="font-size: 32px; margin-bottom: 10px;"><?= htmlspecialchars($product['name']) ?></h1>
                <span style="display: inline-block; background: #eee; padding: 5px 10px; border-radius: 5px; font-size: 14px; margin-bottom: 15px;"><?= htmlspecialchars($product['type']) ?></span>
                
                <p style="font-size: 28px; color: #0066cc; font-weight: bold; margin: 10px 0 20px 0;">RM <?= number_format($product['price'], 2) ?></p>
                <p style="color: #666; line-height: 1.6; margin-bottom: 10px; font-size: 16px;"><?= nl2br(htmlspecialchars($product['description'] ?? 'No description available.')) ?></p>
                <p style="color: #888; font-size: 14px; margin-bottom: 30px;">In Stock: <?= $product['stock'] ?></p>
                
                <?php if ($product['stock'] > 0): ?>
                    <form method="POST">
                        <input type="hidden" name="product_id" value="<?= htmlspecialchars($product['id']) ?>">
                        <div style="margin-bottom: 30px;">
                            <label style="font-weight: bold; font-size: 16px;">Quantity:</label>
                            <select name="unit" style="padding: 10px; margin-left: 10px; font-size: 16px; border-radius: 5px;">
                                <?php 
                                $max = $product['stock'] > 10 ? 10 : $product['stock'];
                                for($i=1; $i<=$max; $i++) echo "<option value='$i'>$i</option>"; 
                                ?>
                            </select>
                        </div>
                        <button type="submit" name="add_to_cart" class="btn-primary" style="padding: 15px 40px; background: #000; color: #fff; border: none; cursor: pointer; font-size: 16px; font-weight: bold; border-radius: 5px; width: 100%; transition: background 0.3s;">
                            ADD TO CART
                        </button>
                    </form>
                <?php else: ?>
                    <div style="margin-bottom: 30px;">
                        <p style="font-size: 16px; color: #dc3545; font-weight: bold;">⚠️ This item is currently OUT OF STOCK.</p>
                    </div>
                    <button disabled style="padding: 15px 40px; background: #cccccc; color: #666666; border: none; cursor: not-allowed; font-size: 16px; font-weight: bold; border-radius: 5px; width: 100%;">
                        OUT OF STOCK
                    </button>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
<script>
        function changeImage(element) {
            document.getElementById('mainImage').src = element.src;

            let thumbs = document.querySelectorAll('.photo-thumb');
            thumbs.forEach(thumb => {
                thumb.style.borderColor = 'transparent';
            });

            element.style.borderColor = '#000';
        }
    </script>
</body>
</html>