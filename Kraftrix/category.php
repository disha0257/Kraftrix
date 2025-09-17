<?php
session_start();
require_once 'admin/db.php';

// ✅ Get category slug safely
$category_slug = isset($_GET['cat']) ? trim($_GET['cat']) : '';
$category_id = 0;

// ✅ Fetch category_id if slug exists
if ($category_slug !== '') {
    $stmtCat = $conn->prepare("SELECT category_id FROM categories WHERE category_slug = ?");
    $stmtCat->bind_param("s", $category_slug);
    $stmtCat->execute();
    $resCat = $stmtCat->get_result();
    if ($resCat->num_rows > 0) {
        $rowCat = $resCat->fetch_assoc();
        $category_id = $rowCat['category_id'];
    }
}

// ✅ Price filter
$min_price = isset($_GET['min_price']) ? floatval($_GET['min_price']) : 0;
$max_price = isset($_GET['max_price']) ? floatval($_GET['max_price']) : 0;

// ✅ Sorting
$sort = $_GET['sort'] ?? 'newest';

// ✅ Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 9;
$offset = ($page-1)*$limit;

// ✅ Categories for dropdown
$categories = $conn->query("SELECT * FROM categories ORDER BY category_name ASC");

// ✅ Build product query
$query = "SELECT * FROM products WHERE 1=1";
$params = [];
$types = "";

if ($category_id > 0) {
    $query .= " AND category_id=?";
    $params[] = $category_id;
    $types .= "i";
}
if ($min_price > 0) {
    $query .= " AND price>=?";
    $params[] = $min_price;
    $types .= "d";
}
if ($max_price > 0) {
    $query .= " AND price<=?";
    $params[] = $max_price;
    $types .= "d";
}

// Sorting
switch($sort) {
    case 'price_low': $query .= " ORDER BY price ASC"; break;
    case 'price_high': $query .= " ORDER BY price DESC"; break;
    default: $query .= " ORDER BY created_at DESC"; break;
}

// Pagination
$query .= " LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$types .= "ii";

$stmt = $conn->prepare($query);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$products = $stmt->get_result();

