<?php
session_start();
include("db.php");
include("includes/header.php");

// Success messages
if (isset($_GET['msg'])) {
    echo "<div class='alert alert-success'>" . htmlspecialchars($_GET['msg']) . "</div>";
}
?>

<h2 class="fw-bold mb-4">Manage Products</h2>
<a href="add_product.php" class="btn btn-primary mb-3"><i class="bi bi-plus-circle"></i> Add New Product</a>

<div class="table-responsive">
  <table class="table table-bordered align-middle">
    <thead class="table-light">
      <tr>
        <th>ID</th>
        <th>Image</th>
        <th>Name</th>
        <th>Price</th>
        <th>Stock</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $result = $conn->query("SELECT * FROM products ORDER BY product_id DESC");
      if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
      ?>
        <tr>
          <td><?php echo $row['product_id']; ?></td>
          <td><img src="../images/<?php echo htmlspecialchars($row['image']); ?>" width="50"></td>
          <td><?php echo htmlspecialchars($row['name']); ?></td>
          <td>₹<?php echo number_format($row['price'], 2); ?></td>
          <td><?php echo $row['stock'] ?? 'N/A'; ?></td>
          <td>
            <a href="edit_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
            <a href="delete_product.php?id=<?php echo $row['product_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this product?')"><i class="bi bi-trash"></i></a>
          </td>
        </tr>
      <?php } } else {
          echo "<tr><td colspan='6'>No products found.</td></tr>";
      } ?>
    </tbody>
  </table>
</div>

<?php include("includes/footer.php"); ?>
