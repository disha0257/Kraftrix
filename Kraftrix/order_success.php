<?php
session_start();
include 'admin/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

if ($order_id <= 0) die("Invalid Order ID.");

// Fetch order
$stmt = $conn->prepare("SELECT * FROM orders WHERE order_id=? AND user_id=?");
$stmt->bind_param("ii", $order_id, $user_id);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
if (!$order) die("Order not found.");

// Fetch items
$stmt2 = $conn->prepare("
    SELECT oi.*, p.name, p.image, COALESCE(oi.price, p.price) AS price
    FROM order_items oi
    JOIN products p ON oi.product_id = p.product_id
    WHERE oi.order_id=?
");
$stmt2->bind_param("i", $order_id);
$stmt2->execute();
$result_items = $stmt2->get_result();

$order_items = [];
$subtotal = 0;
while ($row = $result_items->fetch_assoc()) {
    $row['subtotal'] = $row['price'] * $row['quantity'];
    $subtotal += $row['subtotal'];
    $row['image_path'] = file_exists(__DIR__ . "/images/".$row['image']) ? "/Kraftrix/images/".$row['image'] : "/Kraftrix/images/no-image.png";
    $order_items[] = $row;
}

$total_paid = $subtotal; // You can add shipping/discount if you have
$payment_labels = ['cod'=>'Cash on Delivery','online'=>'Online Payment','razorpay'=>'Razorpay'];
$payment_method = $payment_labels[$order['payment_method']] ?? strtoupper($order['payment_method']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Order Success - Kraftrix</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
body { background:#f8fafc; font-family: 'Segoe UI', sans-serif; }
.container { max-width: 900px; margin:auto; padding:30px; }
.success-box { background:#fff; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.08); padding:30px; }
.success-header { text-align:center; margin-bottom:25px; }
.success-header h2 { color:#28a745; font-weight:700; margin-top:15px; }
.order-info { background:#f1f5f9; padding:20px; border-radius:10px; margin-bottom:25px; }
.order-item { display:flex; align-items:center; border-bottom:1px solid #eee; padding:15px 0; }
.order-item img { width:90px; height:90px; object-fit:cover; margin-right:20px; border-radius:8px; border:1px solid #ddd; }
.order-summary { font-size:20px; font-weight:600; text-align:right; margin-top:20px; }
.btn-custom { background:#ff9800; color:#fff; font-weight:600; padding:12px 25px; border-radius:8px; text-decoration:none; }
.btn-custom:hover { background:#e68900; }
</style>
</head>
<body>
<div class="container">
  <div class="success-box">
    <div class="success-header">
      <img src="https://img.icons8.com/color/96/000000/checked--v2.png">
      <h2>Thank You! Your Order Has Been Placed 🎉</h2>
    </div>

    <div class="order-info">
      <p><strong>Order ID:</strong> #<?= $order['order_id']; ?></p>
      <p><strong>Name:</strong> <?= htmlspecialchars($order['fullname']); ?></p>
      <p><strong>Email:</strong> <?= htmlspecialchars($order['email']); ?></p>
      <p><strong>Phone:</strong> <?= htmlspecialchars($order['phone']); ?></p>
      <p><strong>Address:</strong> <?= htmlspecialchars($order['address']); ?></p>
      <p><strong>Payment Method:</strong> <?= $payment_method; ?></p>
      <p><strong>Status:</strong> <?= ucfirst($order['status']); ?></p>
    </div>

    <h4>🛍 Your Items</h4>
    <?php if(count($order_items) > 0): ?>
        <?php foreach ($order_items as $item): ?>
            <div class="order-item">
                <img src="<?= $item['image_path']; ?>" alt="<?= htmlspecialchars($item['name']); ?>">
                <div>
                    <p><strong><?= htmlspecialchars($item['name']); ?></strong></p>
                    <p>Qty: <?= $item['quantity']; ?></p>
                    <p>Price: ₹<?= number_format($item['price'],2); ?></p>
                    <p>Subtotal: ₹<?= number_format($item['subtotal'],2); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-center text-muted">No items found.</p>
    <?php endif; ?>

    <div class="order-summary">
        <p><strong>Total Paid:</strong> ₹<?= number_format($total_paid,2); ?></p>
    </div>

    <div class="text-center mt-4">
        <a href="category.php" class="btn-custom">Continue Shopping</a>
    </div>
  </div>
</div>
</body>
</html>
