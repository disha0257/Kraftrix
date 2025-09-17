<?php
// wishlist.php
session_start();
require_once "admin/db.php";
if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}
$user_id = $_SESSION['user_id'];

// Add to wishlist
if (isset($_POST['add_to_wishlist'])) {
  $pid = intval($_POST['product_id']);
  $check = $conn->prepare("SELECT * FROM wishlist WHERE user_id=? AND product_id=?");
  $check->bind_param("ii", $user_id, $pid);
  $check->execute();
  if ($check->get_result()->num_rows == 0) {
    $stmt = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?,?)");
    $stmt->bind_param("ii", $user_id, $pid);
    $stmt->execute();
  }
}

// Remove
if (isset($_GET['remove'])) {
  $pid = intval($_GET['remove']);
  $conn->query("DELETE FROM wishlist WHERE user_id=$user_id AND product_id=$pid");
}

// Move to cart
if (isset($_GET['move'])) {
  $pid = intval($_GET['move']);
  $conn->query("INSERT INTO cart (user_id, product_id, quantity) VALUES ($user_id,$pid,1) ON DUPLICATE KEY UPDATE quantity=quantity+1");
  $conn->query("DELETE FROM wishlist WHERE user_id=$user_id AND product_id=$pid");
}

// Fetch wishlist
$sql = "SELECT w.*, p.name, p.price, p.image FROM wishlist w 
        JOIN products p ON w.product_id=p.product_id 
        WHERE w.user_id=$user_id";
$items = $conn->query($sql);

include "header.php";
?>
<div class="container mt-4">
  <h2>Your Wishlist</h2>
  <table class="table table-bordered">
    <tr><th>Image</th><th>Name</th><th>Price</th><th>Action</th></tr>
    <?php while($row = $items->fetch_assoc()): ?>
    <tr>
      <td><img src="images/<?= $row['image'] ?>" width="60"></td>
      <td><?= htmlspecialchars($row['name']) ?></td>
      <td>₹<?= $row['price'] ?></td>
      <td>
        <a href="wishlist.php?move=<?= $row['product_id'] ?>" class="btn btn-sm btn-primary">Move to Cart</a>
        <a href="wishlist.php?remove=<?= $row['product_id'] ?>" class="btn btn-sm btn-danger">Remove</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
</div>
<?php include "footer.php"; ?>
