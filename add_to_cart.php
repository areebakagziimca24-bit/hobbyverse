<?php
session_start();
include 'db_connect.php';

if (!isset($_GET['id'])) {
    header("Location: products.php");
    exit;
}

$product_id = intval($_GET['id']);
$quantity = isset($_GET['qty']) ? intval($_GET['qty']) : 1;

// Fetch product to check if it exists
$result = mysqli_query($conn, "SELECT * FROM products WHERE product_id = $product_id");
$product = mysqli_fetch_assoc($result);

if (!$product) {
    header("Location: products.php?error=notfound");
    exit;
}

// Initialize cart session
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// If item already in cart, increase quantity
if (isset($_SESSION['cart'][$product_id])) {
    $_SESSION['cart'][$product_id]['quantity'] += $quantity;
} else {
    $_SESSION['cart'][$product_id] = [
        'quantity' => $quantity
    ];
}

// Redirect to cart or stay on same page
header("Location: cart.php?added=" . urlencode($product['product_name']));
exit;
?>
