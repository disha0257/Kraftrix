<?php
session_start();
include("db.php");

// --- Delete product ---
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    $stmt = $conn->prepare("SELECT image FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        if (!empty($row['image'])) {
            $imagePath = __DIR__ . '/uploads/' . $row['image'];
            if (file_exists($imagePath)) unlink($imagePath);
        }

        $delStmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
        $delStmt->bind_param("i", $delete_id);
        $delStmt->execute();

        header("Location: admin_panel.php?msg=deleted");
        exit;
    } else {
        die("Product not found or query failed: " . $conn->error);
    }
}

// --- Add/Edit Product ---
$editMode = false;
$productData = [
    'name' => '',
    'price' => '',
    'image' => '',
    'category_id' => ''
];

// Fetch categories for dropdown
$categories = $conn->query("SELECT category_id, category_name FROM categories ORDER BY category_name ASC");
$categoriesList = [];
if($categories && $categories->num_rows > 0){
    while($cat = $categories->fetch_assoc()){
        $categoriesList[] = $cat;
    }
}

// Edit mode
if (isset($_GET['id'])) {
    $editMode = true;
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res && $res->num_rows > 0) {
        $productData = $res->fetch_assoc();
    } else {
        die("Product not found.");
    }
}

// Handle form submission
if (isset($_POST['save_product'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category_id = intval($_POST['category_id']);
    $imageName = $productData['image'] ?? '';

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $imageName = time() . "." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/uploads/' . $imageName);

        if ($editMode && !empty($productData['image'])) {
            $oldImage = __DIR__ . '/uploads/' . $productData['image'];
            if (file_exists($oldImage)) unlink($oldImage);
        }
    }

    if ($editMode) {
        $stmt = $conn->prepare("UPDATE products SET name=?, price=?, image=?, category_id=? WHERE product_id=?");
        $stmt->bind_param("sdsii", $name, $price, $imageName, $category_id, $id);
        $stmt->execute();
        header("Location: admin_panel.php?msg=updated");
        exit;
    } else {
        $stmt = $conn->prepare("INSERT INTO products (name, price, image, category_id) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdsi", $name, $price, $imageName, $category_id);
        $stmt->execute();
        header("Location: admin_panel.php?msg=added");
        exit;
    }
}

// Fetch all products with category name
$products = $conn->query("
    SELECT p.*, c.category_name 
    FROM products p 
    LEFT JOIN categories c ON p.category_id = c.category_id 
    ORDER BY p.product_id DESC
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Panel | Kraftrix</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h2 class="mb-4">Admin Panel - Products</h2>

    <?php if(isset($_GET['msg'])): ?>
        <div class="alert alert-success">
            <?php 
                if($_GET['msg']=='deleted') echo "Product deleted successfully!";
                elseif($_GET['msg']=='added') echo "Product added successfully!";
                elseif($_GET['msg']=='updated') echo "Product updated successfully!";
            ?>
        </div>
    <?php endif; ?>

    <!-- Product Form -->
    <div class="card mb-4">
        <div class="card-header"><?= $editMode ? "Edit Product" : "Add New Product" ?></div>
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label>Name</label>
                    <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($productData['name']) ?>">
                </div>
                <div class="mb-3">
                    <label>Price</label>
                    <input type="number" step="0.01" name="price" class="form-control" required value="<?= htmlspecialchars($productData['price']) ?>">
                </div>
                <div class="mb-3">
                    <label>Category</label>
                    <select name="category_id" class="form-control" required>
                        <option value="">-- Select Category --</option>
                        <?php foreach($categoriesList as $cat): ?>
                            <option value="<?= $cat['category_id'] ?>" <?= ($editMode && $productData['category_id']==$cat['category_id']) ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['category_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Image</label>
                    <input type="file" name="image" class="form-control">
                    <?php if($editMode && !empty($productData['image'])): ?>
                        <img src="uploads/<?= htmlspecialchars($productData['image']) ?>" width="80" class="mt-2">
                    <?php endif; ?>
                </div>
                <button type="submit" name="save_product" class="btn btn-success"><?= $editMode ? "Update" : "Add" ?></button>
                <?php if($editMode): ?>
                    <a href="admin_panel.php" class="btn btn-secondary">Cancel</a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <!-- Products Table -->
    <table class="table table-bordered table-hover">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Price</th>
                <th>Category</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $products->fetch_assoc()): ?>
            <tr>
                <td><?= $row['product_id'] ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td>₹<?= number_format($row['price'],2) ?></td>
                <td><?= htmlspecialchars($row['category_name'] ?? 'N/A') ?></td>
                <td>
                    <?php if(!empty($row['image']) && file_exists(__DIR__ . '/uploads/' . $row['image'])): ?>
                        <img src="uploads/<?= htmlspecialchars($row['image']) ?>" width="80">
                    <?php else: ?> N/A <?php endif; ?>
                </td>
                <td>
                    <a href="admin_panel.php?id=<?= $row['product_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                    <a href="admin_panel.php?delete_id=<?= $row['product_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
