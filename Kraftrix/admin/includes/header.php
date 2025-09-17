<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kraftrix Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-dark bg-dark sticky-top p-3 shadow-sm">
  <a class="navbar-brand fw-bold" href="dashboard.php">⚡ Kraftrix Admin</a>
  <a href="logout.php" class="btn btn-outline-light btn-sm"><i class="bi bi-box-arrow-right"></i> Logout</a>
</nav>

<div class="d-flex">
  <!-- Sidebar -->
  <div class="bg-dark text-white p-3 vh-100" style="width:240px;">
      <h6 class="text-uppercase text-secondary">Menu</h6>
      <ul class="nav flex-column">
          <li class="nav-item"><a href="dashboard.php" class="nav-link text-white"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
          <li class="nav-item"><a href="manage_products.php" class="nav-link text-white"><i class="bi bi-box-seam"></i> Products</a></li>
          <li class="nav-item"><a href="manage_orders.php" class="nav-link text-white"><i class="bi bi-cart4"></i> Orders</a></li>
          <li class="nav-item"><a href="manage_users.php" class="nav-link text-white"><i class="bi bi-people"></i> Users</a></li>
      </ul>
  </div>

  <!-- Main Content -->
  <div class="flex-grow-1 bg-light p-4">
