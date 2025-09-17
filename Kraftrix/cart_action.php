<?php
session_start();
require_once 'admin/db.php';

// ✅ Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['HTTP_REFERER']));
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Add to Cart
if (isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);

    // check if already in cart
    $stmt = $conn->prepare("SELECT quantity FROM cart WHERE user_id=? AND product_id=?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        // update quantity
        $newQty = $row['quantity'] + 1;
        $update = $conn->prepare("UPDATE cart SET quantity=? WHERE user_id=? AND product_id=?");
        $update->bind_param("iii", $newQty, $user_id, $product_id);
        $update->execute();
    } else {
        // insert new
        $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
        $insert->bind_param("ii", $user_id, $product_id);
        $insert->execute();
    }

    header("Location: cart.php");
    exit;
}

// ✅ Remove from Cart
if (isset($_POST['remove_from_cart'])) {
    $cart_id = intval($_POST['cart_id']);
    $delete = $conn->prepare("DELETE FROM cart WHERE cart_id=? AND user_id=?");
    $delete->bind_param("ii", $cart_id, $user_id);
    $delete->execute();

    header("Location: cart.php");
    exit;
}

// ✅ Update Quantity
if (isset($_POST['update_quantity'])) {
    $cart_id = intval($_POST['cart_id']);
    $qty     = max(1, intval($_POST['quantity']));

    $update = $conn->prepare("UPDATE cart SET quantity=? WHERE cart_id=? AND user_id=?");
    $update->bind_param("iii", $qty, $cart_id, $user_id);
    $update->execute();

    header("Location: cart.php");
    exit;
}
?>
