<?php
$page_title = "ADIDAS - Edit Product";
include 'admin_header.php';
require_once 'resize.image.php';

$id = $_GET['id'] ?? '';
if (empty($id)) {
    header("Location: manage_product.php");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT * FROM product WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch();

    if (!$product) {
        die("Product not found!");
    }
} catch (PDOException $e) {
    die("Database Error: " . $e->getMessage());
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $category_id = $_POST['category_id'];
    $name        = trim($_POST['name']);
    $price       = $_POST['price'];
    $stock       = intval($_POST['stock']);
    $type        = $_POST['type'];
    $photo       = $product['photo']; 

    if (!empty($_FILES['main_photo']['name'])) {
        $check = getimagesize($_FILES['main_photo']['tmp_name']);
        if ($check !== false) {
            $ext = strtolower(pathinfo($_FILES['main_photo']['name'], PATHINFO_EXTENSION));
            $new_filename = uniqid("main_") . "." . $ext;
            $target_path = "../uploads/products/" . $new_filename;
        
        if (move_uploaded_file($_FILES['main_photo']['tmp_name'], $target_path)) {
            resize_image($target_path, $target_path); // Resize the new image
            $photo = $new_filename;
        }
    } else {
        $error = "Invalid image file.";
    }
}
    try {
        $sql = "UPDATE product SET category_id = ?, name = ?, price = ?, stock = ?, type = ?, photo = ? WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$category_id, $name, $price, $stock, $type, $photo, $id]);
        
        echo "<script>alert('Product updated successfully!'); window.location='manage_product.php';</script>";
        exit();
    } catch (Exception $e) {
        $error = "Update failed: " . $e->getMessage();
    }
}



$categories = $pdo->query("SELECT * FROM category")->fetchAll();
?>

                <h2 class="section-title" style="text-align:left;">EDIT PRODUCT DETAILS</h2>
                        <?php if(isset($error)): ?>
                            <p style="color:red; background:#fee; padding:10px; border-left: 5px solid red;"><?= $error ?></p>
                        <?php endif; ?>

                    <form action="edit_product.php?id=<?= urlencode($id) ?>" method="POST" enctype="multipart/form-data">

                    <div class="form-group">
                        <label>Product Name</label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
                    </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label>Category</label>
                                <select name="category_id" class="form-control" required>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($cat['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Type</label>
                                <select name="type" class="form-control">
                                    <option value="Shoes" <?= $product['type'] == 'Shoes' ? 'selected' : '' ?>>Shoes</option>
                                    <option value="Clothing" <?= $product['type'] == 'Clothing' ? 'selected' : '' ?>>Clothing</option>
                                </select>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label>Price (RM)</label>
                                <input type="number" name="price" class="form-control" step="0.01" value="<?= $product['price'] ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Stock Quantity</label>
                                <input type="number" name="stock" class="form-control" value="<?= $product['stock'] ?>" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Update Product Image (Leave blank to keep current)</label>
                            <input type="file" name="main_photo" class="form-control" accept="image/*">
                            
                            <div style="margin-top: 15px; border: 1px solid #eee; padding: 10px; display: inline-block; background: #fafafa;">
                                <p style="font-size: 12px; color: #888; margin-bottom: 5px;">Current Preview:</p>
                                <img src="../uploads/products/<?= $product['photo'] ?>" style="max-width: 150px; border: 1px solid #ddd; display: block;">
                            </div>
                        </div>

                        <div style="margin-top: 30px; display: flex; gap: 15px;">
                            <button type="submit" class="btn-shop" style="flex: 1; border: none; cursor: pointer;">
                                SAVE CHANGES
                            </button>
                            <a href="manage_product.php" class="btn-shop" style="flex: 1; background: #fff; color: #000; border: 1px solid #000; text-align: center; text-decoration: none; line-height: 20px; display: flex; align-items: center; justify-content: center;">
                                CANCEL
                            </a>
                        </div>
                    </form>


<?php 
include '../footer.php'; 
?>