<?php
session_start();
require_once '../config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_SESSION['cart'])) {
        $_SESSION['error'] = "Your cart is empty.";
        header('Location: cart.php');
        exit;
    }

    try {
        $pdo->beginTransaction();
        $userId = $_SESSION['user_id'];

        $stmt = $pdo->prepare("INSERT INTO `order` (datetime, user_id, status) VALUES (NOW(), ?, 'Pending')");
        $stmt->execute([$userId]);
        $orderId = $pdo->lastInsertId();

        $stmtItem = $pdo->prepare("
            INSERT INTO item (order_id, product_id, price, unit, subtotal) 
            VALUES (?, ?, (SELECT price FROM product WHERE id = ?), ?, (SELECT price FROM product WHERE id = ?) * ?)
        ");
        foreach ($_SESSION['cart'] as $productId => $unit) {
            $stmtItem->execute([$orderId, $productId, $productId, $unit, $productId, $unit]);
        }

        $stmtUpdate = $pdo->prepare("
            UPDATE `order` 
            SET count = (SELECT SUM(unit) FROM item WHERE order_id = ?),
                total = (SELECT SUM(subtotal) FROM item WHERE order_id = ?)
            WHERE id = ?
        ");
        $stmtUpdate->execute([$orderId, $orderId, $orderId]);

        $pdo->commit();

        $_SESSION['cart'] = [];
        $_SESSION['success'] = "Payment successful! Order #$orderId has been placed.";
        header("Location: history.php");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Checkout failed: " . $e->getMessage();
        header('Location: cart.php');
        exit;
    }
} else {
    header('Location: cart.php');
}