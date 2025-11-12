<?php
include 'db_connect.php';
include 'header.php';

$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Fetch order details
$order = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM orders WHERE order_id = $order_id"));
$order_items = mysqli_query($conn, "SELECT oi.*, p.product_name 
                                    FROM order_items oi 
                                    JOIN products p ON oi.product_id = p.product_id 
                                    WHERE oi.order_id = $order_id");

if (!$order) {
    die("<h2 style='text-align:center; color:#ff4c8b; margin-top:80px;'>❌ Invalid Order ID</h2>");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Success | Hobbyverse</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <style>
    :root {
      --pink: #ff4c8b;
      --light-pink: #ffe6ef;
      --bg: #fff7fa;
    }

    body {
      margin: 0;
      font-family: 'Poppins', sans-serif;
      background: var(--bg);
      color: #333;
      overflow-x: hidden;
    }

    .container {
      max-width: 800px;
      margin: 60px auto;
      background: #fff;
      padding: 40px;
      border-radius: 20px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.08);
      text-align: center;
      position: relative;
      overflow: hidden;
    }

    .success-icon {
      font-size: 80px;
      color: var(--pink);
      margin-bottom: 15px;
      animation: pop 0.8s ease forwards;
    }

    @keyframes pop {
      0% { transform: scale(0); opacity: 0; }
      80% { transform: scale(1.2); opacity: 1; }
      100% { transform: scale(1); }
    }

    h1 {
      font-size: 36px;
      color: var(--pink);
      margin-bottom: 10px;
    }

    p {
      font-size: 16px;
      color: #555;
      margin-bottom: 25px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 25px;
    }

    th, td {
      padding: 12px;
      text-align: center;
    }

    th {
      background: var(--light-pink);
      color: #333;
    }

    tr:nth-child(even) {
      background: #fff6f9;
    }

    .total {
      font-weight: 700;
      color: var(--pink);
      margin-top: 15px;
    }

    .btn {
      display: inline-block;
      background: var(--pink);
      color: #fff;
      padding: 12px 24px;
      border-radius: 25px;
      text-decoration: none;
      margin-top: 30px;
      transition: 0.3s;
      font-weight: 600;
    }

    .btn:hover {
      background: #e63972;
      transform: scale(1.05);
    }

    .sparkle {
      position: absolute;
      width: 100%;
      height: 100%;
      top: 0;
      left: 0;
      pointer-events: none;
    }
  </style>
</head>
<body>

<div class="container" data-aos="fade-up">
  <div class="success-icon">🎉</div>
  <h1>Order Placed Successfully!</h1>
  <p>Thank you for shopping with <strong>Hobbyverse</strong> ✨<br>
     Your order has been received and is being processed.</p>

  <h3>🧾 Order Summary (ID: <?php echo $order['order_id']; ?>)</h3>
  <table>
    <tr>
      <th>Product</th>
      <th>Qty</th>
      <th>Price</th>
      <th>Subtotal</th>
    </tr>
    <?php while ($item = mysqli_fetch_assoc($order_items)): ?>
    <tr>
      <td><?php echo htmlspecialchars($item['product_name']); ?></td>
      <td><?php echo $item['quantity']; ?></td>
      <td>₹<?php echo number_format($item['price'], 2); ?></td>
      <td>₹<?php echo number_format($item['subtotal'], 2); ?></td>
    </tr>
    <?php endwhile; ?>
  </table>

  <div class="total">Total Amount: ₹<?php echo number_format($order['total_amount'], 2); ?></div>

  <a href="products.php" class="btn">Continue Shopping →</a>
</div>

<div class="sparkle"></div>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
AOS.init({ duration: 1000, once: true });

// 🎊 Confetti celebration
setTimeout(() => {
  const duration = 2 * 1000;
  const end = Date.now() + duration;
  (function frame() {
    confetti({
      particleCount: 5,
      startVelocity: 30,
      spread: 360,
      ticks: 60,
      origin: { x: Math.random(), y: Math.random() - 0.2 }
    });
    if (Date.now() < end) requestAnimationFrame(frame);
  })();
}, 400);
</script>

</body>
</html>
<?php include 'footer.php'; ?>
