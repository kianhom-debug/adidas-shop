<?php
$page_title = "ADIDAS - Manage Products";
include 'admin_header.php';

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
            
<?php 
    include '../footer.php'; 
?>