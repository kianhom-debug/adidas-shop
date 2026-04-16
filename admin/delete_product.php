<?php
session_start();
require_once '../config.php';
require_once 'auth_check.php';

$id = $_GET['id'] ?? '';

if (empty($id)) {
    header("Location: manage_product.php");
    exit();
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("SELECT photo FROM product WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if ($product) {
        $main_photo_path = "../uploads/products/" . $product['photo'];
        if (!empty($product['photo']) && file_exists($main_photo_path)) {
            unlink($main_photo_path); 
        }

        $stmt_extra = $pdo->prepare("SELECT filename FROM product_photo WHERE product_id = ?");
        $stmt_extra->execute([$id]);
        $extra_photos = $stmt_extra->fetchAll();

        foreach ($extra_photos as $extra) {
            $extra_path = "../uploads/products/" . $extra['filename'];
            if (file_exists($extra_path)) {
                unlink($extra_path); 
            }
        }

        $pdo->prepare("DELETE FROM product_photo WHERE product_id = ?")->execute([$id]);

        $pdo->prepare("DELETE FROM product WHERE id = ?")->execute([$id]);

        $pdo->commit();
        echo "<script>alert('Product and its images deleted successfully!'); window.location='manage_product.php';</script>";
    } else {
        header("Location: manage_product.php");
    }

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo "<script>alert('Error deleting product: " . addslashes($e->getMessage()) . "'); window.location='manage_product.php';</script>";
}
?>