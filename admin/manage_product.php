<?php
session_start();
require_once '../config.php';
require_once 'auth_check.php';
require_once 'resize.image.php';


$search = $_GET['search'] ?? '';

try {
    $sql = "SELECT p.*, c.name AS category_name 
            FROM product p 
            JOIN category c ON p.category_id = c.id 
            WHERE p.name LIKE ? 
            ORDER BY p.id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$search%"]);

    $products = $stmt->fetchAll();
} catch (PDOException $e) { 
    die("Error: " . $e->getMessage()); 
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ADIDAS - Manage Products</title>
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
                    <li><a href="manage_product.php" class="active">📦 Manage Products</a></li>
                    <li><a href="add_product.php">➕ Add New Product</a></li>
                    <li><a href="category_maintenance.php">📂 Category Maintenance</a></li>
                    <li><a href="manage_orders.php">🛒 Manage Orders</a></li>
                </ul>
            </aside>

            <main class="admin-main">
                <h2 class="section-title" style="text-align:left;">PRODUCT INVENTORY</h2>
                
                <form action="manage_product.php" method="GET" class="admin-search-bar" style="display:flex; gap:10px; margin-bottom:20px;">
                    <input type="text" name="search" class="form-control" placeholder="Search product name..." value="<?= htmlspecialchars($search) ?>">
                    <button type="submit" class="btn-shop">SEARCH</button>
                </form>

                <table class="product-table">
                    <thead>
                        <tr>
                            <th>Photo</th>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td>
                                <?php if($p['photo']): ?>
                                    <img src="../uploads/products/<?= $p['photo'] ?>" class="admin-thumb" style="width:50px; height:50px; object-fit:cover;">
                                <?php else: ?>
                                    <div class="no-image-placeholder">No Image</div>
                                <?php endif; ?>
                            </td>
                            <td>#<?= $p['id'] ?></td>
                            <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                            <td><?= htmlspecialchars($p['category_name']) ?></td>
                            <td>RM <?= number_format($p['price'], 2) ?></td>
                            <td>
                                <span class="<?= $p['stock'] < 10 ? 'low-stock' : '' ?>">
                                    <?= $p['stock'] ?>
                                </span>
                            </td>
                            <td>
                                <a href="edit_product.php?id=<?= $p['id'] ?>" class="action-link" style="color:green;">EDIT</a> | 
                                <a href="delete_product.php?id=<?= $p['id'] ?>" class="action-link" style="color:red;" onclick="return confirm('Confirm delete this product?')">DEL</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="7" class="empty-row">No products found.</td>
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