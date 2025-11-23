<?php
session_start();
include 'db_connect.php';
include 'header.php';

$order_id = intval($_GET['order_id']);
$uid = $_SESSION['user_id'];

$order = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM orders WHERE order_id=$order_id AND user_id=$uid")
);

if (!$order) die("<h2>Order not found!</h2>");

$items = mysqli_query($conn, "
    SELECT oi.*, p.product_name, p.image
    FROM order_items oi
    JOIN products p ON p.product_id = oi.product_id
    WHERE order_id=$order_id
");
?>
<div class="container" style="max-width:900px;margin:30px auto;">
  <h2>Order #<?= $order_id ?></h2>
  <p><b>Date:</b> <?= $order['created_at'] ?></p>
  <p><b>Status:</b> <?= $order['status'] ?></p>
  <p><b>Total:</b> ₹<?= number_format($order['total_amount']) ?></p>

  <h3>Items</h3>
  <table class="table">
    <tr><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th></tr>
    <?php while ($i = mysqli_fetch_assoc($items)) : ?>
      <tr>
        <td><?= $i['product_name'] ?></td>
        <td><?= $i['quantity'] ?></td>
        <td>₹<?= $i['price'] ?></td>
        <td>₹<?= $i['subtotal'] ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>
<?php include 'footer.php'; ?>
