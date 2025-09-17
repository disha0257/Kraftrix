<?php
session_start();
require_once 'admin/db.php';

// ✅ Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php?redirect=" . urlencode($_SERVER['HTTP_REFERER']));
    exit;
}

$user_id = $_SESSION['user_id'];

// ✅ Add to Wishlist
if (isset($_POST['add_to_wishlist'])) {
    $product_id = intval($_POST['product_id']);

    // check if already exists
    $stmt = $conn->prepare("SELECT * FROM wishlist WHERE user_id=? AND product_id=?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result->fetch_assoc()) {
        $insert = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?, ?)");
        $insert->bind_param("ii", $user_id, $product_id);
        $insert->execute();
    }

    header("Location: wishlist.php");
    exit;
}

// ✅ Remove from Wishlist
if (isset($_POST['remove_from_wishlist'])) {
    $wishlist_id = intval($_POST['wishlist_id']);
    $delete = $conn->prepare("DELETE FROM wishlist WHERE wishlist_id=? AND user_id=?");
    $delete->bind_param("ii", $wishlist_id, $user_id);
    $delete->execute();

    header("Location: wishlist.php");
    exit;
}
?>