// ✅ Total products for pagination
$totalQuery = "SELECT COUNT(*) AS total FROM products WHERE 1=1";
if ($category_id > 0) $totalQuery .= " AND category_id=$category_id";
if ($min_price > 0) $totalQuery .= " AND price>=$min_price";
if ($max_price > 0) $totalQuery .= " AND price<=$max_price";
$totalRes = $conn->query($totalQuery);
$totalCount = $totalRes->fetch_assoc()['total'];
$totalPages = ceil($totalCount/$limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Kraftrix - Category</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
body { background:#f8f9fa; }
.product-card { transition: transform 0.2s; position:relative; overflow:hidden; }
.product-card:hover { transform: scale(1.03); box-shadow:0 4px 12px rgba(0,0,0,0.15);}
.product-actions { position:absolute; top:10px; right:-60px; display:flex; flex-direction:column; gap:10px; transition:right 0.3s;}
.product-card:hover .product-actions { right:10px;}
.icon-btn { background:white; border:none; border-radius:50%; width:40px;height:40px; display:flex; align-items:center; justify-content:center; box-shadow:0 2px 5px rgba(0,0,0,0.15); cursor:pointer;}
.icon-btn:hover { background:#f1f1f1;}
.price { font-weight:bold; color:#B12704; }
.old-price { text-decoration:line-through; color:gray; font-size:0.9rem; margin-left:5px; }
.badge-discount { position:absolute; top:10px; left:10px; background:#dc3545; color:white; padding:5px 7px; font-size:0.8rem; border-radius:5px; }
.sidebar-card { position:sticky; top:20px;}
</style>
</head>
<body>
<?php include 'header.php'; ?>

<div class="container-fluid py-4">
<div class="row">

<!-- Sidebar -->
<div class="col-lg-3 mb-4">
  <div class="card p-3 shadow-sm sidebar-card">
    <!-- Category Dropdown -->
    <h5>Categories</h5>
    <div class="dropdown mb-3">
      <button class="btn btn-outline-primary w-100 dropdown-toggle" type="button" data-bs-toggle="dropdown">
        <?= $category_slug ? htmlspecialchars(str_replace('-', ' ', $category_slug)) : 'All'; ?>
      </button>
      <ul class="dropdown-menu w-100">
        <li><a class="dropdown-item" href="category.php?cat=">All</a></li>
        <?php
        $categories->data_seek(0); // reset pointer
        while($cat=$categories->fetch_assoc()):
        ?>
          <li><a class="dropdown-item" href="category.php?cat=<?= $cat['category_slug']; ?>"><?= htmlspecialchars($cat['category_name']);?></a></li>
        <?php endwhile;?>
      </ul>
    </div>

    <hr>
    <h5>Price Filter</h5>
    <form method="GET" action="category.php">
      <input type="hidden" name="cat" value="<?= htmlspecialchars($category_slug);?>">
      <input type="number" name="min_price" placeholder="Min" class="form-control mb-2" value="<?= $min_price>0?$min_price:'';?>">
      <input type="number" name="max_price" placeholder="Max" class="form-control mb-2" value="<?= $max_price>0?$max_price:'';?>">
      <button class="btn btn-primary w-100 mb-2">Apply</button>
      <a href="category.php?cat=<?= $category_slug;?>" class="btn btn-secondary w-100">Reset Filters</a>
    </form>
  </div>
</div>

<!-- Products Grid -->
<div class="col-lg-9">
  <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
    <div>Showing <?= $offset+1;?>–<?= min($offset+$limit,$totalCount);?> of <?= $totalCount;?> results</div>
    <div>
      <select class="form-select" onchange="location=this.value;" style="width:auto;">
        <option value="category.php?cat=<?= $category_slug;?>&sort=newest" <?= $sort=='newest'?'selected':'';?>>Newest</option>
        <option value="category.php?cat=<?= $category_slug;?>&sort=price_low" <?= $sort=='price_low'?'selected':'';?>>Price: Low → High</option>
        <option value="category.php?cat=<?= $category_slug;?>&sort=price_high" <?= $sort=='price_high'?'selected':'';?>>Price: High → Low</option>
      </select>
    </div>
  </div>

  <div class="row g-4">
    <?php if($products->num_rows>0): ?>
      <?php while($prod=$products->fetch_assoc()):
        $img = $prod['image'] ?: 'no-image.png';
        $imgPath = "images/$img";
        $finalPrice = $prod['price'] - ($prod['price']*($prod['discount_percent']/100));
      ?>
      <div class="col-md-4 col-sm-6">
        <div class="card product-card h-100 position-relative">
          <?php if($prod['discount_percent']>0): ?>
            <span class="badge-discount"><?= $prod['discount_percent'];?>% OFF</span>
          <?php endif; ?>
          <a href="product.php?id=<?= $prod['product_id']; ?>">
            <img src="<?= $imgPath;?>" class="card-img-top" alt="<?= htmlspecialchars($prod['name']);?>" style="height:250px;object-fit:cover;">
          </a>
          <div class="card-body text-center">
            <h6><?= htmlspecialchars($prod['name']);?></h6>
            <p class="price">₹<?= number_format($finalPrice,2);?>
              <?php if($prod['discount_percent']>0): ?>
                <span class="old-price">₹<?= number_format($prod['price'],2);?></span>
              <?php endif;?>
            </p>
          </div>
          <div class="product-actions d-flex flex-column gap-2 position-absolute">
            <form method="POST" action="cart_action.php">
              <input type="hidden" name="product_id" value="<?= $prod['product_id'];?>">
              <button class="icon-btn"><i class="fa fa-shopping-cart text-warning"></i></button>
            </form>
            <form method="POST" action="wishlist_action.php">
              <input type="hidden" name="product_id" value="<?= $prod['product_id'];?>">
              <button class="icon-btn"><i class="fa fa-heart text-danger"></i></button>
            </form>
            <a href="product.php?id=<?= $prod['product_id'];?>" class="icon-btn"><i class="fa fa-eye text-primary"></i></a>
          </div>
        </div>
      </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p class="text-muted">No products found.</p>
    <?php endif;?>
  </div>

  <!-- Pagination -->
  <nav class="mt-4">
    <ul class="pagination justify-content-center flex-wrap">
      <?php if($page>1): ?>
        <li class="page-item"><a class="page-link" href="category.php?cat=<?= $category_slug;?>&page=<?= $page-1;?>&sort=<?= $sort;?>">Previous</a></li>
      <?php endif;?>
      <?php for($i=1;$i<=$totalPages;$i++): ?>
        <li class="page-item <?= $i==$page?'active':'';?>">
          <a class="page-link" href="category.php?cat=<?= $category_slug;?>&page=<?= $i;?>&sort=<?= $sort;?>"><?= $i;?></a>
        </li>
      <?php endfor;?>
      <?php if($page<$totalPages): ?>
        <li class="page-item"><a class="page-link" href="category.php?cat=<?= $category_slug;?>&page=<?= $page+1;?>&sort=<?= $sort;?>">Next</a></li>
      <?php endif;?>
    </ul>
  </nav>
</div>

</div>
</div>

<?php include 'footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
