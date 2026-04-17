<?php
$page_title = "ADIDAS - Manage Products";
include 'admin_header.php';


if (isset($_POST['add_category'])) {
    $name = trim($_POST['cat_name']);
    
    if (empty($name)) {
        $error = "Category name cannot be empty.";
    } 
    elseif (is_numeric($name)) {
        $error = "Category name cannot be numbers only.";
    } 
    else {
        $pdo->prepare("INSERT INTO category (name) VALUES (?)")->execute([$name]);
        header("Location: category_maintenance.php?msg=added");
        exit();
    }
}
if (isset($_POST['delete_category'])) {
    try {
        $pdo->prepare("DELETE FROM category WHERE id = ?")->execute([$_POST['delete_id']]);
        header("Location: category_maintenance.php?msg=deleted");
        exit();
    } catch (Exception $e) {
        $error = "Cannot delete: This category is being used by products.";
    }
}
$categories = $pdo->query("SELECT * FROM category")->fetchAll();
?>


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
                                    <form action="category_maintenance.php" method="POST" style="display:inline;" onsubmit="return confirm('Warning: This may affect products. Continue?')">
                                        <input type="hidden" name="delete_id" value="<?= $cat['id'] ?>">
                                        <button type="submit" name="delete_category" class="btn-delete" style="color:red; border:none; background:none; cursor:pointer;">
                                            DELETE
                                        </button>
                                    </form>
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

<?php 
    include '../footer.php'; 
?>