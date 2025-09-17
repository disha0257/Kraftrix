<?php
session_start();
include("db.php");
include("includes/header.php");

$id = $_GET['id'] ?? 0;
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id=?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
if (!$product) {
    die("Product not found");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name']);
    $price = (float) $_POST['price'];
    $stock = (int) $_POST['stock'];
    $image = $product['image'];

    // If new image uploaded
    if (!empty($_FILES['image']['name'])) {
        $image = time() . "_" . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], "../images/" . $image);
    }

    $stmt = $conn->prepare("UPDATE products SET name=?, price=?, stock=?, image=? WHERE product_id=?");
    $stmt->bind_param("sdisi", $name, $price, $stock, $image, $id);

    if ($stmt->execute()) {
        header("Location: manage_products.php?msg=Product updated successfully");
        exit;
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<h2 class="fw-bold mb-4">Edit Product</h2>

<?php if (!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

<form method="post" enctype="multipart/form-data" class="card p-4 shadow-sm" style="max-width:600px;">
  <div class="mb-3">
    <label class="form-label">Product Name</label>
    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Price</label>
    <input type="number" step="0.01" name="price" class="form-control" value="<?php echo $product['price']; ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Stock</label>
    <input type="number" name="stock" class="form-control" value="<?php echo $product['stock']; ?>" required>
  </div>
  <div class="mb-3">
    <label class="form-label">Image</label><br>
    <img src="../images/<?php echo htmlspecialchars($product['image']); ?>" width="80" class="mb-2"><br>
    <input type="file" name="image" class="form-control">
  </div>
  <button type="submit" class="btn btn-primary">Update Product</button>
  <a href="manage_products.php" class="btn btn-secondary">Cancel</a>
</form>

<?php include("includes/footer.php"); ?>
