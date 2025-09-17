<?php 
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/admin/db.php';

// --- CART COUNT ---
$cart_count = 0;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT SUM(quantity) AS total FROM cart WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result && $row = $result->fetch_assoc()) {
        $cart_count = $row['total'] ?? 0;
    }
}

// --- WISHLIST COUNT ---
$wishlist_count = (!empty($_SESSION['wishlist']) && is_array($_SESSION['wishlist']))
    ? count($_SESSION['wishlist'])
    : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kraftrix - Handicraft Store</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <style>
    .navbar { background: #fff !important; box-shadow: 0 2px 6px rgba(0,0,0,0.05); }
    .navbar-brand { font-size: 24px; font-weight: bold; color: #0d6efd !important; margin-right: 30px; }
    .nav-link { color: #333 !important; font-weight: 500; margin: 0 5px; }
    .nav-link:hover { color: #0d6efd !important; }
    .search-box { width: 250px; margin-right: 20px; }
    .search-box input { border-radius: 20px 0 0 20px; border: 1px solid #ddd; padding: 6px 12px; }
    .search-box button { border-radius: 0 20px 20px 0; padding: 6px 12px; }
    .icon-link { font-size: 20px; margin-left: 15px; color: #0d6efd !important; position: relative; }
    .icon-link:hover { color: #0056b3 !important; }
    .badge-count { position: absolute; top: -5px; right: -8px; background: red; color: white; font-size: 12px; border-radius: 50%; padding: 2px 6px; }
  </style>
</head>
<body>

<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container-fluid">
    
    <!-- Logo -->
    <a class="navbar-brand" href="index.php">Kraftrix</a>

    <!-- Mobile toggle -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="mainNavbar">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" id="categoriesDropdown" role="button" data-bs-toggle="dropdown">
            Categories
          </a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="category.php?cat=home-decor">Home Décor</a></li>
            <li><a class="dropdown-item" href="category.php?cat=jewelry">Jewelry</a></li>
            <li><a class="dropdown-item" href="category.php?cat=clothing">Clothing</a></li>
            <li><a class="dropdown-item" href="category.php?cat=kitchen">Kitchen</a></li>
            <li><a class="dropdown-item" href="category.php?cat=art">Art</a></li>
            <li><a class="dropdown-item" href="category.php?cat=festive">Festive & Gifts</a></li>
          </ul>
        </li>
        <li class="nav-item"><a class="nav-link" href="contact.php">Contact</a></li>
        <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
      </ul>

      <!-- Search -->
      <form class="d-flex search-box" action="search.php" method="GET">
        <input class="form-control" type="search" name="q" placeholder="Search..." required>
        <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
      </form>

      <!-- Icons -->
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
          <a class="icon-link" href="wishlist.php">
            <i class="bi bi-heart"></i>
            <?php if($wishlist_count > 0): ?>
              <span class="badge-count"><?= $wishlist_count ?></span>
            <?php endif; ?>
          </a>
        </li>
        <li class="nav-item">
          <a class="icon-link" href="cart.php">
            <i class="bi bi-cart"></i>
            <?php if($cart_count > 0): ?>
              <span class="badge-count"><?= $cart_count ?></span>
            <?php endif; ?>
          </a>
        </li>

        <?php if(isset($_SESSION['email'])): ?>
          <?php if($_SESSION['role'] === "admin"): ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i> Admin
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="admin/dashboard.php">Dashboard</a></li>
                <li><a class="dropdown-item" href="admin/products.php">Products</a></li>
                <li><a class="dropdown-item" href="admin/orders.php">Orders</a></li>
              </ul>
            </li>
          <?php else: ?>
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown">
                <i class="bi bi-person-circle"></i> <?= htmlspecialchars($_SESSION['email']); ?>
              </a>
              <ul class="dropdown-menu dropdown-menu-end">
                <li><a class="dropdown-item" href="account.php">My Account</a></li>
                <li><a class="dropdown-item" href="orders.php">My Orders</a></li>
              </ul>
            </li>
          <?php endif; ?>
          <li class="nav-item"><a class="nav-link text-danger fw-bold" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a></li>
          <li class="nav-item"><a class="nav-link" href="register.php"><i class="bi bi-person-plus"></i> Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
