<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require 'db_connect.php';

if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit;
}
$order_id = intval($_GET['order_id']);

$orderRes = mysqli_query($conn, "SELECT * FROM orders WHERE order_id=$order_id");
$order = mysqli_fetch_assoc($orderRes);
if (!$order) {
    die("<h2 style='text-align:center;margin-top:40px;'>Order not found.</h2>");
}

$itemsRes = mysqli_query(
    $conn,
    "SELECT oi.*, p.product_name 
     FROM order_items oi 
     JOIN products p ON oi.product_id = p.product_id
     WHERE oi.order_id=$order_id"
);
?>

<?php include 'header.php'; ?>
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

<style>
.success-wrap{
  max-width:900px;
  margin:40px auto 70px;
  text-align:center;
  position:relative;
}
.success-card{
  background:#fff;
  padding:30px 28px 26px;
  border-radius:24px;
  box-shadow:0 12px 30px rgba(0,0,0,0.08);
}
.check-circle{
  width:72px;height:72px;
  border-radius:50%;
  border:4px solid #4caf50;
  margin:0 auto 16px;
  display:flex;align-items:center;justify-content:center;
  font-size:36px;color:#4caf50;
  animation:pop .6s ease;
}
@keyframes pop{
  0%{transform:scale(0.3);opacity:0;}
  80%{transform:scale(1.1);opacity:1;}
  100%{transform:scale(1);}
}
.success-title{
  font-size:26px;
  font-weight:700;
  color:#ff6f91;
  margin-bottom:6px;
}
.success-sub{
  font-size:14px;
  color:#777;
  margin-bottom:18px;
}
.order-meta{
  display:flex;
  justify-content:center;
  gap:24px;
  font-size:14px;
  color:#555;
  margin-bottom:18px;
  flex-wrap:wrap;
}
.items-table{
  width:100%;
  border-collapse:collapse;
  margin-top:10px;
  font-size:14px;
}
.items-table th,
.items-table td{
  padding:8px 6px;
  border-bottom:1px solid #f0e1ea;
}
.items-table th{
  background:#fff4f8;
  color:#ff6f91;
}
.success-total{
  text-align:right;
  margin-top:10px;
  font-weight:700;
}
.success-actions{
  margin-top:24px;
  display:flex;
  justify-content:center;
  gap:14px;
  flex-wrap:wrap;
}
.success-btn{
  padding:10px 18px;
  border-radius:24px;
  border:none;
  background:#ff6f91;
  color:#fff;
  text-decoration:none;
  font-size:14px;
  font-weight:600;
  box-shadow:0 8px 22px rgba(255,111,145,0.4);
  transition:.25s;
}
.success-btn:hover{background:#ff4d7a;transform:translateY(-2px);}
.success-btn.secondary{
  background:#fff;
  color:#ff6f91;
  border:1px solid #ffd1e1;
  box-shadow:none;
}

/* simple confetti */
.confetti{
  position:fixed;
  top:0;left:0;
  width:100%;height:0;
  pointer-events:none;
  overflow:visible;
}
.confetti-piece{
  position:absolute;
  width:8px;height:14px;
  background:#ff6f91;
  opacity:0.9;
  animation:fall 2.7s linear forwards;
}
@keyframes fall{
  0%{transform:translateY(-20px) rotateZ(0deg);}
  100%{transform:translateY(110vh) rotateZ(360deg);opacity:0;}
}
</style>

<div class="success-wrap" data-aos="zoom-in">

  <div class="confetti" id="confetti"></div>

  <div class="success-card">
    <div class="check-circle">✓</div>
    <div class="success-title">Order Confirmed!</div>
    <div class="success-sub">
      Thank you, <?= htmlspecialchars($order['customer_name']); ?>.
      Your order has been placed successfully.
    </div>

    <div class="order-meta">
      <div><strong>Order #</strong> <?= $order_id; ?></div>
      <div><strong>Payment:</strong> <?= htmlspecialchars(strtoupper($order['payment_method'])); ?></div>
      <?php if (!empty($order['order_date'] ?? '')): ?>
        <div><strong>Date:</strong> <?= htmlspecialchars($order['order_date']); ?></div>
      <?php endif; ?>
    </div>

    <table class="items-table">
      <thead>
        <tr>
          <th style="text-align:left;">Item</th>
          <th>Qty</th>
          <th>Price</th>
          <th>Subtotal</th>
        </tr>
      </thead>
      <tbody>
      <?php while($it = mysqli_fetch_assoc($itemsRes)): ?>
        <tr>
          <td style="text-align:left;"><?= htmlspecialchars($it['product_name']); ?></td>
          <td><?= $it['quantity']; ?></td>
          <td>₹<?= number_format($it['price'],2); ?></td>
          <td>₹<?= number_format($it['subtotal'],2); ?></td>
        </tr>
      <?php endwhile; ?>
      </tbody>
    </table>

    <div class="success-total">
      Total Paid: ₹<?= number_format($order['total_amount'], 2); ?>
    </div>

    <div class="success-actions">
      <a href="account.php" class="success-btn">View in My Account</a>
      <a href="products.php" class="success-btn secondary">Continue Shopping →</a>
    </div>

  </div>
</div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
AOS.init({duration:800, once:true});

// simple confetti generator
(function makeConfetti(){
  const container = document.getElementById('confetti');
  const colors = ['#ff6f91','#ff9a9e','#ffd1e1','#ffc107','#4caf50','#03a9f4'];

  for(let i=0;i<80;i++){
    const piece = document.createElement('div');
    piece.className = 'confetti-piece';
    piece.style.left = Math.random()*100 + 'vw';
    piece.style.background = colors[Math.floor(Math.random()*colors.length)];
    piece.style.animationDelay = (Math.random()*1.5) + 's';
    piece.style.animationDuration = (2 + Math.random()*1.5) + 's';
    container.appendChild(piece);
  }
})();
</script>

<?php include 'footer.php'; ?>
