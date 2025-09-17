<?php
session_start();
require_once 'admin/db.php';

// Enable MySQLi error reporting
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

// ✅ Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=checkout.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Ensure cart is not empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    die("Your cart is empty. <a href='index.php'>Go back to shopping</a>");
}

// ✅ Calculate total amount
$total_amount = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_amount += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
}

// Convert cart to JSON for storing in DB
$cart_json = json_encode($_SESSION['cart']);

// ✅ Handle form submission
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payment_method = $_POST['payment_method'] ?? '';
    $address = trim($_POST['address'] ?? '');

    if (empty($address)) {
        $error = "Please enter your delivery address.";
    } elseif (empty($payment_method)) {
        $error = "Please select a payment method.";
    } else {
        // Insert order into database
        $stmt = $conn->prepare("INSERT INTO orders(user_id, cart, amount, payment_method, status, address) VALUES (?,?,?,?,?,?)");
        if(!$stmt){
            die("Prepare failed: " . $conn->error);
        }

        $status = ($payment_method === 'COD') ? 'Pending Payment' : 'Awaiting Payment';
        $stmt->bind_param("isdsss", $user_id, $cart_json, $total_amount, $payment_method, $status, $address);
        $stmt->execute();
        $order_id = $stmt->insert_id;
        $stmt->close();

        // Razorpay redirection
        if ($payment_method === 'Razorpay') {
            $_SESSION['razorpay_order_id'] = $order_id;
            $_SESSION['razorpay_amount'] = $total_amount * 100; // Amount in paise
            header("Location: razorpay_checkout.php");
            exit;
        }

        // COD - clear cart
        $_SESSION['cart'] = [];
        header("Location: order_success.php?order_id=".$order_id);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Checkout | Kraftrix</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { background: #f8f9fa; }
.checkout-container { margin-top: 30px; }
.cart-item { background: #fff; border-radius: 10px; padding: 15px; margin-bottom: 15px; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
.cart-item img { width: 100px; height: 100px; object-fit: contain; border:1px solid #eee; border-radius:8px; }
.product-name { font-size:1.1rem; font-weight:500; }
.price { font-size:1.1rem; font-weight:bold; color:#B12704; }
.payment-option { cursor:pointer; }
.payment-option:hover { background: #f1f1f1; }
</style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="container checkout-container">
  <div class="row">

    <!-- Cart Items -->
    <div class="col-lg-8">
      <h3 class="mb-4">Review Cart</h3>
      <?php foreach ($_SESSION['cart'] as $item): ?>
        <div class="cart-item d-flex align-items-center">
          <img src="images/<?= htmlspecialchars($item['image'] ?? 'no-image.png'); ?>" alt="<?= htmlspecialchars($item['name'] ?? ''); ?>" class="me-3">
          <div class="flex-grow-1">
            <h5 class="product-name"><?= htmlspecialchars($item['name'] ?? ''); ?></h5>
            <p class="price">₹<?= number_format($item['price'] ?? 0, 2); ?> × <?= $item['quantity'] ?? 1; ?> = ₹<?= number_format(($item['price'] ?? 0)*($item['quantity'] ?? 1),2); ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <!-- Checkout / Payment -->
    <div class="col-lg-4">
      <div class="card p-3 shadow-sm">
        <h5>Delivery Address</h5>
        <?php if(!empty($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST">
          <textarea name="address" class="form-control mb-3" placeholder="Enter your delivery address" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>

          <h5>Payment Method</h5>
          <div class="mb-3 payment-option p-2 border rounded">
            <input type="radio" name="payment_method" value="Razorpay" required <?= (($_POST['payment_method'] ?? '')==='Razorpay')?'checked':'' ?>> <i class="fas fa-credit-card"></i> Razorpay / Card / UPI
          </div>
          <div class="mb-3 payment-option p-2 border rounded">
            <input type="radio" name="payment_method" value="COD" required <?= (($_POST['payment_method'] ?? '')==='COD')?'checked':'' ?>> <i class="fas fa-money-bill-alt"></i> Cash on Delivery
          </div>

          <hr>
          <p><strong>Total Amount: ₹<?= number_format($total_amount,2); ?></strong></p>
          <button type="submit" class="btn btn-warning w-100 btn-lg">Place Order</button>
        </form>
      </div>
    </div>

  </div>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
