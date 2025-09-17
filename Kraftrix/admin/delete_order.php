<?php
session_start();
include("db.php");

// Check if user is logged in (optional admin check)
if (!isset($_SESSION['user_id'])) {
    die("Access denied. Please log in.");
}

// Get the order ID from URL
$order_id = $_GET['id'] ?? null;

if (!$order_id) {
    die("Invalid order ID.");
}

// Optional: you can check if the logged-in user is admin
// if ($_SESSION['role'] !== 'admin') {
//     die("Access denied. Admins only.");
// }

// Prepare and execute delete query
$stmt = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
$stmt->bind_param("i", $order_id);

if ($stmt->execute()) {
    // Redirect back to orders page
    header("Location: manage_orders.php?msg=deleted");
    exit;
} else {
    die("Error deleting order: " . $conn->error);
}
