<?php
// -----------------------------------------
// START SESSION (must be first!)
if (session_status() === PHP_SESSION_NONE) session_start();

include 'db_connect.php';
include 'header.php';   // this prints HTML, so logic must be ABOVE this
// -----------------------------------------

/* -------------------------------
   INIT CART
---------------------------------*/
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

/* -------------------------------
   ADD TO CART
---------------------------------*/
if (isset($_GET['add'])) {
    $id = intval($_GET['add']);

    if (!isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] = ['quantity' => 1];
    } else {
        $_SESSION['cart'][$id]['quantity'] += 1;
    }

    header("Location: cart.php");
    exit;
}

/* -------------------------------
   REMOVE ITEM
---------------------------------*/
if (isset($_GET['remove'])) {
    unset($_SESSION['cart'][intval($_GET['remove'])]);
    header("Location: cart.php");
    exit;
}

/* -------------------------------
   CLEAR CART
---------------------------------*/
if (isset($_GET['clear'])) {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit;
}

/* -------------------------------
   UPDATE QUANTITY
---------------------------------*/
if (isset($_POST['update_qty'])) {
    foreach ($_POST['qty'] as $pid => $qty) {
        $qty = intval($qty);

        // Get stock from DB
        $check = mysqli_fetch_assoc(mysqli_query($conn, "SELECT stock FROM products WHERE product_id=$pid"));
        $stock = $check['stock'];

        // LIMIT qty to stock
        if ($qty > $stock) $qty = $stock;
        if ($qty < 1)     $qty = 1;

        $_SESSION['cart'][$pid]['quantity'] = $qty;
    }

    header("Location: cart.php");
    exit;
}

/* -------------------------------
   LOAD CART ITEMS
---------------------------------*/
$cart_items = [];
$total = 0;

if (!empty($_SESSION['cart'])) {
    $ids = implode(",", array_keys($_SESSION['cart']));
    $sql = "SELECT * FROM products WHERE product_id IN ($ids)";
    $q = mysqli_query($conn, $sql);

    while ($row = mysqli_fetch_assoc($q)) {
        $id  = $row['product_id'];
        $qty = $_SESSION['cart'][$id]['quantity'];

        // stock check
        $stock = $row['stock'];
        if ($qty > $stock) {
            $qty = $stock;
            $_SESSION['cart'][$id]['quantity'] = $stock;
        }

        $subtotal = $row['price'] * $qty;

        $cart_items[] = [
            'id'       => $id,
            'name'     => $row['product_name'],
            'price'    => $row['price'],
            'stock'    => $stock,
            'qty'      => $qty,
            'subtotal' => $subtotal
        ];

        $total += $subtotal;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Your Cart | Hobbyverse</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
<style>
body{font-family:'Poppins',sans-serif;margin:0;background:#fff7fa;color:#333;}
.container{max-width:950px;margin:40px auto;padding:20px;}
h1{text-align:center;color:#ff6f91;font-size:34px;margin-bottom:30px;}
table{width:100%;border-collapse:collapse;background:#fff;border-radius:18px;
      overflow:hidden;box-shadow:0 10px 22px rgba(0,0,0,.06);}
th,td{text-align:center;padding:15px;}
th{background:#ffe3eb;}
tr:nth-child(even){background:#fff9fb;}
.qty-input{width:55px;padding:6px;text-align:center;border-radius:6px;border:1px solid #ccc;}
.small{font-size:12px;color:#e63946;margin-top:4px;}
.btn{padding:10px 20px;border:none;border-radius:25px;font-weight:600;cursor:pointer;transition:.3s;}
.btn-update{background:#ff6f91;color:#fff;}
.btn-update:hover{background:#ff4d7a;}
.btn-remove{background:#ffe3eb;}
.btn-remove:hover{background:#ffc1cf;}
.btn-clear{background:#ccc;}
.btn-checkout{background:#ff6f91;color:#fff;margin-top:20px;}
.btn-checkout:hover{background:#ff4d7a;}
.total{text-align:right;font-size:22px;font-weight:600;margin-top:20px;}
.empty{text-align:center;font-size:20px;color:#777;margin-top:50px;}
</style>
</head>

<body>
<div class="container">
<h1>Your Shopping Cart 🛒</h1>

<?php if (empty($cart_items)): ?>
    <div class="empty">
        Your cart is empty 😔<br><br>
        <a href="products.php" class="btn btn-update">Browse Products</a>
    </div>

<?php else: ?>
<form method="POST">
<table>
    <tr>
        <th>Product</th>
        <th>Qty</th>
        <th>Price</th>
        <th>Subtotal</th>
        <th>Action</th>
    </tr>

    <?php foreach ($cart_items as $item): ?>
    <tr>

        <td><?= htmlspecialchars($item['name']) ?></td>

        <td>
            <input type="number"
                   min="1"
                   max="<?= $item['stock'] ?>"
                   class="qty-input"
                   name="qty[<?= $item['id'] ?>]"
                   value="<?= $item['qty'] ?>">

            <div class="small">
                <?= $item['stock'] ?> left
            </div>
        </td>

        <td>₹<?= number_format($item['price']) ?></td>
        <td>₹<?= number_format($item['subtotal']) ?></td>

        <td>
            <a class="btn btn-remove"
               href="cart.php?remove=<?= $item['id'] ?>">Remove</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<div style="margin-top:20px;">
    <button type="submit" name="update_qty" class="btn btn-update">Update Quantities</button>
    <a class="btn btn-clear" href="cart.php?clear=1">Clear Cart</a>
</div>
</form>

<div class="total">Total: ₹<?= number_format($total) ?></div>

<button onclick="location.href='checkout.php'" class="btn btn-checkout">
    Proceed to Checkout →
</button>

<?php endif; ?>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
