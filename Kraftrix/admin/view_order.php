<?php
session_start();
include("db.php");
include("includes/header.php");

// Get order ID from URL safely
$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($order_id <= 0) {
    echo "<div class='alert alert-danger'>Invalid order ID.</div>";
    include("includes/footer.php");
    exit;
}

// Handle status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status'])) {
    $new_status = $_POST['status'];
    $updateQuery = $conn->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
    $updateQuery->bind_param("si", $new_status, $order_id);
    if ($updateQuery->execute()) {
        echo "<div class='alert alert-success'>Order status updated successfully.</div>";
    } else {
        echo "<div class='alert alert-danger'>Failed to update order status: {$conn->error}</div>";
    }
}

// Fetch order details
$orderQuery = $conn->prepare("SELECT o.order_id, o.user_id, o.total_price, o.status, o.created_at, u.name AS customer, u.email, u.phone 
                              FROM orders o 
                              LEFT JOIN users u ON o.user_id = u.user_id 
                              WHERE o.order_id = ?");
$orderQuery->bind_param("i", $order_id);
$orderQuery->execute();
$orderResult = $orderQuery->get_result();

if ($orderResult->num_rows === 0) {
    echo "<div class='alert alert-warning'>Order not found.</div>";
    include("includes/footer.php");
    exit;
}

$order = $orderResult->fetch_assoc();

// Fetch order items
$itemsQuery = $conn->prepare("SELECT oi.*, p.name AS product_name, p.image 
                              FROM order_items oi 
                              LEFT JOIN products p ON oi.product_id = p.product_id 
                              WHERE oi.order_id = ?");
$itemsQuery->bind_param("i", $order_id);
$itemsQuery->execute();
$itemsResult = $itemsQuery->get_result();
?>

<div class="container my-5">
    <h2 class="mb-4">Order Details #<?= $order['order_id'] ?></h2>

    <div class="mb-3">
        <strong>Customer:</strong> <?= htmlspecialchars($order['customer'] ?? 'Guest') ?><br>
        <strong>Email:</strong> <?= htmlspecialchars($order['email'] ?? '-') ?><br>
        <strong>Phone:</strong> <?= htmlspecialchars($order['phone'] ?? '-') ?><br>
        <strong>Created At:</strong> <?= $order['created_at'] ?>
    </div>

    <!-- Status Update Form -->
    <form method="post" class="mb-4">
        <label for="status" class="form-label"><strong>Order Status:</strong></label>
        <select name="status" id="status" class="form-select w-auto d-inline-block">
            <?php
            $statuses = ['pending','shipped','delivered','cancelled'];
            foreach ($statuses as $statusOption) {
                $selected = ($order['status'] === $statusOption) ? 'selected' : '';
                echo "<option value='{$statusOption}' {$selected}>" . ucfirst($statusOption) . "</option>";
            }
            ?>
        </select>
        <button type="submit" class="btn btn-primary">Save Status</button>
    </form>

    <h4>Order Items</h4>
    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>Product</th>
                    <th>Image</th>
                    <th>Quantity</th>
                    <th>Price</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $counter = 1;
                $grand_total = 0;
                while ($item = $itemsResult->fetch_assoc()): 
                    $subtotal = $item['price'] * $item['quantity'];
                    $grand_total += $subtotal;
                ?>
                <tr>
                    <td><?= $counter++ ?></td>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td>
                        <?php 
                        $imgPath = 'images/' . ($item['image'] ?: 'no-image.png');
                        if (!file_exists($imgPath)) $imgPath = 'images/no-image.png';
                        ?>
                        <img src="<?= $imgPath ?>" width="70" class="rounded">
                    </td>
                    <td><?= $item['quantity'] ?></td>
                    <td>₹<?= number_format($item['price'],2) ?></td>
                    <td>₹<?= number_format($subtotal,2) ?></td>
                </tr>
                <?php endwhile; ?>
                <tr>
                    <td colspan="5" class="text-end fw-bold">Total</td>
                    <td class="fw-bold">₹<?= number_format($grand_total,2) ?></td>
                </tr>
            </tbody>
        </table>
    </div>

    <a href="orders.php" class="btn btn-secondary mt-3">Back to Orders</a>
</div>

<?php include("includes/footer.php"); ?>
