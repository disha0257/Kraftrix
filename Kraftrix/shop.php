<?php
session_start();
include("admin/db.php");
include("header.php");

// Ensure wishlist & cart sessions exist
if (!isset($_SESSION['wishlist'])) $_SESSION['wishlist'] = [];
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// Get Category Filter
$category_id = isset($_GET['category']) ? intval($_GET['category']) : 0;

// Fetch Categories
$categories = [];
$resCat = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");
if ($resCat) $categories = $resCat->fetch_all(MYSQLI_ASSOC);

// Fetch Products (only active ones)
$products = [];
if ($category_id > 0) {
    $sql = "SELECT p.*, c.category_name 
            FROM products p
            JOIN categories c ON p.category_id = c.category_id
            WHERE p.stock > 0 AND p.status = 'active' AND p.category_id = ?
            ORDER BY p.product_id DESC";
    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Prepare failed: " . $conn->error);
    $stmt->bind_param("i", $category_id);
} else {
    $sql = "SELECT p.*, c.category_name 
            FROM products p
            JOIN categories c ON p.category_id = c.category_id
            WHERE p.stock > 0 AND p.status = 'active'
            ORDER BY p.product_id DESC";
    $stmt = $conn->prepare($sql);
    if (!$stmt) die("Prepare failed: " . $conn->error);
}

$stmt->execute();
$result = $stmt->get_result();
if ($result) $products = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap">
        <h2 class="fw-bold mb-2">🛍️ Shop Products</h2>
        <form method="get" class="d-flex">
            <select name="category" class="form-select" onchange="this.form.submit()">
                <option value="0">All Categories</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['category_id'] ?>" <?= $category_id == $cat['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['category_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <div class="row g-4">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card shadow-sm h-100 border-0 rounded-3">
                        <div class="position-relative">
                            <img src="images/<?= htmlspecialchars($product['image'] ?? 'placeholder.png') ?>" 
                                 class="card-img-top rounded-top" 
                                 alt="<?= htmlspecialchars($product['name']) ?>" 
                                 style="height:220px; object-fit:cover;">
                            <!-- Wishlist Button -->
                            <a href="wishlist.php?add=<?= $product['product_id'] ?>" 
                               class="btn btn-sm btn-light position-absolute top-0 end-0 m-2 rounded-circle shadow">💖</a>
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($product['name']) ?></h5>
                            <p class="text-muted mb-1"><?= htmlspecialchars($product['category_name']) ?></p>
                            <p class="fw-bold text-primary mb-2">₹<?= number_format($product['price'], 2) ?></p>
                            <div class="mt-auto d-flex gap-2 flex-wrap">
                                <a href="cart.php?add=<?= $product['product_id'] ?>" 
                                   class="btn btn-sm btn-primary flex-grow-1">🛒 Add to Cart</a>
                                <a href="product.php?id=<?= $product['product_id'] ?>" 
                                   class="btn btn-sm btn-outline-secondary">View</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12 text-center py-5">
                <img src="https://cdn-icons-png.flaticon.com/512/1178/1178479.png" width="100" class="mb-3">
                <h5>No products found</h5>
                <p class="text-muted">Please check another category.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include("footer.php"); ?>

<style>
/* Modern card hover effect */
.card { transition: 0.3s; }
.card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.15); }
.btn-primary { background-color: #ff9900; border: none; }
.btn-primary:hover { background-color: #e68a00; }
</style>
