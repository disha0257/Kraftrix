<?php  
session_start();
require_once 'admin/db.php';

// ✅ Get product ID
$product_id = intval($_GET['id'] ?? 0);
if (!$product_id) die("Product not found");

// ✅ Fetch product
$stmt = $conn->prepare("SELECT * FROM products WHERE product_id=?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
if (!$product) die("Product not found");

// ✅ Image Handling
$imgFile = trim($product['image'] ?? '');
$diskPath = __DIR__ . "/images/" . $imgFile;
if (!$imgFile || !file_exists($diskPath)) {
    $imgFile = "no-image.png";
}
$imgPath = "/Kraftrix/images/" . $imgFile;

// ✅ Price + Discount
$actualPrice     = floatval($product['price']);
$discountPercent = intval($product['discount_percent'] ?? 0);
$discountPrice   = $discountPercent > 0 
    ? $actualPrice - ($actualPrice * ($discountPercent / 100)) 
    : $actualPrice;

// ✅ Add to Cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }

    $qty = max(1, intval($_POST['quantity'] ?? 1));

    if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += $qty;
    } else {
        $_SESSION['cart'][$product_id] = [
            'id'       => $product_id,
            'name'     => $product['name'],
            'price'    => $discountPrice,
            'image'    => $imgFile,
            'quantity' => $qty
        ];
    }

    header("Location: cart.php"); 
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($product['name']); ?> | Kraftrix</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <style>
    body { background: #f8f9fa; }
    .product-img { max-height: 450px; object-fit: contain; }
    .price { font-size: 1.8rem; font-weight: bold; color: #B12704; }
    .old-price { text-decoration: line-through; color: #6c757d; margin-left: 10px; font-size: 1.1rem; }
    .discount { color: green; font-weight: 600; margin-left: 10px; }
    .product-box { background:#fff; border-radius:10px; padding:20px; box-shadow:0 2px 8px rgba(0,0,0,0.1);}
    .info-list li { margin-bottom: 10px; font-size: 1rem; display: flex; align-items: center; gap: 8px; }
    .info-list i { color: green; }
  </style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="container py-5">
  <div class="row g-4">
    
    <!-- Product Image -->
    <div class="col-md-5 text-center">
      <div class="product-box">
        <img src="<?= htmlspecialchars($imgPath); ?>" 
             class="img-fluid product-img" 
             alt="<?= htmlspecialchars($product['name']); ?>">
      </div>
    </div>

    <!-- Product Details -->
    <div class="col-md-7">
      <div class="product-box h-100">
        <h2 class="mb-3"><?= htmlspecialchars($product['name']); ?></h2>
        
        <!-- Price + Discount -->
        <p class="price mb-2">
          ₹<?= number_format($discountPrice, 2); ?>
          <?php if ($discountPercent > 0): ?>
            <span class="old-price">₹<?= number_format($actualPrice, 2); ?></span>
            <span class="discount">(<?= $discountPercent; ?>% OFF)</span>
          <?php endif; ?>
        </p>

        <p class="text-muted"><?= nl2br(htmlspecialchars($product['short_desc'] ?? $product['description'])); ?></p>

        <?php if(isset($_SESSION['user_id'])): ?>
        <form method="POST" class="d-flex gap-2 align-items-center my-3">
          <input type="number" name="quantity" value="1" min="1" class="form-control w-25">
          <button type="submit" name="add_to_cart" class="btn btn-warning btn-lg">
            <i class="fas fa-cart-plus"></i> Add to Cart
          </button>
        </form>
        <?php else: ?>
          <a href="login.php?redirect=<?= urlencode($_SERVER['REQUEST_URI']); ?>" 
             class="btn btn-warning btn-lg">
             <i class="fas fa-sign-in-alt"></i> Login to Buy
          </a>
        <?php endif; ?>

        <hr>

        <!-- Extra Info -->
        <ul class="info-list list-unstyled">
          <li><i class="fas fa-truck"></i> Free Delivery</li>
          <li><i class="fas fa-undo"></i> 10 Days Returnable</li>
          <li><i class="fas fa-credit-card"></i> Cash/Pay on Delivery</li>
          <li><i class="fas fa-shield-alt"></i> Secure Transaction</li>
          <li><i class="fas fa-star"></i> Top Brand Quality</li>
        </ul>
      </div>
    </div>
  </div>

  <!-- Product Description -->
  <div class="mt-5">
    <h3>Product Description</h3>
    <div class="card p-4 shadow-sm border-0">
      <h5 class="mb-3">About this item</h5>
      <p><?= nl2br(htmlspecialchars($product['description'])); ?></p>

      <h5 class="mt-4 mb-3">Key Features</h5>
      <ul>
        <li>✅ High-quality materials with premium durability</li>
        <li>✅ Lightweight and easy to use design</li>
        <li>✅ 1 Year Warranty included</li>
        <li>✅ Available in multiple sizes/colors</li>
        <li>✅ Eco-friendly & sustainable packaging</li>
      </ul>

      <h5 class="mt-4 mb-3">Specifications</h5>
      <table class="table table-bordered">
        <tbody>
          <tr><th>Brand</th><td><?= htmlspecialchars($product['brand'] ?? 'Kraftrix'); ?></td></tr>
          <tr><th>Model</th><td><?= htmlspecialchars($product['sku'] ?? 'N/A'); ?></td></tr>
          <tr><th>Material</th><td>Cotton / Plastic / Metal</td></tr>
          <tr><th>Dimensions</th><td>30 x 20 x 10 cm</td></tr>
          <tr><th>Weight</th><td>500g</td></tr>
          <tr><th>Warranty</th><td>1 Year</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
