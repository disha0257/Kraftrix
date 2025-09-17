<?php  
// dashboard.php
session_start();
include(__DIR__ . "/db.php");
include("includes/header.php");

// --- Helper function to safely get counts ---
function getCount($conn, $table, $column='*') {
    $result = $conn->query("SELECT COUNT($column) AS c FROM $table");
    if ($result) {
        return $result->fetch_assoc()['c'] ?? 0;
    } else {
        error_log("Query failed for table $table: " . $conn->error);
        return 0;
    }
}

// --- Fetch real stats safely ---
$usersCount      = getCount($conn, 'users');
$ordersCount     = getCount($conn, 'orders');
$productsCount   = getCount($conn, 'products');
$categoriesCount = getCount($conn, 'categories');

// Revenue sum safely
$revenueSum = 0;
$res = $conn->query("SELECT SUM(total_amount) AS s FROM orders WHERE status='delivered'");
if ($res) {
    $revenueSum = $res->fetch_assoc()['s'] ?? 0;
}

// Recent orders safely
$recentOrders = $conn->query("
    SELECT o.order_id, u.name AS customer, o.total_amount, o.status, o.created_at
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    ORDER BY o.created_at DESC
    LIMIT 5
");

if (!$recentOrders) {
    $recentOrders = [];
    error_log("Recent orders query failed: " . $conn->error);
}
?>

<div class="container-fluid">
  <!-- Header row -->
  <div class="d-flex flex-wrap align-items-center justify-content-between mb-4">
    <div>
      <h2 class="fw-bold mb-1">Dashboard</h2>
      <div class="text-muted">Overview & insights at a glance</div>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <a href="admin_panel.php" class="btn btn-primary">
        <i class="bi bi-plus-circle"></i> Add Product
      </a>
      <a href="manage_orders.php" class="btn btn-outline-dark">
        <i class="bi bi-cart4"></i> Manage Orders
      </a>
      <a href="categories.php" class="btn btn-outline-primary">
        <i class="bi bi-tags"></i> Manage Categories
      </a>
    </div>
  </div>

  <!-- Stat cards -->
  <div class="row g-3">
    <?php
    $stats = [
        ['label' => 'Total Users', 'value' => $usersCount, 'icon' => 'people-fill'],
        ['label' => 'Orders', 'value' => $ordersCount, 'icon' => 'basket-fill'],
        ['label' => 'Revenue', 'value' => "₹" . number_format($revenueSum), 'icon' => 'cash-stack'],
        ['label' => 'Products', 'value' => $productsCount, 'icon' => 'box-seam'],
        ['label' => 'Categories', 'value' => $categoriesCount, 'icon' => 'tags-fill']
    ];
    foreach ($stats as $stat): ?>
    <div class="col-12 col-sm-6 col-xl-3">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <div class="d-flex align-items-center justify-content-between">
            <div>
              <div class="text-muted small"><?= $stat['label'] ?></div>
              <div class="h3 fw-bold mb-0"><?= $stat['value'] ?></div>
            </div>
            <div class="display-6"><i class="bi bi-<?= $stat['icon'] ?>"></i></div>
          </div>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
  </div>

  <!-- Recent Orders -->
  <div class="row g-3 mt-3">
    <div class="col-12 col-xl-7">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 pb-0">
          <h5 class="mb-0">Sales Overview</h5>
        </div>
        <div class="card-body">
          <p class="text-muted">You can integrate charts here from orders if needed.</p>
        </div>
      </div>
    </div>

    <div class="col-12 col-xl-5">
      <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-0 pb-0">
          <h5 class="mb-0">Recent Orders</h5>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table align-middle">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Customer</th>
                  <th>Total</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <?php if (!empty($recentOrders) && $recentOrders->num_rows > 0): ?>
                  <?php while($row = $recentOrders->fetch_assoc()): ?>
                    <tr>
                      <td>#<?= $row['order_id'] ?></td>
                      <td><?= htmlspecialchars($row['customer']) ?></td>
                      <td>₹<?= number_format($row['total_amount']) ?></td>
                      <td>
                        <?php 
                        $status = $row['status'];
                        $badgeClass = match($status) {
                            'delivered' => 'success',
                            'shipped'   => 'primary',
                            'pending'   => 'warning text-dark',
                            'cancelled' => 'danger',
                            default     => 'secondary',
                        };
                        ?>
                        <span class="badge bg-<?= $badgeClass ?>"><?= ucfirst($status) ?></span>
                      </td>
                    </tr>
                  <?php endwhile; ?>
                <?php else: ?>
                  <tr><td colspan="4" class="text-center text-muted">No recent orders</td></tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          <a href="manage_orders.php" class="btn btn-sm btn-outline-dark mt-2">View all</a>
        </div>
      </div>
    </div>
  </div>
</div>

<?php include("includes/footer.php"); ?>
