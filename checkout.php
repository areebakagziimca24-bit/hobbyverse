<?php
session_start();
include 'db_connect.php';

// ✅ Check if cart is empty
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    die("<h2 style='text-align:center; color:red;'>❌ Your cart is empty. Please add products first.</h2>");
}

// ✅ Calculate total & prepare cart items
$total = 0;
$cart_items = [];
foreach ($_SESSION['cart'] as $id => $cartItem) {
    $qty = $cartItem['quantity'] ?? 1;
    $product = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM products WHERE product_id=$id"));
    if ($product) {
        $subtotal = $product['price'] * $qty;
        $total += $subtotal;
        $cart_items[] = [
            'id' => $id,
            'name' => $product['product_name'],
            'qty' => $qty,
            'price' => $product['price'],
            'subtotal' => $subtotal
        ];
    }
}

// ✅ Handle checkout form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city = mysqli_real_escape_string($conn, $_POST['city']);
    $payment = mysqli_real_escape_string($conn, $_POST['payment']);

    $sql = "INSERT INTO orders (customer_name, email, phone, address, city, payment_method, total_amount) 
            VALUES ('$name', '$email', '$phone', '$address', '$city', '$payment', '$total')";
    if (mysqli_query($conn, $sql)) {
        $order_id = mysqli_insert_id($conn);
        foreach ($cart_items as $item) {
            $pid = $item['id'];
            $qty = $item['qty'];
            $price = $item['price'];
            $subtotal = $item['subtotal'];
            mysqli_query($conn, "INSERT INTO order_items (order_id, product_id, quantity, price, subtotal) 
                                 VALUES ($order_id, $pid, $qty, $price, $subtotal)");
        }
        unset($_SESSION['cart']);
        header("Location: order_success.php?order_id=$order_id");
        exit;
    } else {
        die("❌ Error placing order: " . mysqli_error($conn));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Checkout - Hobbyverse</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <style>
    body { margin:0; font-family:'Poppins',sans-serif; background:#fff7fa; color:#333; }
    nav { display:flex; justify-content:space-between; align-items:center; padding:20px 60px; background:#fff;
          box-shadow:0 2px 6px rgba(0,0,0,0.05); position:sticky; top:0; z-index:100; }
    .logo { font-weight:700; font-size:26px; color:#ff6f91; }
    nav a { margin-left:25px; text-decoration:none; color:#333; font-weight:500; transition:.3s; }
    nav a:hover { color:#ff6f91; }

    .header { text-align:center; padding:60px 20px; background:linear-gradient(180deg,#fff5f8,#fff);
              border-radius:0 0 60px 60px; box-shadow:0 8px 20px rgba(0,0,0,0.05); }
    .header h1 { color:#ff6f91; font-size:42px; margin-bottom:10px; }

    .checkout-container { max-width:1100px; margin:40px auto; display:grid; grid-template-columns:2fr 1fr; gap:40px; padding:0 20px; }
    .form-section, .summary-section { background:#fff; padding:30px; border-radius:18px; box-shadow:0 6px 16px rgba(0,0,0,0.06); }

    label { display:block; margin:10px 0 5px; font-weight:500; }
    input, textarea, select { width:100%; padding:12px; border:1px solid #ddd; border-radius:10px; margin-bottom:15px; font-family:'Poppins'; }

    textarea { resize:none; height:80px; }

    .payment-options label {
      display:flex; align-items:center; gap:10px;
      border:2px solid #ffe3eb; border-radius:14px;
      padding:10px 15px; cursor:pointer; transition:all .3s;
      margin-bottom:10px; background:#fff;
    }
    .payment-options label:hover { border-color:#ff6f91; background:#fff1f5; transform:scale(1.02); }
    .payment-options input[type="radio"] { accent-color:#ff6f91; }
    .payment-options img { width:30px; height:30px; object-fit:contain; }

    .payment-detail { display:none; animation:fadeIn .5s ease; }
    @keyframes fadeIn { from{opacity:0; transform:translateY(10px);} to{opacity:1; transform:translateY(0);} }

    .btn { padding:14px 28px; background:#ff6f91; color:#fff; border:none; border-radius:30px;
           cursor:pointer; transition:.3s; width:100%; font-weight:600; font-size:16px; }
    .btn:hover { background:#ff4d7a; transform:scale(1.05); }

    .summary-section ul { list-style:none; padding:0; }
    .summary-section li { padding:8px 0; border-bottom:1px solid #f0f0f0; }
    .summary-section h2 { color:#ff6f91; margin-bottom:10px; }
    .summary-section h3 { margin-top:15px; color:#333; }
    .secure-note { font-size:14px; color:#777; margin-top:10px; text-align:center; }
    .secure-note::before { content:"🔒 "; }
    @media(max-width:900px){ .checkout-container{grid-template-columns:1fr;} }
  </style>
</head>
<body>

<nav>
  <div class="logo">Hobbyverse ✨</div>
  <div>
    <a href="index.php">Home</a>
    <a href="products.php">Shop</a>
    <a href="cart.php">Cart</a>
    <a href="login.php">Account</a>
  </div>
</nav>

<div class="header" data-aos="fade-down">
  <h1>Secure Checkout 🛍️</h1>
  <p>Complete your order safely and easily</p>
</div>

<div class="checkout-container">

  <!-- 🧾 Checkout Form -->
  <div class="form-section" data-aos="fade-up">
    <form method="POST">
      <label>Full Name</label>
      <input type="text" name="name" required>

      <label>Email</label>
      <input type="email" name="email" required>

      <label>Phone</label>
      <input type="text" name="phone" required>

      <label>Address</label>
      <textarea name="address" required></textarea>

      <label>City</label>
      <input type="text" name="city" required>

      <label>Payment Method</label>
      <div class="payment-options">
        <label>
          <input type="radio" name="payment" value="cod" checked>
          <img src="https://cdn-icons-png.flaticon.com/512/633/633611.png"> Cash on Delivery
        </label>
        <label>
          <input type="radio" name="payment" value="upi">
          <img src="https://upload.wikimedia.org/wikipedia/commons/0/0c/UPI-Logo-vector.svg"> UPI (Google Pay / PhonePe)
        </label>
        <label>
          <input type="radio" name="payment" value="card">
          <img src="https://cdn-icons-png.flaticon.com/512/196/196561.png"> Credit / Debit Card
        </label>
      </div>

      <!-- Dynamic Fields -->
      <div id="upi-details" class="payment-detail">
        <label>UPI ID</label>
        <input type="text" name="upi_id" placeholder="example@upi">
      </div>

      <div id="card-details" class="payment-detail">
        <label>Card Number</label>
        <input type="text" name="card_number" maxlength="16" placeholder="xxxx xxxx xxxx xxxx">
        <label>Expiry Date</label>
        <input type="text" name="expiry" placeholder="MM/YY">
        <label>CVV</label>
        <input type="password" name="cvv" maxlength="3" placeholder="***">
      </div>

      <button type="submit" class="btn">Place Order →</button>
      <p class="secure-note">Your payment information is encrypted and secure.</p>
    </form>
  </div>

  <!-- 💰 Order Summary -->
  <div class="summary-section" data-aos="fade-left">
    <h2>Order Summary</h2>
    <ul>
      <?php foreach ($cart_items as $item): ?>
        <li><?php echo htmlspecialchars($item['name'])." (x".$item['qty'].") - ₹".number_format($item['subtotal'],2); ?></li>
      <?php endforeach; ?>
    </ul>
    <hr>
    <h3>Total: ₹<?php echo number_format($total,2); ?></h3>
  </div>

</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({duration:1000, once:true});</script>
<script>
  // 🎯 Show payment details dynamically
  document.querySelectorAll('input[name="payment"]').forEach(option => {
    option.addEventListener('change', function() {
      document.querySelectorAll('.payment-detail').forEach(div => div.style.display = 'none');
      const selected = document.getElementById(this.value + '-details');
      if (selected) selected.style.display = 'block';
    });
  });
</script>

</body>
</html>
