<?php
session_start();
include 'admin/db.php';

// Get product id
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Fetch product
$stmt = $conn->prepare("SELECT p.*, c.category_name 
                        FROM product p 
                        JOIN category c ON p.category_id = c.category_id 
                        WHERE product_id=?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();

// Fetch related products
$rel_stmt = $conn->prepare("SELECT * FROM product WHERE category_id=? AND product_id!=? LIMIT 4");
$rel_stmt->bind_param("ii", $product['category_id'], $product_id);
$rel_stmt->execute();
$related = $rel_stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?php echo $product['name']; ?></title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <div class="product-detail">
    <img src="images/<?php echo $product['image']; ?>" alt="<?php echo $product['name']; ?>">
    <div>
      <h1><?php echo $product['name']; ?></h1>
      <h3>Category: <?php echo $product['category_name']; ?></h3>
      <p><?php echo $product['description']; ?></p>
      <h2>₹<?php echo number_format($product['price'], 2); ?></h2>
      <form method="post" action="add_to_cart.php">
        <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
        <button type="submit">Add to Cart</button>
      </form>
    </div>
  </div>

  <h2>Related Products</h2>
  <div class="product-grid">
    <?php while($row = $related->fetch_assoc()) { ?>
      <div class="product-card">
        <a href="product_view.php?id=<?php echo $row['product_id']; ?>">
          <img src="images/<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>">
          <h3><?php echo $row['name']; ?></h3>
          <p>₹<?php echo number_format($row['price'], 2); ?></p>
        </a>
      </div>
    <?php } ?>
  </div>
</body>
</html>
