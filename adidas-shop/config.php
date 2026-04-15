<?php
$host = "localhost";
$dbname = "adidas_shop";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB Error: " . $e->getMessage());
}

//test
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    $id = trim($_POST['id'] ?? '');
    $category_id = trim($_POST['category_id'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $price = $_POST['price'] ?? 0;
    $stock = $_POST['stock'] ?? 0;
    $type = $_POST['type'] ?? '';
    
    $photo = "";
 
    if (!empty($_FILES['photo']['name'])) {
        
        $photo = uniqid("main_") . "." . pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        
        move_uploaded_file($_FILES['photo']['tmp_name'], "../uploads/products/" . $photo);
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
        echo "<script>alert('Product and photos added successfully!'); window.location='manage_product.php';</script>";
        
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "<script>alert('Error: " . $e->getMessage() . "');</script>";
    }
}

?>