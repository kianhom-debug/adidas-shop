<?php

$page_title = "ADIDAS - Manage Products";
include 'admin_header.php';
require_once 'resize.image.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id          = trim($_POST['id'] ?? '');
    $category_id = trim($_POST['category_id'] ?? '');
    $name        = trim($_POST['name'] ?? '');
    $price       = floatval($_POST['price'] ?? 0); 
    $stock       = intval($_POST['stock'] ?? 0);   
    $type        = $_POST['type'] ?? '';
    $photo       = "";

    try {
        if (empty($id) || empty($name)) {
            throw new Exception("Product ID and Name are required.");
        }
        if ($price < 0) {
            throw new Exception("Price cannot be negative (RM 0.00 is minimum).");
        }
        if ($stock < 0) {
            throw new Exception("Stock cannot be negative.");
        }

        if (!empty($_FILES['main_photo']['name'])) {
            $check = getimagesize($_FILES['main_photo']['tmp_name']);
            if($check === false) {
                throw new Exception("File is not a valid image.");
            }

            $ext = strtolower(pathinfo($_FILES['main_photo']['name'], PATHINFO_EXTENSION));
            $main_filename = uniqid("main_") . "." . $ext;
            $target_main = "../uploads/products/" . $main_filename;
            
            if (move_uploaded_file($_FILES['main_photo']['tmp_name'], $target_main)) {
                resize_image($target_main, $target_main);
                $photo = $main_filename;
            }
        }

        if (empty($photo)) {
            throw new Exception("Please upload a valid main photo.");
        }
        $pdo->beginTransaction();

        $checkId = $pdo->prepare("SELECT id FROM product WHERE id = ?");
        $checkId->execute([$id]);
        if ($checkId->fetch()) {
            throw new Exception("Product ID '$id' already exists in inventory.");
        }

        $sql = "INSERT INTO product (id, category_id, name, price, stock, type, photo) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id, $category_id, $name, $price, $stock, $type, $photo]);

        if (!empty($_FILES['extra_photos']['name'][0])) {
            foreach ($_FILES['extra_photos']['tmp_name'] as $key => $tmp_name) {
                if ($_FILES['extra_photos']['error'][$key] == 0) {
                    $ext = strtolower(pathinfo($_FILES['extra_photos']['name'][$key], PATHINFO_EXTENSION));
                    $new_filename = uniqid("img_") . "." . $ext; 
                    $target_path = "../uploads/products/" . $new_filename;
                    
                    if (move_uploaded_file($tmp_name, $target_path)) {
                        resize_image($target_path, $target_path);
                        $sql_photo = "INSERT INTO product_photo (product_id, filename) VALUES (?, ?)";
                        $pdo->prepare($sql_photo)->execute([$id, $new_filename]);
                    }
                }
            }
        }

        $pdo->commit();
        echo "<script>alert('Product added successfully!'); window.location='manage_product.php';</script>";
        
        } catch (Exception $e) {
            if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        
        echo "<script>alert('Error: " . addslashes($e->getMessage()) . "');</script>";
    }
}


$categories = $pdo->query("SELECT * FROM category")->fetchAll();
?>

                <h2 class="section-title" style="text-align:left;">ADD NEW PRODUCT</h2>
                    <form action="add_product.php" method="POST" enctype="multipart/form-data">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label>Product ID</label>
                                <input type="text" name="id" class="form-control" maxlength="4" placeholder="e.g. S007" required>
                            </div>

                            <div class="form-group">
                                <label>Category</label>
                                <select name="category_id" class="form-control" required>
                                    <option value="">-- SELECT CATEGORY --</option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label>Product Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label>Type</label>
                                    <select name="type" class="form-control">
                                    <option value="Shoes">Shoes</option>
                                    <option value="Clothing">Clothing</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Price (RM)</label>
                                <input type="number" name="price" class="form-control" step="0.01" min="0" required>
                            </div>

                            <div class="form-group">
                                <label>Stock Quantity</label>
                                <input type="number" name="stock" class="form-control" min="0" required>
                            </div>
                        </div>

                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                            <div class="form-group">
                                <label>Main Photo</label>
                                <input type="file" name="main_photo" class="form-control" accept="image/*" required>
                            </div>

                            <div class="form-group">
                                <label>Extra Photos</label>
                                <input type="file" name="extra_photos[]" class="form-control" accept="image/*" multiple>
                            </div>
                        </div>

                        <div style="margin-top: 20px;">
                            <button type="submit" class="btn-shop" style="width: 100%; border: 3px solid #000; cursor: pointer;">UPLOAD TO INVENTORY</button>
                        </div>
                    </form>

    <script>
        $('form').on('submit', function() {
            return confirm('Confirm adding this product to Adidas Inventory?');
        });
    </script>

<?php 
    include '../footer.php'; 
?>