<?php
session_start();
include 'admin/db.php';

$user_id = $_SESSION['user_id'] ?? 1;
$cart = $_SESSION['cart'] ?? [];

if(empty($cart)){
    header("Location: cart.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] === 'POST'){

    $fullname = $_POST['fullname'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $payment_method = $_POST['payment_method'];

    $total = 0;
    foreach($cart as $item){
        $total += $item['price'] * $item['quantity'];
    }

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_price, status, fullname, email, phone, address, payment_method) VALUES (?, ?, 'pending', ?, ?, ?, ?, ?)");
    $stmt->bind_param("idsssss", $user_id, $total, $fullname, $email, $phone, $address, $payment_method);
    $stmt->execute();

    $order_id = $stmt->insert_id;

    // Insert order items
    $stmt_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    foreach($cart as $item){
        $stmt_item->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
        $stmt_item->execute();
    }

    unset($_SESSION['cart']); // Clear cart

    // Redirect to success page
    header("Location: order_success.php?order_id=$order_id");
    exit;
}
?>
