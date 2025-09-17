<?php
session_start();
include("db.php");

// ✅ Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    die("Access denied. Please log in as admin.");
}

// ✅ Get the user ID from URL
$user_id = $_GET['id'] ?? null;
if (!$user_id) {
    die("Invalid user ID.");
}

// ✅ Check if user exists
$stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    die("User not found.");
}

// ✅ Delete related data first
$conn->query("DELETE FROM wishlist WHERE user_id = $user_id");
$conn->query("DELETE FROM cart WHERE user_id = $user_id");

// Delete orders and order items
$orders = $conn->query("SELECT order_id FROM orders WHERE user_id = $user_id");
while ($order = $orders->fetch_assoc()) {
    $order_id = $order['order_id'];
    $conn->query("DELETE FROM order_items WHERE order_id = $order_id");
}
$conn->query("DELETE FROM orders WHERE user_id = $user_id");

// ✅ Delete the user
$stmt2 = $conn->prepare("DELETE FROM users WHERE user_id = ?");
$stmt2->bind_param("i", $user_id);

if ($stmt2->execute()) {
    // Redirect back to users page with a message
    header("Location: users.php?msg=deleted");
    exit;
} else {
    die("Error deleting user: " . $conn->error);
}
