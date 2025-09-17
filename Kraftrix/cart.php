<?php
session_start();
require_once 'admin/db.php';

// ✅ Ensure cart exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ✅ Handle Remove Item
if (isset($_GET['remove'])) {
    $removeId = $_GET['remove'];
    if (isset($_SESSION['cart'][$removeId])) {
        unset($_SESSION['cart'][$removeId]);
    }
    header("Location: cart.php");
    exit;
}

// ✅ Handle Update Quantity
if (isset($_POST['update_qty'])) {
    foreach ($_POST['qty'] as $id => $qty) {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] = max(1, intval($qty));
        }
    }
    header("Location: cart.php");
    exit;
}

// ✅ Calculate subtotal
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += ($item['price'] ?? 0) * ($item['quantity'] ?? 1);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Shopping Cart | Kraftrix</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<style>
    body { background:#f8f9fa; }
    .cart-container { margin-top:30px; }
    .cart-item { background:#fff; border-radius:10px; padding:15px; margin-bottom:15px; box-shadow:0 2px 6px rgba(0,0,0,0.08); }
    .cart-item img { width:120px; height:120px; object-fit:contain; border:1px solid #eee; border-radius:8px; }
    .product-name { font-size:1.2rem; font-weight:500; }
    .price { font-size:1.1rem; font-weight:bold; color:#B12704; }
    .order-summary { background:#fff; border-radius:10px; padding:20px; box-shadow:0 2px 6px rgba(0,0,0,0.1); position:sticky; top:20px; }
    .qty-box { width:70px; }
    .cart-actions { display:flex; align-items:center; gap:10px; margin-top:8px; }
</style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="container cart-container">
  <div class="row">
    
    <!-- 🛒 Cart Items -->
    <div class="col-lg-8">
      <h3 class="mb-4">Shopping Cart</h3>

      <?php if (!empty($_SESSION['cart'])): ?>
        <form method="post" action="cart.php">
          <?php foreach ($_SESSION['cart'] as $id => $item): ?>
            <div class="cart-item d-flex">
              
              <!-- 🖼️ Product Image -->
              <img src="images/<?= htmlspecialchars($item['image'] ?? 'no-image.png'); ?>" 
                   alt="<?= htmlspecialchars($item['name'] ?? ''); ?>" class="me-3">
              
              <!-- 📄 Product Details -->
              <div class="flex-grow-1">
                <h5 class="product-name"><?= htmlspecialchars($item['name'] ?? ''); ?></h5>
                <p class="price">₹<?= number_format($item['price'] ?? 0, 2); ?></p>
                
                <!-- Qty + Update + Remove in same row -->
                <div class="cart-actions">
                  <label for="qty<?= $id; ?>" class="me-2">Qty:</label>
                  <input type="number" id="qty<?= $id; ?>" class="form-control qty-box" 
                         name="qty[<?= $id; ?>]" 
                         value="<?= $item['quantity'] ?? 1; ?>" min="1">
                  
                  <button type="submit" name="update_qty" class="btn btn-sm btn-primary">Update</button>
                  <a href="cart.php?remove=<?= $id; ?>" class="btn btn-sm btn-outline-danger">Remove</a>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </form>
      <?php else: ?>
        <div class="alert alert-info">Your cart is empty.</div>
      <?php endif; ?>
    </div>

    <!-- 📦 Order Summary -->
    <div class="col-lg-4">
      <div class="order-summary">
        <h5>Order Summary</h5>
        <hr>
        <p>Subtotal (<?= count($_SESSION['cart']); ?> items): 
           <strong>₹<?= number_format($subtotal, 2); ?></strong></p>
        <p class="text-success">✔ Free Delivery</p>
        <p class="text-success">✔ Secure Checkout</p>
        <hr>
        <?php if (!empty($_SESSION['cart'])): ?>
          <a href="checkout.php" class="btn btn-warning w-100 btn-lg">Proceed to Checkout</a>
        <?php else: ?>
          <button class="btn btn-warning w-100 btn-lg" disabled>Proceed to Checkout</button>
        <?php endif; ?>
      </div>
    </div>

  </div>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
