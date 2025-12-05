<?php
session_start();
include 'db_connect.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$order_id = intval($_GET['order_id']);
$uid = intval($_SESSION['user_id']);

$order = mysqli_fetch_assoc(
    mysqli_query($conn, "SELECT * FROM orders WHERE order_id=$order_id AND user_id=$uid")
);

if (!$order) {
    echo "<h2 style='text-align:center;margin-top:50px;color:#ff4c8b;'>Order not found!</h2>";
    include 'footer.php';
    exit;
}

$items = mysqli_query($conn, "
    SELECT oi.*, p.product_name, p.image
    FROM order_items oi
    JOIN products p ON p.product_id = oi.product_id
    WHERE order_id=$order_id
");
?>
<style>
body {
    background: #fff2fa;
    font-family: 'Poppins', sans-serif;
}

/* CARD */
.order-wrapper {
    max-width: 920px;
    margin: 40px auto;
    background: #ffffff;
    border-radius: 18px;
    padding: 35px;
    box-shadow: 0 12px 40px rgba(0,0,0,0.08);
}

/* HEADER */
.order-title {
    font-size: 26px;
    font-weight: 600;
    text-align: center;
    margin-bottom: 8px;
    background: linear-gradient(90deg,#ff3f89,#ff74b2);
    -webkit-background-clip: text;
    color: transparent;
}

/* SUB DETAILS */
.order-info {
    text-align: center;
    font-size: 14px;
    margin-bottom: 25px;
    color: #444;
}

/* TABLE */
.order-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 18px;
}
.order-table th {
    background: #ffe3f0;
    color: #ff2e78;
    padding: 12px;
    font-size: 15px;
}
.order-table td {
    padding: 12px;
    border-bottom: 1px solid #f6d6e6;
    font-size: 14px;
}
.order-table img {
    width: 60px;
    border-radius: 10px;
}

/* TOTAL */
.total-box {
    margin-top: 18px;
    text-align: right;
    font-size: 20px;
    font-weight: 600;
    color: #ff2e78;
}

/* BUTTONS */
.btn-area {
    text-align: center;
    margin-top: 35px;
    display: flex;
    justify-content: center;
    gap: 18px;
}
.btn-order, .btn-home {
    background: #ff4c8b;
    color: white;
    padding: 12px 28px;
    border-radius: 30px;
    font-size: 15px;
    text-decoration: none;
    font-weight: 600;
    transition: .25s;
}
.btn-order:hover, .btn-home:hover {
    opacity: 0.85;
}
</style>

<div class="order-wrapper">
    <div class="order-title">🧾 Order Summary</div>

    <div class="order-info">
        <b>Order ID:</b> #<?= $order_id ?><br>
        <b>Date:</b> <?= date("d M Y", strtotime($order['created_at'])) ?><br>
        <b>Status:</b> <?= ucfirst($order['status']) ?>
    </div>

    <table class="order-table">
        <tr>
            <th>Product</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Subtotal</th>
        </tr>
        <?php while ($i = mysqli_fetch_assoc($items)) : ?>
        <tr>
            <td>
                <img src="<?= $i['image'] ?>"> 
                <?= $i['product_name'] ?>
            </td>
            <td><?= $i['quantity'] ?></td>
            <td>₹<?= number_format($i['price'], 2) ?></td>
            <td>₹<?= number_format($i['subtotal'], 2) ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <div class="total-box">
        Total Amount: ₹<?= number_format($order['total_amount'], 2) ?>
    </div>

    <div class="btn-area">
        <a href="invoice.php?order_id=<?= $order_id ?>" class="btn-order">🔻 Download Invoice</a>
        <a href="index.php" class="btn-home">🏠 Back to Home</a>
    </div>
</div>

<?php include 'footer.php'; ?>
