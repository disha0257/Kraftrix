<?php
// index.php
include("admin/db.php"); // your database connection
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kraftrix | Handmade Crafts</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;800&family=Roboto:wght@400;500&display=swap" rel="stylesheet">

  <style>
    body { font-family: 'Roboto', sans-serif; }
    .hero {
      background: url('images/banner.png') center/cover no-repeat;
      height: 94vh;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      position: relative;
    }
    .hero::after { content: ""; position: absolute; inset: 0; background: rgba(0,0,0,0.5); }
    .hero-content { position: relative; z-index: 2; }
    .hero h1 { font-family: 'Poppins', sans-serif; font-size: 3.2rem; font-weight: 800; color: #fff; text-shadow: 2px 4px 15px rgba(0,0,0,0.7); }
    .hero p { font-size: 1.2rem; font-weight: 400; color: #f1f1f1; text-shadow: 1px 2px 8px rgba(0,0,0,0.6); }

    .category-card, .product-card { transition: transform 0.3s; cursor: pointer; }
    .category-card:hover, .product-card:hover { transform: translateY(-8px); }
    .card-img-top { height: 200px; object-fit: cover; }
    .category-icon { font-size: 2rem; color: #6c63ff; }
  </style>
</head>
<body>

<?php include("header.php"); ?>

<!-- Hero Section -->
<section class="hero">
  <div class="hero-content text-white">
    <h1 class="fw-bold"></h1>
    <p class="lead"></p>
    <a href="shop.php" class="btn btn-lg btn-light mt-3">Shop Now</a>
  </div>
</section>

<!-- Categories -->
<section class="container py-5">
  <div class="text-center mb-4">
    <h2 class="fw-bold">Shop by Category</h2>
    <p class="text-muted">Choose from our handcrafted collections</p>
  </div>
  <div class="row g-4 text-center">
    <?php
    $catQuery = "SELECT * FROM categories ORDER BY category_name ASC";
    $catResult = $conn->query($catQuery);
    if ($catResult && $catResult->num_rows > 0) {
        while ($cat = $catResult->fetch_assoc()) {
            echo '<div class="col-md-3">
                    <a href="shop.php?category='.$cat['category_id'].'" style="text-decoration:none;">
                      <div class="card category-card h-100 p-3 shadow-sm border-0">
                        <div class="category-icon"><i class="bi bi-grid"></i></div>
                        <h5 class="mt-3 text-dark">'.$cat['category_name'].'</h5>
                      </div>
                    </a>
                  </div>';
        }
    } else {
        echo "<p class='text-center'>No categories found.</p>";
    }
    ?>
  </div>
</section>

<!-- Best Selling Products -->
<section class="container py-5">
  <div class="text-center mb-4">
    <h2 class="fw-bold">Our Best Selling Products</h2>
    <p class="text-muted">Crafted with passion, delivered with love.</p>
  </div>
  <div class="row g-4">
    <?php
    $bestQuery = "SELECT * FROM products WHERE status='active' ORDER BY sales_count DESC LIMIT 4";
    $bestResult = $conn->query($bestQuery);

    if ($bestResult && $bestResult->num_rows > 0) {
        while ($prod = $bestResult->fetch_assoc()) {
            $imagePath = !empty($prod['image']) ? 'images/'.$prod['image'] : 'images/default.jpg';
            echo '<div class="col-md-3">
                    <div class="card product-card h-100 shadow-sm">
                      <img src="'.$imagePath.'" class="card-img-top" alt="'.$prod['name'].'">
                      <div class="card-body text-center">
                        <h5 class="card-title">'.$prod['name'].'</h5>
                        <p class="card-text">₹'.$prod['price'].'</p>
                        <a href="product.php?id='.$prod['product_id'].'" class="btn btn-primary">View</a>
                      </div>
                    </div>
                  </div>';
        }
    } else {
        echo "<p class='text-center'>No best selling products found.</p>";
    }
    ?>
  </div>
</section>

<!-- New Arrivals (Coming Soon Products) -->
<section class="container py-5">
  <div class="text-center mb-4">
    <h2 class="fw-bold">New Arrivals</h2>
    <p class="text-muted">Coming soon to our store</p>
  </div>
  <div class="row g-4">
    <?php
    $newQuery = "SELECT * FROM products WHERE status='coming_soon' ORDER BY created_at DESC LIMIT 4";
    $newResult = $conn->query($newQuery);

    if ($newResult && $newResult->num_rows > 0) {
        while ($prod = $newResult->fetch_assoc()) {
            $imagePath = !empty($prod['image']) ? 'images/'.$prod['image'] : 'images/default.jpg';
            echo '<div class="col-md-3">
                    <div class="card product-card h-100 shadow-sm border-warning">
                      <img src="'.$imagePath.'" class="card-img-top" alt="'.$prod['name'].'">
                      <div class="card-body text-center">
                        <h5 class="card-title">'.$prod['name'].' <span class="badge bg-warning text-dark">Coming Soon</span></h5>
                        <p class="card-text text-muted">Launching Soon</p>
                      </div>
                    </div>
                  </div>';
        }
    } else {
        echo "<p class='text-center'>No upcoming products right now.</p>";
    }
    ?>
  </div>
</section>

<!-- Special Offer Banner -->
<section class="py-5 bg-primary text-white text-center">
  <div class="container">
    <h2 class="fw-bold">Festival Sale – Up to 40% OFF</h2>
    <p>Don’t miss out on our limited-time offers!</p>
    <a href="shop.php" class="btn btn-light">Shop Deals</a>
  </div>
</section>

<!-- Footer -->
<?php include("footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
