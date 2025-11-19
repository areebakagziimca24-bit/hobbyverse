<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    // redirect to login if not logged in
    header("Location: login.php?redirect=" . urlencode($_SERVER['HTTP_REFERER']));
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_GET['id']);

// Check if already in wishlist
$check = mysqli_query($conn, "SELECT id FROM wishlist WHERE user_id=$user_id AND product_id=$product_id");

if (mysqli_num_rows($check) > 0) {
    // REMOVE from wishlist
    mysqli_query($conn, "DELETE FROM wishlist WHERE user_id=$user_id AND product_id=$product_id");
} else {
    // ADD to wishlist
    mysqli_query($conn, "INSERT INTO wishlist (user_id, product_id) VALUES ($user_id, $product_id)");
}

// Redirect back
header("Location: " . $_SERVER['HTTP_REFERER']);
exit;
?>
