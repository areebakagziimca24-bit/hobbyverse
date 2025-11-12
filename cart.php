<?php
session_start();
include 'db_connect.php';
include 'header.php';

// Initialize cart if empty
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Remove item
if (isset($_GET['remove'])) {
    $id = intval($_GET['remove']);
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit;
}

// Clear all
if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit;
}

// Update quantity
if (isset($_POST['update_qty'])) {
    foreach ($_POST['qty'] as $id => $q) {
        $_SESSION['cart'][$id]['quantity'] = max(1, intval($q));
    }
    header("Location: cart.php");
    exit;
}

// Fetch products from DB based on cart IDs
$cart_items = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_map('intval', array_keys($_SESSION['cart'])));
    $result = mysqli_query($conn, "SELECT * FROM products WHERE product_id IN ($ids)");
    while ($row = mysqli_fetch_assoc($result)) {
        $id = $row['product_id'];
        $row['quantity'] = $_SESSION['cart'][$id]['quantity'];
        $row['subtotal'] = $row['price'] * $row['quantity'];
        $total += $row['subtotal'];
        $cart_items[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Hobbyverse | Your Cart</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <style>
    body {font-family:'Poppins',sans-serif;margin:0;background:#fff7fa;color:#333;}
    .container {max-width:950px;margin:50px auto;padding:20px;}
    h1{text-align:center;color:#ff6f91;font-size:36px;margin-bottom:30px;}
    table{width:100%;border-collapse:collapse;background:#fff;box-shadow:0 8px 20px rgba(0,0,0,0.05);border-radius:15px;overflow:hidden;}
    th,td{text-align:center;padding:15px;font-size:15px;}
    th{background:#ffe3eb;color:#333;}
    tr:nth-child(even){background:#fff9fb;}
    .qty-input{width:60px;text-align:center;padding:5px;border:1px solid #ccc;border-radius:5px;}
    .btn{padding:10px 20px;border:none;border-radius:25px;cursor:pointer;font-weight:600;transition:.3s;}
    .btn-update{background:#ff6f91;color:#fff;}
    .btn-update:hover{background:#ff4d7a;}
    .btn-remove{background:#ffe3eb;color:#333;}
    .btn-remove:hover{background:#ffc1cf;}
    .btn-clear{background:#ccc;color:#333;margin-top:15px;}
    .btn-checkout{background:#ff6f91;color:#fff;margin-top:25px;}
    .btn-checkout:hover{background:#ff4d7a;}
    .total{text-align:right;font-size:20px;font-weight:600;margin-top:20px;color:#222;}
    .empty{text-align:center;padding:80px;font-size:20px;color:#777;}
    .actions{text-align:center;margin-top:20px;}
    @media(max-width:768px){
      table,th,td{font-size:13px;}
      .qty-input{width:45px;}
    }
  </style>
</head>
<body>

<div class="container" data-aos="fade-up">
  <h1>Your Shopping Cart 🛒</h1>

  <?php if (empty($cart_items)): ?>
    <div class="empty" data-aos="fade-in">
      Your cart is empty 😔<br><br>
      <a href="products.php" class="btn btn-update">Browse Products</a>
    </div>
  <?php else: ?>
    <form method="POST" action="cart.php">
      <table data-aos="fade-up">
        <tr>
          <th>Product</th>
          <th>Price</th>
          <th>Quantity</th>
          <th>Subtotal</th>
          <th>Action</th>
        </tr>
        <?php foreach ($cart_items as $item): ?>
          <tr>
            <td><?php echo htmlspecialchars($item['product_name']); ?></td>
            <td>₹<?php echo number_format($item['price'],2); ?></td>
            <td>
              <input type="number" class="qty-input" name="qty[<?php echo $item['product_id']; ?>]" 
                     value="<?php echo $item['quantity']; ?>" min="1">
            </td>
            <td>₹<?php echo number_format($item['subtotal'],2); ?></td>
            <td>
              <a href="?remove=<?php echo $item['product_id']; ?>" class="btn btn-remove">Remove</a>
            </td>
          </tr>
        <?php endforeach; ?>
      </table>
      <div class="actions">
        <button type="submit" name="update_qty" class="btn btn-update">Update Quantities</button>
        <a href="?clear=1" class="btn btn-clear">Clear Cart</a>
      </div>
    </form>

    <div class="total" data-aos="fade-right">
      Total: ₹<?php echo number_format($total,2); ?>
    </div>

    <div class="actions" data-aos="zoom-in">
      <button class="btn btn-checkout" onclick="checkout()">Proceed to Checkout →</button>
    </div>
  <?php endif; ?>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({duration:800,once:true});</script>

<script>
function checkout(){
  <?php if (!isset($_SESSION['user_id'])): ?>
    window.location.href = "login.php?redirect=checkout.php";
  <?php else: ?>
    window.location.href = "checkout.php";
  <?php endif; ?>
}
</script>

</body>
</html>
<?php include 'footer.php'; ?>
