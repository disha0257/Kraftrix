<?php
session_start();
include 'admin/db.php'; // Database connection

$cart = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];
$user_id = 1; // Example: logged-in user ID. Replace with session user_id in real app.

if(empty($cart)){
    header("Location: cart.php");
    exit;
}

$orderPlaced = false;

if(isset($_POST['place_order'])){
    $total = 0;
    foreach($cart as $item){
        $total += $item['price'] * $item['quantity'];
    }

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, status) VALUES (?, ?, 'pending')");
    $stmt->bind_param("id", $user_id, $total);
    $stmt->execute();

    $order_id = $stmt->insert_id; // Get the new order ID
    $orderPlaced = true;
    unset($_SESSION['cart']); // Clear cart
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Place Order</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2 class="mb-4">Place Your Order</h2>

    <?php if($orderPlaced): ?>
        <div class="alert alert-success">
            <h4>Order Placed Successfully!</h4>
            <p>Your order ID is: <strong>#<?= $order_id ?></strong></p>
            <a href="index.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-12">
                <h4>Your Cart</h4>
                <table class="table table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Product</th>
                            <th>Quantity</th>
                            <th>Price</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $total = 0;
                        foreach($cart as $item):
                            $subtotal = $item['price'] * $item['quantity'];
                            $total += $subtotal;
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td><?= $item['quantity'] ?></td>
                            <td>$<?= number_format($item['price'],2) ?></td>
                            <td>$<?= number_format($subtotal,2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                        <tr>
                            <td colspan="3"><strong>Total</strong></td>
                            <td><strong>$<?= number_format($total,2) ?></strong></td>
                        </tr>
                    </tbody>
                </table>
                <form method="post">
                    <button type="submit" name="place_order" class="btn btn-success">Place Order</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
