<?php
include("db.php");

// Delete product if id is provided
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);

    // Fetch the product first to get the image name
    $stmt = $conn->prepare("SELECT image FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Delete the image file if exists
        if (!empty($row['image'])) {
            $imagePath = __DIR__ . '/uploads/' . $row['image'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }

        // Delete the product from DB
        $delStmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
        $delStmt->bind_param("i", $delete_id);
        $delStmt->execute();

        header("Location: admin_panel.php?deleted=1");
        exit;
    } else {
        die("Product not found or query failed: " . $conn->error);
    }
}

// Fetch all products safely
$result = $conn->query("SELECT * FROM products ORDER BY product_id DESC");
if (!$result) {
    die("Error fetching products: " . $conn->error);
}
?>

<table border="1" cellpadding="5" cellspacing="0">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Price</th>
    <th>Action</th>
</tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= $row['product_id'] ?></td>
    <td><?= htmlspecialchars($row['name']) ?></td>
    <td>₹<?= number_format($row['price'], 2) ?></td>
    <td>
        <a href="admin_panel.php?delete_id=<?= $row['product_id'] ?>" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
