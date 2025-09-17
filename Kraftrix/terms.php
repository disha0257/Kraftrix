<?php // terms.php ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Terms & Conditions | Kraftrix</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
    .hero {
      background: url('images/terms.png') center/cover no-repeat;
      height: 60vh;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      text-align: center;
      position: relative;
    }
    .hero::after {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(0,0,0,0.6);
    }
    .hero-content {
      position: relative;
      z-index: 2;
    }
    .summary-card {
      border-radius: 12px;
      background: #f8f9fa;
      padding: 20px;
      height: 100%;
      transition: transform 0.3s;
    }
    .summary-card:hover {
      transform: translateY(-5px);
    }
    .terms-section h4 {
      margin-top: 25px;
      font-weight: 600;
      color: #0d6efd;
    }
  </style>
</head>
<body>

<?php include("header.php"); ?>

<!-- Hero Section -->
<section class="hero">
  <div class="hero-content">
    <h1 class="fw-bold display-5">Terms & Conditions</h1>
    <p class="lead">Understand your rights and responsibilities when using Kraftrix</p>
  </div>
</section>

<!-- Quick Summary -->
<section class="container py-5">
  <h2 class="fw-bold text-center mb-4">Quick Summary</h2>
  <div class="row g-4 text-center">
    <div class="col-md-3">
      <div class="summary-card shadow-sm">
        <i class="bi bi-bag-check-fill fs-1 text-primary"></i>
        <h6 class="mt-2">Orders</h6>
        <p class="text-muted small">Orders depend on availability & acceptance.</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="summary-card shadow-sm">
        <i class="bi bi-currency-rupee fs-1 text-success"></i>
        <h6 class="mt-2">Payments</h6>
        <p class="text-muted small">Secure payment methods are supported.</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="summary-card shadow-sm">
        <i class="bi bi-truck fs-1 text-warning"></i>
        <h6 class="mt-2">Delivery</h6>
        <p class="text-muted small">We ship across India with trusted partners.</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="summary-card shadow-sm">
        <i class="bi bi-arrow-counterclockwise fs-1 text-danger"></i>
        <h6 class="mt-2">Returns</h6>
        <p class="text-muted small">7-day return window for unused products.</p>
      </div>
    </div>
  </div>
</section>

<!-- Detailed Terms -->
<section class="container py-5 terms-section">
  <h2 class="fw-bold mb-4">Full Terms & Conditions</h2>

  <h4>1. Eligibility</h4>
  <p>You must be 18+ or have parental consent to use our services. Orders placed must include accurate information.</p>

  <h4>2. Products & Pricing</h4>
  <p>Prices are in INR and may change without notice. Product images are for representation and may vary slightly.</p>

  <h4>3. Orders & Payments</h4>
  <p>Orders are confirmed only after payment. We reserve the right to cancel fraudulent or suspicious orders.</p>

  <h4>4. Shipping & Delivery</h4>
  <p>Delivery times are estimates. We are not liable for courier delays or unforeseen circumstances.</p>

  <h4>5. Returns & Refunds</h4>
  <p>Returns accepted within 7 days. Refunds processed in 5–7 business days after inspection.</p>

  <h4>6. Intellectual Property</h4>
  <p>All site content belongs to Kraftrix. Unauthorized use is prohibited.</p>

  <h4>7. Limitation of Liability</h4>
  <p>We are not liable for indirect damages. Our liability is limited to the value of purchased goods.</p>

  <h4>8. Privacy & Data</h4>
  <p>See our <a href="privacy.php">Privacy Policy</a> to understand how we handle your information.</p>

  <h4>9. Governing Law</h4>
  <p>These Terms are governed by Indian law. Disputes are subject to Rajkot, Gujarat jurisdiction.</p>

  <h4>10. Contact Us</h4>
  <p>Questions? Reach us at <a href="contact.php">Contact Page</a> or email <strong>support@kraftrix.com</strong>.</p>
</section>

<?php include("footer.php"); ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
