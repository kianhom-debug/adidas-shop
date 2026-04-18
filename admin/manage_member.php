<?php
$page_title = "ADIDAS - Manage Members";
include 'admin_header.php';

$search = $_GET['search'] ?? '';

try {

    $sql = "SELECT id, name, email, photo FROM users 
            WHERE name LIKE ? OR email LIKE ? 
            ORDER BY id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute(["%$search%", "%$search%"]);

    $members = $stmt->fetchAll();
} catch (PDOException $e) { 

    die("Error: " . $e->getMessage()); 
}
?>

<h2 class="section-title" style="text-align:left;">MEMBER DIRECTORY</h2>

<form action="manage_member.php" method="GET" class="admin-search-bar" style="display:flex; gap:10px; margin-bottom:20px;">
    <input type="text" name="search" class="form-control" placeholder="Search name or email..." value="<?= htmlspecialchars($search) ?>">
    <button type="submit" class="btn-shop">SEARCH</button>
</form>

<table class="product-table">
    <thead>
        <tr>
            <th>Profile</th>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($members as $u): ?>
        <tr>
            <td>
                <?php if(!empty($u['photo'])): ?>
                    <img src="../uploads/profiles/<?= htmlspecialchars($u['photo']) ?>" class="admin-thumb" style="width:50px; height:50px; border-radius:50%; object-fit:cover;">
                <?php else: ?>
                    <div class="no-image-placeholder" style="width:50px; height:50px; line-height:50px; background:#eee; font-size:10px; border-radius:50%; text-align:center; color:#888;">No Photo</div>
                <?php endif; ?>
            </td>
            <td>#<?= $u['id'] ?></td>
            <td><strong><?= htmlspecialchars($u['name']) ?></strong></td>
            <td><?= htmlspecialchars($u['email']) ?></td>

        </tr>
        <?php endforeach; ?>
        
        <?php if (empty($members)): ?>
        <tr>
            <td colspan="5" style="text-align:center; padding:20px;">No members found.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php 
    include '../footer.php';
?>