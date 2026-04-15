<?php
session_start();
require_once '../config.php';
require_once 'auth_check.php';

// login check 

if (isset($_POST['add_category'])) {
    $name = $_POST['cat_name'];
    if (!empty($name)) {
        $pdo->prepare("INSERT INTO category (name) VALUES (?)")->execute([$name]);
        header("Location: category_maintenance.php?msg=added");
        exit();
    }
}

if (isset($_GET['delete'])) {
    try {
        $pdo->prepare("DELETE FROM category WHERE id = ?")->execute([$_GET['delete']]);
        header("Location: category_maintenance.php?msg=deleted");
        exit();
    } catch (Exception $e) {
        $error = "Cannot delete: This category is being used by products.";
    }
}

$categories = $pdo->query("SELECT * FROM category")->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ADIDAS ADMIN - Category Maintenance</title>
    <link rel="stylesheet" href="../css/style.css">
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
                    <li><a href="add_product.php">➕ Add New Product</a></li>
                    <li><a href="category_maintenance.php" class="active">📂 Category Maintenance</a></li>
                </ul>
            </aside>

            <main class="admin-main">
                <h2 class="section-title" style="text-align:left;">CATEGORY MAINTENANCE</h2>

                <?php if (isset($error)): ?>
                    <div class="error-msg"><?= $error ?></div>
                <?php endif; ?>

                <div class="admin-card" style="margin-bottom: 30px;">
                    <form action="category_maintenance.php" method="POST" style="display: flex; gap: 10px;">
                        <input type="text" name="cat_name" class="form-control" placeholder="New Category Name (e.g. Originals)" required>
                        <button type="submit" name="add_category" class="btn-shop" style="white-space:nowrap;">ADD CATEGORY</button>
                    </form>
                </div>

                <table class="product-table">
                    <thead>
                        <tr>
                            <th width="15%">ID</th>
                            <th>Category Name</th>
                            <th width="20%">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td>#<?= $cat['id'] ?></td>
                            <td><strong><?= htmlspecialchars($cat['name']) ?></strong></td>
                            <td>
                                <a href="?delete=<?= $cat['id'] ?>" class="btn-delete" onclick="return confirm('Warning: This may affect products in this category. Continue?')">DELETE</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if (empty($categories)): ?>
                        <tr>
                            <td colspan="3" style="text-align:center; padding:30px; color:#999;">No categories found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </main>
        </div>
    </div>
</body>
</html>
<?php 
    include '../footer.php'; 
?>