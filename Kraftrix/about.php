<?php
// about.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>About Us | Kraftrix</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
    .about-hero {
      background: url('images/about-hero.jpg') center/cover no-repeat;
      height: 60vh;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      position: relative;
    }
    .about-hero::after {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(0,0,0,0.6);
    }
    .about-hero-content {
      position: relative;
      z-index: 2;
    }
    .mission-card {
      border-radius: 12px;
      background: #f8f9fa;
      padding: 25px;
      transition: transform 0.3s;
    }
    .mission-card:hover {
      transform: translateY(-5px);
    }
    .team-card img {
      border-radius: 50%;
      width: 120px;
      height: 120px;
      object-fit: cover;
    }
  </style>
</head>
<body>

<!-- Include Header -->
<?php include("header.php"); ?>

<!-- Hero Section -->
<section class="about-hero">
  <div class="about-hero-content">
    <h1 class="display-4 fw-bold">About Kraftrix</h1>
    <p class="lead">Where tradition meets creativity</p>
  </div>
</section>

<!-- Our Story -->
<section class="container py-5">
  <div class="row align-items-center">
    <div class="col-md-6">
      <img src="images/story.jpg" class="img-fluid rounded shadow" alt="Our Story">
    </div>
    <div class="col-md-6">
      <h2 class="fw-bold mb-3">Our Story</h2>
      <p class="text-muted">Kraftrix started with a simple vision – to bring the beauty of handmade crafts to the world. 
      Every product tells the story of an artisan’s dedication, patience, and love for tradition. 
      We work closely with skilled craftspeople across India to curate unique, sustainable, and beautiful creations for your home and lifestyle.</p>
    </div>
  </div>
</section>

<!-- Our Mission -->
<section class="container py-5">
  <div class="text-center mb-4">
    <h2 class="fw-bold">Our Mission & Values</h2>
    <p class="text-muted">What drives us at Kraftrix</p>
  </div>
  <div class="row g-4 text-center">
    <div class="col-md-4">
      <div class="mission-card shadow-sm">
        <i class="bi bi-heart-fill fs-1 text-danger"></i>
        <h5 class="mt-3">Promote Handicrafts</h5>
        <p>We empower artisans by giving their handmade creations a global platform.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="mission-card shadow-sm">
        <i class="bi bi-globe2 fs-1 text-success"></i>
        <h5 class="mt-3">Sustainability</h5>
        <p>Eco-friendly practices and sustainable products are at the heart of what we do.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="mission-card shadow-sm">
        <i class="bi bi-people-fill fs-1 text-primary"></i>
        <h5 class="mt-3">Community First</h5>
        <p>We build strong relationships with artisans and customers, creating a family-like community.</p>
      </div>
    </div>
  </div>
</section>

<!-- Meet Our Team -->
<section class="container py-5">
  <div class="text-center mb-4">
    <h2 class="fw-bold">Meet Our Team</h2>
    <p class="text-muted">The people behind Kraftrix</p>
  </div>
  <div class="row g-4 text-center">
    <div class="col-md-3">
      <div class="team-card">
        <img src="images/team1.jpg" alt="Team Member">
        <h6 class="fw-bold mt-3">Aarav Sharma</h6>
        <p class="text-muted">Founder</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="team-card">
        <img src="images/team2.jpg" alt="Team Member">
        <h6 class="fw-bold mt-3">Priya Verma</h6>
        <p class="text-muted">Creative Head</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="team-card">
        <img src="images/team3.jpg" alt="Team Member">
        <h6 class="fw-bold mt-3">Rohan Patel</h6>
        <p class="text-muted">Marketing Lead</p>
      </div>
    </div>
    <div class="col-md-3">
      <div class="team-card">
        <img src="images/team4.jpg" alt="Team Member">
        <h6 class="fw-bold mt-3">Simran Kaur</h6>
        <p class="text-muted">Operations</p>
      </div>
    </div>
  </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-primary text-white text-center">
  <div class="container">
    <h2 class="fw-bold">Join Our Journey</h2>
    <p>Support artisans, shop handmade, and be part of the Kraftrix family.</p>
    <a href="shop.php" class="btn btn-light">Explore Our Products</a>
  </div>
</section>

<!-- Include Footer -->
<?php include("footer.php"); ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
