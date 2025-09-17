<?php
session_start();
include("db.php");
include("includes/header.php");
?>

<h2 class="fw-bold mb-4">Manage Orders</h2>

<?php
// Show success message after delete (optional)
if (isset($_GET['msg']) && $_GET['msg'] == 'deleted') {
    echo "<div class='alert alert-success'>Order deleted successfully.</div>";
}
?>

<div class="table-responsive">
  <table class="table table-bordered align-middle">
    <thead class="table-light">
      <tr>
        <th>Order ID</th>
        <th>Customer</th>
        <th>Total Price</th>
        <th>Status</th>
        <th>Created At</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Query real orders from database with correct column name
      $query = "SELECT o.order_id, u.name AS customer, o.total_price, o.status, o.created_at
                FROM orders o
                LEFT JOIN users u ON o.user_id = u.user_id
                ORDER BY o.created_at DESC";
      $orders = $conn->query($query);

      // Check for query errors
      if (!$orders) {
          echo "<tr><td colspan='6'>Error: " . $conn->error . "</td></tr>";
      } elseif ($orders->num_rows === 0) {
          echo "<tr><td colspan='6'>No orders found.</td></tr>";
      } else {
          while ($row = $orders->fetch_assoc()) {
              $status = $row['status'];
              $badge = match(strtolower($status)) {
                  'delivered' => 'bg-success',
                  'shipped' => 'bg-info',
                  'pending' => 'bg-secondary',
                  'cancelled' => 'bg-danger',
                  default => 'bg-warning text-dark'
              };
              echo "<tr>
                      <td>#{$row['order_id']}</td>
                      <td>" . htmlspecialchars($row['customer'] ?? 'Guest') . "</td>
                      <td>₹" . number_format($row['total_price'], 2) . "</td>
                      <td><span class='badge {$badge}'>" . htmlspecialchars($status) . "</span></td>
                      <td>{$row['created_at']}</td>
                      <td>
                        <a href='view_order.php?id={$row['order_id']}' class='btn btn-sm btn-primary'><i class='bi bi-eye'></i></a>
                        <a href='delete_order.php?id={$row['order_id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Delete this order?')\"><i class='bi bi-trash'></i></a>
                      </td>
                    </tr>";
          }
      }
      ?>
    </tbody>
  </table>
</div>

<?php include("includes/footer.php"); ?>
