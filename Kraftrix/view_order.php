<?php
session_start();
include_once 'admin/db.php';

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$order_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $conn->prepare("
    SELECT o.*, u.name AS customer, u.email 
    FROM orders o 
    LEFT JOIN users u ON o.user_id = u.user_id 
    WHERE o.order_id=?
");
$stmt->bind_param("i", $order_id);
$stmt->execute();
$order_res = $stmt->get_result();
$order = $order_res->fetch_assoc();
$stmt->close();

if(!$order){
    die("Order not found.");
}

// Fetch order items
$stmt2 = $conn->prepare("
    SELECT oi.*, p.name, p.image 
    FROM order_items oi 
    LEFT JOIN products p ON oi.product_id=p.product_id 
    WHERE oi.order_id=?
");
$stmt2->bind_param("i", $order_id);
$stmt2->execute();
$items_res = $stmt2->get_result();
$stmt2->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Order #<?= $order_id ?> | Kraftrix</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body>
<?php include_once 'header.php'; ?>

<div class="container my-5">
    <h3 class="mb-4">Order Details #<?= $order_id ?></h3>

    <div class="mb-3">
        <strong>Customer:</strong> <?= htmlspecialchars($order['customer']) ?><br>
        <strong>Email:</strong> <?= htmlspecialchars($order['email']) ?><br>
        <strong>Order Status:</strong> <?= htmlspecialchars(ucfirst($order['status'])) ?><br>
        <strong>Created At:</strong> <?= $order['created_at'] ?>
    </div>

    <h5>Order Items</h5>
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
            $i = 1;
            $grand_total = 0;
            while($item = $items_res->fetch_assoc()):
                $subtotal = $item['price'] * $item['quantity'];
                $grand_total += $subtotal;
                $imgPath = "images/" . ($item['image'] ?? 'no-image.png');
                if(!file_exists($imgPath)){
                    $imgPath = "images/no-image.png";
                }
            ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($item['name']) ?></td>
                <td><img src="<?= $imgPath ?>" width="60" class="rounded"></td>
                <td><?= $item['quantity'] ?></td>
                <td>₹<?= number_format($item['price'],2) ?></td>
                <td>₹<?= number_format($subtotal,2) ?></td>
            </tr>
            <?php endwhile; ?>
            <tr>
                <td colspan="5" class="text-end fw-bold">Total</td>
                <td>₹<?= number_format($grand_total,2) ?></td>
            </tr>
        </tbody>
    </table>
</div>

<?php include_once 'footer.php'; ?>
</body>
</html>
