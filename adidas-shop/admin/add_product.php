<?php
session_start();
require_once '../config.php';
require_once 'auth_check.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id          = trim($_POST['id'] ?? '');
    $category_id = trim($_POST['category_id'] ?? '');
    $name        = trim($_POST['name'] ?? '');
    $price       = $_POST['price'] ?? 0;
    $stock       = $_POST['stock'] ?? 0;
    $type        = $_POST['type'] ?? '';
    
    $photo = "";
    if (!empty($_FILES['main_photo']['name'])) {
        $photo = uniqid("main_") . "." . pathinfo($_FILES['main_photo']['name'], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES['main_photo']['tmp_name'], "../uploads/products/" . $photo);
    }

    try {
        $pdo->beginTransaction();

        if (empty($id) || $price <= 0 || empty($photo)) {
            throw new Exception("Invalid input data or missing main photo.");
        }

        $sql = "INSERT INTO product (id, category_id, name, price, stock, type, photo) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id, $category_id, $name, $price, $stock, $type, $photo]);

        if (!empty($_FILES['extra_photos']['name'][0])) {
            foreach ($_FILES['extra_photos']['tmp_name'] as $key => $tmp_name) {
                
                if ($_FILES['extra_photos']['error'][$key] == 0) {
                    $ext = pathinfo($_FILES['extra_photos']['name'][$key], PATHINFO_EXTENSION);
                    $new_filename = uniqid("img_") . "." . $ext; 
                    
                    if (move_uploaded_file($tmp_name, "../uploads/products/" . $new_filename)) {
                    
                        $sql_photo = "INSERT INTO product_photo (product_id, filename) VALUES (?, ?)";
                        $pdo->prepare($sql_photo)->execute([$id, $new_filename]);
                    }
                }
            }
        }

    
        $pdo->commit();
        echo "<script>alert('Product added successfully!'); window.location='manage_product.php';</script>";
        
    } catch (Exception $e) {
    
        $pdo->rollBack();
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}

$categories = $pdo->query("SELECT * FROM category")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ADIDAS ADMIN - Add Product</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <header class="main-header">
        <div class="container">
            <div class="logo">
                <a href="../index.php">ADIDAS ADMIN</a>
            </div>
            <div class="header-actions">
                <span>Welcome, <?= htmlspecialchars($_SESSION['user_name'] ?? 'Admin') ?></span>
                <a href="../member/logout.php" class="logout-btn">Logout</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="admin-layout">
            <aside class="admin-sidebar">
                <h3>Management</h3>
                <ul class="admin-nav-list">
                    <li><a href="index.php">🏠 Dashboard</a></li>
                    <li><a href="manage_product.php">📦 Manage Products</a></li>
                    <li><a href="add_product.php" class="active">➕ Add New Product</a></li>
                    <li><a href="category_maintenance.php">📂 Category Maintenance</a></li>
                    <li><a href="manage_orders.php">🛒 Manage Orders</a></li>
                </ul>
            </aside>

            <main class="admin-main">
                <div class="admin-card">
                    <div class="form-title">ADD NEW ADIDAS PRODUCT</div>
                    
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
                                <input type="number" name="price" class="form-control" step="0.01" required>
                            </div>

                            <div class="form-group">
                                <label>Stock Quantity</label>
                                <input type="number" name="stock" class="form-control" required>
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
                </div>
            </main>
        </div>
    </div>

    <script>
        $('form').on('submit', function() {
            return confirm('Confirm adding this product to Adidas Inventory?');
        });
    </script>
</body>
</html>
<?php 
    include '../footer.php'; 
?>