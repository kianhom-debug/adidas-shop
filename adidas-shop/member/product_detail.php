<?php
session_start();
require_once '../config.php';

$productId = isset($_GET['id']) ? trim($_GET['id']) : '';

if (empty($productId)) {

    header('Location: ../index.php');
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM product WHERE id = ?");
$stmt->execute([$productId]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {

    header('Location: ../index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $cart_id = $_POST['product_id']; // 保持为字符串
    $unit = (int)$_POST['unit'];
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$cart_id])) {
        $_SESSION['cart'][$cart_id] += $unit;
    } else {
        $_SESSION['cart'][$cart_id] = $unit;
    }
    
    $_SESSION['success'] = "Successfully added to your cart!";
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
            <div class="product-visual" style="flex: 1; background: #f5f5f5; display: flex; align-items: center; justify-content: center; border-radius: 10px; min-height: 400px; overflow: hidden;">
                <img src="../uploads/products/<?= htmlspecialchars($product['photo']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" style="max-width: 100%; max-height: 400px; object-fit: contain;">
            </div>
            
            <div class="product-details" style="flex: 1;">
                <h1 style="font-size: 32px; margin-bottom: 10px;"><?= htmlspecialchars($product['name']) ?></h1>
                <span style="display: inline-block; background: #eee; padding: 5px 10px; border-radius: 5px; font-size: 14px; margin-bottom: 15px;"><?= htmlspecialchars($product['type']) ?></span>
                
                <p style="font-size: 28px; color: #0066cc; font-weight: bold; margin: 10px 0 20px 0;">RM <?= number_format($product['price'], 2) ?></p>
                <p style="color: #666; line-height: 1.6; margin-bottom: 10px; font-size: 16px;"><?= nl2br(htmlspecialchars($product['description'] ?? 'No description available.')) ?></p>
                <p style="color: #888; font-size: 14px; margin-bottom: 30px;">In Stock: <?= $product['stock'] ?></p>
                
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
            </div>
        </div>
    </div>
</body>
</html>