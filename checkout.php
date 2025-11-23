<?php
// ---------- SERVER LOGIC ----------
if (session_status() === PHP_SESSION_NONE) session_start();
require 'db_connect.php';

// If cart empty → back to cart
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    header("Location: cart.php?msg=empty");
    exit;
}

$uid = $_SESSION['user_id'] ?? null;

// Build cart items & total from DB
$cart_items = [];
$total = 0;

$ids = array_keys($_SESSION['cart']);
if (!empty($ids)) {
    $id_list = implode(',', array_map('intval', $ids));
    $res = mysqli_query($conn, "SELECT * FROM products WHERE product_id IN ($id_list)");

    while ($p = mysqli_fetch_assoc($res)) {
        $pid = $p['product_id'];
        $qty = $_SESSION['cart'][$pid]['quantity'] ?? 1;

        $subtotal = $p['price'] * $qty;
        $total += $subtotal;

        $cart_items[] = [
            'id'       => $pid,
            'name'     => $p['product_name'],
            'qty'      => $qty,
            'price'    => $p['price'],
            'stock'    => $p['stock'],
            'subtotal' => $subtotal,
        ];
    }
}

// If somehow we fail to load any product
if (empty($cart_items)) {
    header("Location: cart.php?msg=invalid_cart");
    exit;
}

$error = "";

// ---------- HANDLE CHECKOUT SUBMIT ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Trim & escape
    $name    = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
    $email   = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $phone   = mysqli_real_escape_string($conn, trim($_POST['phone'] ?? ''));
    $address = mysqli_real_escape_string($conn, trim($_POST['address'] ?? ''));
    $city    = mysqli_real_escape_string($conn, trim($_POST['city'] ?? ''));
    $payment = mysqli_real_escape_string($conn, trim($_POST['payment'] ?? ''));

    // -------- BACKEND VALIDATIONS --------

    // Required fields
    if ($name === '' || $email === '' || $phone === '' || $address === '' || $city === '' || $payment === '') {
        $error = "Please fill in all required fields.";
    }
    // Email format
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    }
    // Phone must be 10 digits
    elseif (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "Phone number must be 10 digits.";
    }
    // Payment-specific checks
    else {
        if ($payment === 'upi') {
            $upi_raw = trim($_POST['upi_id'] ?? '');
            if ($upi_raw === '') {
                $error = "Please enter your UPI ID.";
            } elseif (strpos($upi_raw, '@') === false) {
                $error = "Please enter a valid UPI ID (e.g., name@bank).";
            }
        } elseif ($payment === 'card') {
            $card_last4_raw = trim($_POST['card_last4'] ?? '');
            if ($card_last4_raw === '' || !preg_match('/^[0-9]{4}$/', $card_last4_raw)) {
                $error = "Please enter a valid card number so we can capture the last 4 digits.";
            }
        }
    }

    // (Optional) attach extra payment info into address so it’s saved without DB change
    $payment_note = "";
    if ($payment === 'upi' && !empty($_POST['upi_id'])) {
        $upi_id = mysqli_real_escape_string($conn, $_POST['upi_id']);
        $payment_note = "\nPayment UPI: " . $upi_id;
    } elseif ($payment === 'card' && !empty($_POST['card_last4'])) {
        $card_last4 = mysqli_real_escape_string($conn, $_POST['card_last4']);
        $payment_note = "\nCard last 4: " . $card_last4;
    }
    $address_full = $address . $payment_note;

    // Only continue with DB operations if NO validation error
    if ($error === "") {

        mysqli_begin_transaction($conn);

        try {
            // 1) Check stock with SELECT ... FOR UPDATE
            foreach ($cart_items as $item) {
                $pid = $item['id'];
                $needed = $item['qty'];

                $sRes = mysqli_query($conn, "SELECT stock FROM products WHERE product_id=$pid FOR UPDATE");
                $sRow = mysqli_fetch_assoc($sRes);

                if ($sRow === null || $sRow['stock'] < $needed) {
                    mysqli_rollback($conn);
                    $available = $sRow['stock'] ?? 0;
                    $error = "Only $available left for {$item['name']}. Please update your cart.";
                    break;
                }
            }

            if ($error === "") {
                // 2) Insert order (with user_id if logged in)
                $uid_sql = is_null($uid) ? "NULL" : intval($uid);

                $order_sql = "
                    INSERT INTO orders (
                        user_id, customer_name, email, phone, address, city,
                        payment_method, total_amount
                    ) VALUES (
                        $uid_sql,
                        '$name', '$email', '$phone', '$address_full', '$city',
                        '$payment', $total
                    )
                ";
                mysqli_query($conn, $order_sql);
                $order_id = mysqli_insert_id($conn);

                // 3) Insert order_items and decrease stock
                foreach ($cart_items as $item) {
                    $pid      = $item['id'];
                    $qty      = $item['qty'];
                    $price    = $item['price'];
                    $subtotal = $item['subtotal'];

                    mysqli_query(
                        $conn,
                        "INSERT INTO order_items (order_id, product_id, quantity, price, subtotal)
                         VALUES ($order_id, $pid, $qty, $price, $subtotal)"
                    );

                    mysqli_query(
                        $conn,
                        "UPDATE products SET stock = stock - $qty WHERE product_id=$pid"
                    );
                }

                mysqli_commit($conn);

                // 4) Clear cart and go to success page
                unset($_SESSION['cart']);
                header("Location: order_success.php?order_id=$order_id");
                exit;
            }

        } catch (Exception $e) {
            mysqli_rollback($conn);
            $error = "Something went wrong during checkout. Please try again.";
        }
    }
}
?>

