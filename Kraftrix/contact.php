<?php
// contact.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Us | Kraftrix</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
    .contact-hero {
      background: url('images/contact-hero.jpg') center/cover no-repeat;
      height: 50vh;
      color: white;
      display: flex;
      align-items: center;
      justify-content: center;
      text-align: center;
      position: relative;
    }
    .contact-hero::after {
      content: "";
      position: absolute;
      inset: 0;
      background: rgba(0,0,0,0.6);
    }
    .contact-hero-content {
      position: relative;
      z-index: 2;
    }
    .contact-card {
      border-radius: 12px;
      background: #f8f9fa;
      padding: 25px;
      height: 100%;
      transition: transform 0.3s;
    }
    .contact-card:hover {
      transform: translateY(-5px);
    }
    .contact-form input, .contact-form textarea {
      border-radius: 8px;
      box-shadow: none !important;
    }
  </style>
</head>
<body>

<!-- Include Header -->
<?php include("header.php"); ?>

<!-- Hero Section -->
<section class="contact-hero">
  <div class="contact-hero-content">
    <h1 class="display-4 fw-bold">Get in Touch</h1>
    <p class="lead">We’d love to hear from you</p>
  </div>
</section>

<!-- Contact Info -->
<section class="container py-5">
  <div class="row g-4 text-center">
    <div class="col-md-4">
      <div class="contact-card shadow-sm">
        <i class="bi bi-geo-alt-fill fs-1 text-primary"></i>
        <h5 class="mt-3">Our Address</h5>
        <p class="text-muted">123 Kraftrix , Rajkot , Gujrat , India</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="contact-card shadow-sm">
        <i class="bi bi-telephone-fill fs-1 text-success"></i>
        <h5 class="mt-3">Call Us</h5>
        <p class="text-muted">+91 98765 43210</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="contact-card shadow-sm">
        <i class="bi bi-envelope-fill fs-1 text-danger"></i>
        <h5 class="mt-3">Email</h5>
        <p class="text-muted">support@kraftrix.com</p>
      </div>
    </div>
  </div>
</section>

<!-- Contact Form -->
<section class="container py-5">
  <div class="row align-items-center">
    <div class="col-md-6 mb-4">
      <h2 class="fw-bold mb-3">Send Us a Message</h2>
      <p class="text-muted">Whether you have a question about our products, pricing, or anything else, our team is ready to answer all your questions.</p>
      <form class="contact-form">
        <div class="mb-3">
          <input type="text" class="form-control" placeholder="Your Name" required>
        </div>
        <div class="mb-3">
          <input type="email" class="form-control" placeholder="Your Email" required>
        </div>
        <div class="mb-3">
          <textarea class="form-control" rows="5" placeholder="Your Message" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary w-100">Send Message</button>
      </form>
    </div>
    <div class="col-md-6">
      <iframe class="w-100 rounded shadow" height="350" 
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3709.340575084838!2d70.78099327515868!3d22.30389437968864!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3959ca26a846aaab%3A0x6f8e8b2d8db39d!2sRajkot%2C%20Gujarat%2C%20India!5e0!3m2!1sen!2sin!4v1726060000000"        allowfullscreen="" loading="lazy"></iframe>
    </div>
  </div>
</section>

<!-- Call to Action -->
<section class="py-5 bg-primary text-white text-center">
  <div class="container">
    <h2 class="fw-bold">Let’s Stay Connected</h2>
    <p>Follow us on social media for the latest updates & new arrivals.</p>
    <div class="d-flex justify-content-center gap-3">
      <a href="#" class="text-white fs-3"><i class="bi bi-facebook"></i></a>
      <a href="#" class="text-white fs-3"><i class="bi bi-instagram"></i></a>
      <a href="#" class="text-white fs-3"><i class="bi bi-twitter-x"></i></a>
      <a href="#" class="text-white fs-3"><i class="bi bi-youtube"></i></a>
    </div>
  </div>
</section>

<!-- Include Footer -->
<?php include("footer.php"); ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
