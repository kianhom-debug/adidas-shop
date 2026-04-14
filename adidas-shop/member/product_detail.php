<?php
session_start();
require_once '../config.php';

// for testing \\\待删
$product = [
    'id' => 1,
    'name' => 'Ultraboost 22',
    'price' => 699.00,
    'image' => '👟',
    'description' => 'Experience ultimate comfort and energy return with these running shoes.'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $id = (int)$_POST['product_id'];
    $unit = (int)$_POST['unit'];
    
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] += $unit;
    } else {
        $_SESSION['cart'][$id] = $unit;
    }
    
    $_SESSION['success'] = "Successfully added to your cart!";
    header('Location: product_detail.php?id=' . $productId);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= $product['name'] ?> - Adidas Shop</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header class="main-header">
        <?php if (isset($_SESSION['success'])): ?>
    <div style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 15px; border-radius: 5px; margin-bottom: 20px; text-align: center; font-weight: bold;">
        ✅ <?= $_SESSION['success'] ?> 
        <a href="cart.php" style="color: #0066cc; text-decoration: underline; margin-left: 10px;">View Cart</a>
    </div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>
        <div class="container">
            <div class="logo"><a href="../index.php">ADIDAS</a></div>
            <div class="header-actions">
                <a href="cart.php">🛒 Cart (<?= isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0 ?>)</a>
                <a href="../index.php">Back to Shop</a>
            </div>
        </div>
    </header>

    <div class="container" style="padding: 60px 20px; display: flex; gap: 50px;">
        <div class="product-visual" style="flex: 1; background: #f5f5f5; display: flex; align-items: center; justify-content: center; font-size: 150px; border-radius: 10px;">
            <?= $product['image'] ?>
        </div>
        
        <div class="product-details" style="flex: 1;">
            <h1><?= htmlspecialchars($product['name']) ?></h1>
            <p style="font-size: 24px; color: #0066cc; font-weight: bold; margin: 20px 0;">RM <?= number_format($product['price'], 2) ?></p>
            <p style="color: #666; line-height: 1.6; margin-bottom: 30px;"><?= htmlspecialchars($product['description']) ?></p>
            
            <form method="POST">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <div style="margin-bottom: 20px;">
                    <label>Quantity:</label>
                    <select name="unit" style="padding: 8px; margin-left: 10px;">
                        <?php for($i=1; $i<=10; $i++) echo "<option value='$i'>$i</option>"; ?>
                    </select>
                </div>
                <button type="submit" name="add_to_cart" class="btn-primary" style="padding: 15px 40px; background: #000; color: #fff; border: none; cursor: pointer; font-size: 16px; font-weight: bold;">ADD TO CART</button>
            </form>
        </div>
    </div>
</body>
</html>