<?php include 'header.php'; ?>
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<style>
.checkout-page{
  max-width:1100px;
  margin:30px auto 60px;
  display:grid;
  grid-template-columns:2fr 1fr;
  gap:30px;
}
.checkout-card{
  background:#fff;
  border-radius:20px;
  padding:25px 28px;
  box-shadow:0 10px 25px rgba(0,0,0,0.07);
}
.checkout-title{
  font-size:24px;
  font-weight:700;
  color:#ff6f91;
  margin-bottom:4px;
}
.checkout-sub{
  font-size:14px;
  color:#777;
  margin-bottom:18px;
}
.checkout-card label{
  font-weight:600;
  font-size:14px;
  margin-top:6px;
  display:block;
}
.checkout-card input,
.checkout-card textarea{
  width:100%;
  padding:10px 12px;
  border-radius:12px;
  border:1px solid #eee;
  margin-top:4px;
  margin-bottom:10px;
  font-family:'Poppins',sans-serif;
  font-size:14px;
}
.checkout-card textarea{resize:vertical;min-height:70px;}

.payment-options{
  display:flex;
  flex-direction:column;
  gap:8px;
  margin-top:8px;
}
.payment-pill{
  display:flex;
  align-items:center;
  gap:10px;
  padding:10px 14px;
  border-radius:14px;
  border:2px solid #ffe3eb;
  cursor:pointer;
  transition:.25s;
  background:#fff;
}
.payment-pill:hover{
  border-color:#ff6f91;
  background:#fff7fb;
}
.payment-pill input{accent-color:#ff6f91;}
.payment-pill span{font-size:14px;}

.payment-extra{display:none;margin-top:10px;animation:fadeIn .4s ease;}
@keyframes fadeIn{
  from{opacity:0;transform:translateY(5px);}
  to{opacity:1;transform:translateY(0);}
}

.place-btn{
  width:100%;
  padding:14px 18px;
  border-radius:28px;
  border:none;
  background:#ff6f91;
  color:#fff;
  font-weight:600;
  font-size:16px;
  cursor:pointer;
  margin-top:8px;
  box-shadow:0 10px 24px rgba(255,111,145,0.45);
  transition:.25s;
}
.place-btn:hover{
  transform:translateY(-2px);
  background:#ff4d7a;
}

/* Order summary */
.summary-list{list-style:none;padding:0;margin:0;}
.summary-item{
  display:flex;
  justify-content:space-between;
  font-size:14px;
  padding:6px 0;
  border-bottom:1px solid #f3e0ea;
}
.summary-item span:last-child{font-weight:600;}
.summary-total{
  display:flex;
  justify-content:space-between;
  font-weight:700;
  font-size:16px;
  margin-top:10px;
}
.summary-note{
  font-size:13px;
  color:#777;
  margin-top:10px;
}
.error-box{
  background:#ffe3eb;
  color:#b1002b;
  padding:10px 12px;
  border-radius:10px;
  font-size:14px;
  margin-bottom:12px;
}
@media(max-width:900px){
  .checkout-page{grid-template-columns:1fr;}
}
</style>

<div class="checkout-page" data-aos="fade-up">

  <!-- LEFT: FORM -->
  <div class="checkout-card">
    <div class="checkout-title">Secure Checkout 🛍️</div>
    <div class="checkout-sub">Fill in your details and choose your payment method.</div>

    <?php if ($error): ?>
      <div class="error-box"><?= htmlspecialchars($error); ?></div>
    <?php endif; ?>

    <form method="POST" id="checkoutForm">
      <label>Full Name</label>
      <input type="text" name="name" required
             value="<?= isset($_POST['name']) ? htmlspecialchars($_POST['name']) : ($_SESSION['name'] ?? '') ?>">

      <label>Email</label>
      <input type="email" name="email" required
             value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">

      <label>Phone</label>
      <input type="text" name="phone" required
             value="<?= isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : '' ?>">

      <label>Address</label>
      <textarea name="address" required><?= isset($_POST['address']) ? htmlspecialchars($_POST['address']) : '' ?></textarea>

      <label>City</label>
      <input type="text" name="city" required
             value="<?= isset($_POST['city']) ? htmlspecialchars($_POST['city']) : '' ?>">

      <label>Payment Method</label>
      <div class="payment-options">
        <label class="payment-pill">
          <input type="radio" name="payment" value="cod"
                 <?= (!isset($_POST['payment']) || $_POST['payment']==='cod') ? 'checked' : '' ?>>
          <span>💵 Cash on Delivery</span>
        </label>

        <label class="payment-pill">
          <input type="radio" name="payment" value="upi"
                 <?= (isset($_POST['payment']) && $_POST['payment']==='upi') ? 'checked' : '' ?>>
          <span>📱 UPI (GPay / PhonePe)</span>
        </label>

        <label class="payment-pill">
          <input type="radio" name="payment" value="card"
                 <?= (isset($_POST['payment']) && $_POST['payment']==='card') ? 'checked' : '' ?>>
          <span>💳 Credit / Debit Card</span>
        </label>
      </div>

      <!-- extra: UPI -->
      <div id="upi-extra" class="payment-extra">
        <label>UPI ID</label>
        <input type="text" name="upi_id"
               placeholder="example@upi"
               value="<?= isset($_POST['upi_id']) ? htmlspecialchars($_POST['upi_id']) : '' ?>">
      </div>

      <!-- extra: Card -->
      <div id="card-extra" class="payment-extra">
        <label>Card Number</label>
        <input type="text" maxlength="19" placeholder="xxxx xxxx xxxx xxxx" id="card_number">
        <label>Expiry</label>
        <input type="text" maxlength="5" placeholder="MM/YY" id="card_expiry">
        <label>CVV</label>
        <input type="password" maxlength="3" placeholder="***" id="card_cvv">
        <!-- only store last 4 digits text for reference -->
        <input type="hidden" name="card_last4" id="card_last4">
      </div>

      <button type="submit" class="place-btn">Place Order →</button>
    </form>
  </div>

  <!-- RIGHT: ORDER SUMMARY -->
  <div class="checkout-card" data-aos="fade-left">
    <div class="checkout-title">Order Summary</div>
    <div class="checkout-sub">Review items in your cart before confirming.</div>

    <ul class="summary-list">
      <?php foreach ($cart_items as $item): ?>
        <li class="summary-item">
          <span><?= htmlspecialchars($item['name']); ?> (x<?= $item['qty']; ?>)</span>
          <span>₹<?= number_format($item['subtotal'], 2); ?></span>
        </li>
      <?php endforeach; ?>
    </ul>

    <div class="summary-total">
      <span>Total</span>
      <span>₹<?= number_format($total, 2); ?></span>
    </div>
    <div class="summary-note">
      🔒 Your details are used only to process this order.
    </div>
  </div>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
AOS.init({ duration:800, once:true });

function updatePaymentExtras() {
  const method = document.querySelector('input[name="payment"]:checked').value;
  const upiDiv = document.getElementById('upi-extra');
  const cardDiv = document.getElementById('card-extra');

  upiDiv.style.display = (method === 'upi') ? 'block' : 'none';
  cardDiv.style.display = (method === 'card') ? 'block' : 'none';
}
document.querySelectorAll('input[name="payment"]').forEach(r =>
  r.addEventListener('change', updatePaymentExtras)
);
updatePaymentExtras();

// Simple way to grab last 4 digits of any card input (front-end only)
const cardExtra = document.getElementById('card-extra');
const cardNumberInput = document.getElementById('card_number');
const cardExpiryInput = document.getElementById('card_expiry');
const cardCvvInput = document.getElementById('card_cvv');

cardExtra.addEventListener('input', function(e){
  if (e.target === cardNumberInput) {
    let digits = e.target.value.replace(/\D/g,'');
    // format as xxxx xxxx xxxx xxxx
    e.target.value = digits.replace(/(.{4})/g,'$1 ').trim();
    const last4 = digits.slice(-4);
    document.getElementById('card_last4').value = last4;
  }
});

// -------- FRONTEND VALIDATION --------
document.getElementById('checkoutForm').addEventListener('submit', function(e){
  const form = this;

  const name  = form.name.value.trim();
  const email = form.email.value.trim();
  const phone = form.phone.value.trim();
  const addr  = form.address.value.trim();
  const city  = form.city.value.trim();
  const payment = (form.payment.value || '').trim();

  if (!name || !email || !phone || !addr || !city || !payment) {
    alert("Please fill in all required fields.");
    e.preventDefault();
    return;
  }

  // simple email pattern
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailPattern.test(email)) {
    alert("Please enter a valid email address.");
    e.preventDefault();
    return;
  }

  // phone: 10 digits
  if (!/^[0-9]{10}$/.test(phone)) {
    alert("Phone number must be 10 digits.");
    e.preventDefault();
    return;
  }

  if (payment === 'upi') {
    const upiField = form.upi_id.value.trim();
    if (!upiField) {
      alert("Please enter your UPI ID.");
      e.preventDefault();
      return;
    }
    if (upiField.indexOf('@') === -1) {
      alert("Please enter a valid UPI ID (e.g., name@bank).");
      e.preventDefault();
      return;
    }
  }

  if (payment === 'card') {
    const rawCard = cardNumberInput.value.replace(/\s/g,'');
    const expiry  = cardExpiryInput.value.trim();
    const cvv     = cardCvvInput.value.trim();

    if (rawCard.length !== 16 || !/^[0-9]{16}$/.test(rawCard)) {
      alert("Card number must be 16 digits.");
      e.preventDefault();
      return;
    }
    if (!/^[0-9]{2}\/[0-9]{2}$/.test(expiry)) {
      alert("Expiry must be in MM/YY format.");
      e.preventDefault();
      return;
    }
    if (!/^[0-9]{3}$/.test(cvv)) {
      alert("CVV must be 3 digits.");
      e.preventDefault();
      return;
    }
  }
});
</script>

<?php include 'footer.php'; ?>
