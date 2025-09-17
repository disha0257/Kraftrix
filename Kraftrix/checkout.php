<?php 
session_start();
include 'admin/db.php';

// Logged-in user ID
$user_id = $_SESSION['user_id'] ?? 1;

// Get cart from session
$cart = $_SESSION['cart'] ?? [];
if(empty($cart)){
    header("Location: cart.php");
    exit;
}

// Calculate cart total
$total = 0;
foreach($cart as $item){
    $total += $item['price'] * $item['quantity'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout - Kraftrix</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
body { background: #f2f2f2; font-family: 'Segoe UI', sans-serif; }
.checkout-wrapper { max-width: 1100px; margin: 40px auto; }
.card { border-radius: 12px; box-shadow: 0 4px 20px rgba(0,0,0,0.08); }
.card-header { font-weight: 600; background: #ff9800; color: #fff; border-radius: 12px 12px 0 0; }
.form-control, .form-select { border-radius: 8px; }
h5 { font-weight: 600; margin-bottom: 15px; }
.cart-summary { background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
.cart-summary table { margin-bottom: 15px; }
.total-row { font-weight: 700; font-size: 1.2rem; }
.btn-place { background: #ff9800; color: #fff; font-weight: 600; width: 100%; padding: 12px; border-radius: 8px; }
.btn-place:hover { background: #e68900; }
.section-header { background: #f8f9fa; padding: 12px 20px; border-radius: 12px 12px 0 0; margin-bottom: 15px; font-weight: 600; }
</style>
</head>
<body>

<div class="container checkout-wrapper">
    <div class="row">
        <!-- Left Column: Customer & Payment Details -->
        <div class="col-lg-7 mb-4">
            <div class="card p-4 mb-4">
                <div class="section-header"><i class="fa fa-user me-2"></i>Shipping Information</div>
                <form action="place_order.php" method="post">
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <input type="text" name="fullname" class="form-control" placeholder="Full Name" required>
                        </div>
                        <div class="col-md-6">
                            <input type="email" name="email" class="form-control" placeholder="Email" required>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <input type="text" name="phone" class="form-control" placeholder="Phone" required>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="address" class="form-control" placeholder="Address" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label"><i class="fa fa-credit-card me-2"></i>Payment Method</label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cod">Cash on Delivery</option>
                            <option value="online">Online Payment</option>
                        </select>
                    </div>
        </div>
        </div>

        <!-- Right Column: Cart Summary -->
        <div class="col-lg-5">
            <div class="cart-summary">
                <h5><i class="fa fa-shopping-cart me-2"></i>Your Cart</h5>
                <table class="table table-borderless">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($cart as $item): 
                            $subtotal = $item['price'] * $item['quantity'];
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']); ?></td>
                            <td><?= $item['quantity']; ?></td>
                            <td>₹<?= number_format($subtotal,2); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="total-row text-end">Total: ₹<?= number_format($total,2); ?></div>
                <button type="submit" class="btn btn-place mt-3">Place Order</button>
            </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
                            