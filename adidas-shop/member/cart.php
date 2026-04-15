<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'clear') {
            $_SESSION['cart'] = [];
        } elseif ($_POST['action'] === 'update') {
            $id = $_POST['id'];
            $unit = (int)$_POST['unit'];
            if ($unit > 0) {
                $_SESSION['cart'][$id] = $unit;
            } else {
                unset($_SESSION['cart'][$id]);
            }
        }
    }
    header('Location: cart.php');
    exit;
}

$cart_items = [];
$total_count = 0;
$total_price = 0;

if (!empty($_SESSION['cart'])) {
    $placeholders = str_repeat('?,', count($_SESSION['cart']) - 1) . '?';
    $stmt = $pdo->prepare("SELECT * FROM product WHERE id IN ($placeholders)");
    $stmt->execute(array_keys($_SESSION['cart']));
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($products as $p) {
        $unit = $_SESSION['cart'][$p['id']];
        $subtotal = $p['price'] * $unit;
        $cart_items[] = [
            'id' => $p['id'],
            'name' => $p['name'],
            'price' => $p['price'],
            'unit' => $unit,
            'subtotal' => $subtotal
        ];
        $total_count += $unit;
        $total_price += $subtotal;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Shopping Cart - Adidas Shop</title>
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo"><a href="../index.php">ADIDAS</a></div>
            <div class="header-actions">
                <a href="history.php">My Orders</a>
                <a href="profile.php">👤 <?= htmlspecialchars($_SESSION['user_name']) ?></a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </header>

    <div class="container" style="padding: 40px 20px;">
        <h2>Shopping Cart</h2>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if (empty($cart_items)): ?>
            <p>Your cart is empty.</p>
            <a href="../index.php" class="btn-primary" style="display:inline-block; margin-top:10px; padding:10px 20px; background:#000; color:#fff; text-decoration:none;">Shop Now</a>
        <?php else: ?>
            <table class="info-table" style="width: 100%; border-collapse: collapse;">
                <tr style="border-bottom: 2px solid #eee;">
                    <th style="text-align:left; padding:10px;">Product</th>
                    <th style="text-align:right; padding:10px;">Price (RM)</th>
                    <th style="text-align:center; padding:10px;">Quantity</th>
                    <th style="text-align:right; padding:10px;">Subtotal (RM)</th>
                </tr>
                <?php foreach ($cart_items as $item): ?>
                <tr style="border-bottom: 1px solid #eee;">
                    <td style="padding:10px;"><?= htmlspecialchars($item['name']) ?></td>
                    <td style="text-align:right; padding:10px;"><?= number_format($item['price'], 2) ?></td>
                    <td style="text-align:center; padding:10px;">
                        <form method="POST" style="display:inline;" class="update-cart-form">
                            <input type="hidden" name="action" value="update">
                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                            <input type="number" name="unit" value="<?= $item['unit'] ?>" min="0" style="width: 60px; padding: 5px; border:1px solid #ccc;" class="qty-input">
                            </form>
                    </td>
                    <td style="text-align:right; padding:10px;"><?= number_format($item['subtotal'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
                <tr>
                    <th colspan="2" style="padding:10px;"></th>
                    <th style="text-align:center; padding:10px;">Total Items: <?= $total_count ?></th>
                    <th style="text-align:right; padding:10px; font-size:18px; color:#0066cc;">RM <?= number_format($total_price, 2) ?></th>
                </tr>
            </table>

            <div style="margin-top: 20px; display: flex; justify-content: flex-end; gap: 10px;">
                <form method="POST">
                    <input type="hidden" name="action" value="clear">
                    <button type="submit" class="btn-secondary" style="border:none; cursor:pointer;">Clear Cart</button>
                </form>
                <form method="POST" action="checkout.php">
                    <button type="submit" class="btn-primary" style="padding:10px 20px; background:#000; color:#fff; border:none; cursor:pointer;">Proceed to Checkout</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
        $('.qty-input').on('change', function() {
        $(this).closest('form').submit();
            });
        });
    </script>
</body>
</html>
