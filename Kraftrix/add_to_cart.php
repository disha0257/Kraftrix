<?php
session_start();
require_once 'admin/db.php';

// Ensure cart exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Get product id from POST
if (isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);

    // Fetch product from DB
    $stmt = $conn->prepare("SELECT id, name, price, image FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // If already in cart → increase qty
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += 1;
        } else {
            // Add new product to cart
            $_SESSION['cart'][$product_id] = [
                'name' => $row['name'],
                'price' => $row['price'],
                'image' => $row['image'], // ✅ must only be "filename.jpg"
                'quantity' => 1
            ];
        }
    }
}

// Redirect back
header("Location: cart.php");
exit;
