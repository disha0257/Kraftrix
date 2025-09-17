<?php
include("admin/db.php");
include("includes/header.php");

// Get filter type (all / new / best)
$type = $_GET['type'] ?? 'all';
$cat  = $_GET['cat'] ?? null;

$sql = "SELECT * FROM products";
$where = [];

if ($type === "new") {
    $where[] = "created_at >= NOW() - INTERVAL 30 DAY";
} elseif ($type === "best") {
    // For now, just assume best sellers = stock < 5 (you can adjust)
    $where[] = "stock > 0 ORDER BY stock ASC LIMIT 12";
}

if ($cat) {
    $where[] = "category_id = " . intval($cat);
}

if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY created_at DESC";

$result = $conn->query($sql);
?>

<main class="container py-5 mt-5">
  <div class="text-center mb-4">
    <h2 class="fw-bold">🛍️ Our Products</h2>
    <p class="text-muted">Browse our handcrafted collection</p>
  </div>

  <!-- Category Filter -->
  <div class="d-flex justify-content-center mb-4">
    <div class="btn-group">
      <a href="shop.php?type=all" class="btn btn-outline-primary <?=($type=='all'?'active':'')?>">All</a>
      <a href="shop.php?type=new" class="btn btn-outline-primary <?=($type=='new'?'active':'')?>">New</a>
      <a href="shop.php?type=best" class="btn btn-outline-primary <?=($type=='best'?'active':'')?>">Best Sellers</a>
    </div>
  </div>

  <!-- Product Grid -->
  <div class="row g-4">
    <?php if ($result && $result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <div class="col-md-3 col-sm-6">
          <div class="card h-100 shadow-sm">
            <img src="uploads/<?=htmlspecialchars($row['image'])?>" class="card-img-top" alt="<?=htmlspecialchars($row['name'])?>">
            <div class="card-body text-center">
              <h6 class="fw-bold"><?=htmlspecialchars($row['name'])?></h6>
              <?php if (!empty($row['sale_price']) && $row['sale_end'] > date("Y-m-d H:i:s")): ?>
                <p class="text-danger fw-bold">₹<?=$row['sale_price']?> <del class="text-muted">₹<?=$row['price']?></del></p>
              <?php else: ?>
                <p class="fw-bold">₹<?=$row['price']?></p>
              <?php endif; ?>
              <a href="product.php?id=<?=$row['id']?>" class="btn btn-sm btn-primary">View</a>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <div class="col-12 text-center">
        <p class="fs-5">No products found</p>
        <p class="text-muted">Please check another category.</p>
      </div>
    <?php endif; ?>
  </div>
</main>

<?php include("includes/footer.php"); ?>
